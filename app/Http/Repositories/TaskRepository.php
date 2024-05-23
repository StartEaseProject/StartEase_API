<?php

namespace App\Http\Repositories;

use App\Http\Interfaces\TaskInterface;
use App\Http\Requests\Task\CreateTaskRequest;
use App\Http\Requests\Task\SubmitTaskRequest;
use App\Http\Requests\Task\TaskValidationRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Models\Project;
use App\Models\Student;
use App\Models\Task;
use App\Models\Teacher;
use App\Notifications\TaskSubmissionNotification;
use App\Notifications\TaskValidationNotification;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Storage;


class TaskRepository implements TaskInterface
{
    public function __construct(
        private Task $task,
        private Project $project
    ) {
    }


    public function all(): array
    {
        try {
            return [
                'success' => true,
                'message' => 'all tasks retreived',
                'tasks' => $this->task::all()
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'something went wrong'
            ];
        }
    }

    public function readByProject($project_id): array
    {
        $project = $this->project::find($project_id);
        if (!$project) {
            return [
                'success' => false,
                'message' => 'project not found'
            ];
        }
        if (!$project->allow_view(auth()->user())) {
            return [
                'success' => false,
                'message' => 'not allowed to view this project tasks'
            ];
        }
        return [
            'success' => true,
            'message' => 'tasks retreived',
            'tasks' => $project->tasks()->orderBy('created_at', 'desc')->get()
        ];
    }

    public function getById($id): array
    {
        $task = $this->task::find($id);
        if (!$task) {
            return [
                "success" => false,
                "message" => "task not found",
            ];
        }
        if (!$task->project->allow_view(auth()->user())) {
            return [
                "success" => false,
                "message" => "Not allowed to view this task",
            ];
        }
        return [
            'success' => true,
            'message' => 'task retreived successfully',
            'task' => $task
        ];
    }

    public function create(CreateTaskRequest $request, $project_id): array
    {
        $project = $this->project::find($project_id);
        if (!$project) {
            return [
                'success' => false,
                'message' => 'project not found'
            ];
        }
        $auth = auth()->user();
        if (!$project->is_supervised_by($auth)) {
            return [
                'success' => false,
                'message' => 'not allowed to add task to this project'
            ];
        }
        try {
            $task = $this->task::create([
                'title' => $request->title,
                'description' => $request->description,
                'resources' => array_combine($request->resources, array_fill(0, count($request->resources), null)),
                'deadline' => $request->deadline,
                'status' => $this->task::STATUSES['IN_PROGRESS'],
                'project_id' => $project->id,
                'updated_at' => null
            ]);
            return [
                'success' => true,
                'message' => 'task added',
                'task' => $task
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => "Something went wrong. please try again"
            ];
        }
    }

    public function update(UpdateTaskRequest $request, $task_id): array
    {
        $task = $this->task::find($task_id);
        if (!$task) {
            return [
                'success' => false,
                'message' => 'task not found'
            ];
        }
        $auth = auth()->user();
        if (!$task->project->is_supervised_by($auth)) {
            return [
                'success' => false,
                'message' => 'not allowed to update this task'
            ];
        }
        if ($task->status !== $this->task::STATUSES['IN_PROGRESS']) {
            return [
                'success' => false,
                'message' => "can not update completed task or that has a submission"
            ];
        }
        try {
            $task->update([
                'title' => $request->title,
                'description' => $request->description,
                'resources' => array_combine($request->resources, array_fill(0, count($request->resources), null)),
                'deadline' => $request->deadline,
            ]);
            return [
                'success' => true,
                'message' => 'task updated',
                'task' => $task
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => "something went wrong. please try again"
            ];
        }
    }

    public function destroy($task_id): array
    {
        $task = $this->task::find($task_id);
        if (!$task) {
            return [
                'success' => false,
                'message' => 'task not found'
            ];
        }
        $auth = auth()->user();
        if (!$task->project->is_supervised_by($auth)) {
            return [
                'success' => false,
                'message' => 'not allowed to delete this task'
            ];
        }
        if ($task->status !== $this->task::STATUSES['IN_PROGRESS']) {
            return [
                'success' => false,
                'message' => "can not delete completed task or that has a submission"
            ];
        }
        try {
            $task->delete();
            return [
                'success' => true,
                'message' => 'task deleted',
                'task' => $task
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'something went wrong. please try again'
            ];
        }
    }

    public function submit(SubmitTaskRequest $request, $task_id): array
    {
        $files = $request['resources'] ?? [];
        $names = $request['names'] ?? [];
        $task = $this->task::find($task_id);
        if (!$task) {
            return [
                'success' => false,
                'message' => 'task not found'
            ];
        }
        if ($task->status !== $this->task::STATUSES['IN_PROGRESS']) {
            return [
                'success' => false,
                'message' => "can not submit completed task or that has a submission"
            ];
        }
        if (count($files) !== count(array_keys($request->names))) {
            return [
                'success' => false,
                'message' => "please provide all necessary documents"
            ];
        }
        $auth = auth()->user();
        if (!$task->project->has_member($auth->id)) {
            return [
                'success' => false,
                'message' => 'you are not a member in this project'
            ];
        }

        try {
            $task->resources = $this->save_files($files, $task, $names);
            $task->status = $this->task::STATUSES['PENDING'];
            $task->submission_date = Carbon::today()->format('Y-m-d');
            $task->submission_description = $request->submission_description;
            $task->refusal_motif = null;
            $task->save();

            $project = $task->project;
            $project->supervisor->notify(new TaskSubmissionNotification($task, $auth, Teacher::class));
            if ($project->co_supervisor)
                $project->co_supervisor->notify(new TaskSubmissionNotification($task, $auth, Teacher::class));
            foreach ($project->members as $member) {
                if ($member->id !== $auth->id) {
                    $member->notify(new TaskSubmissionNotification($task, $auth, Student::class));
                }
            }
            return [
                'success' => true,
                'message' => 'task submitted',
                'task' => $task
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => "something went wrong. please try again"
            ];
        }
    }

    public function validateTask(TaskValidationRequest $request, $task_id): array
    {
        $task = $this->task::find($task_id);
        if (!$task) {
            return [
                'success' => false,
                'message' => 'task not found'
            ];
        }
        $auth = auth()->user();
        if (!$task->project->is_supervised_by($auth)) {
            return [
                'success' => false,
                'message' => 'not allowed to validate this task'
            ];
        }
        if ($task->status !== $this->task::STATUSES['PENDING']) {
            return [
                'success' => false,
                'message' => "can not validate a non pending task"
            ];
        }

        try {
            if ($request->validated) {
                $task->update([
                    'status' => $this->task::STATUSES['COMPLETED'],
                    'completed_date' => Carbon::today()->format('Y-m-d')
                ]);
            } else {
                $this->delete_files($task);
                $task->update([
                    'status' => $this->task::STATUSES['IN_PROGRESS'],
                    'refusal_motif' => $request->refusal_motif,
                    'resources' => array_fill_keys(array_keys($task->resources), null),
                    'submission_date' => null,
                    'submission_description' => null
                ]);
            }

            $project = $task->project;
            if ($project->co_supervisor)
                $project->co_supervisor->notify(new TaskValidationNotification($task, $request->validated, Teacher::class));
            foreach ($project->members as $member) {
                $member->notify(new TaskValidationNotification($task, $request->validated, Student::class));
            }
            return [
                'success' => true,
                'message' => 'task submission updated',
                'task' => $task
            ];
        } catch (\Throwable $th) {
            return [
                'success' => false,
                'message' => 'something went wrong, please try again.'
            ];
        }
    }


    private function save_files($files, $task, $names)
    {
        $result = $task->resources;
        for ($i = 0; $i < count($names); $i++) {
            $path = $files[$i]->storeAs('tasks', Carbon::now()->timestamp . '.' . $files[$i]->getClientOriginalName(), 'public');
            $url = asset('storage/' . $path);
            $result[$names[$i]] = [
                'link' => $url,
                'name' => $files[$i]->getClientOriginalName()
            ];
        }
        return $result;
    }

    private function delete_files($task)
    {
        foreach ($task->resources as $type => $file) {
            if($file){
                $path = "public" . str_replace(asset('storage'), '', $file['link']);
                if (Storage::exists($path)) {
                    Storage::delete($path);
                }
            }
        }
    }
}
