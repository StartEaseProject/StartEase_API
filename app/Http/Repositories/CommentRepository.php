<?php

namespace App\Http\Repositories;

use App\Models\Period;
use App\Models\Comment;
use App\Http\Interfaces\CommentInterface;
use App\Http\Requests\Comment\CreateCommentRequest;
use App\Http\Requests\Comment\ReplyCommentRequest;
use App\Http\Requests\Comment\UpdateCommentRequest;
use App\Models\Project;

class CommentRepository implements CommentInterface
{
    public function __construct(
        private Comment $comment,
        private Project $project,
        private Period $period
    ) {
    }


    public function store(CreateCommentRequest $request): array
    {
        $project = $this->project::find($request->project_id);
        if (!$project) {
            return [
                "success" => false,
                "message" => "project not found",
            ];
        }
        if (!$project->is_accepted()) {
            return [
                "success" => false,
                "message" => "You cannot comment on non validated project",
            ];
        }
        $auth = auth()->user();
        if (!$project->allow_view($auth)) {
            return [
                "success" => false,
                "message" => "Not allowed to comment on this project",
            ];
        }

        $comment = $this->comment::create([
            "content" => $request->content,
            "user_id" => $auth->id,
            "project_id" => $project->id,
            'updated_at' => null

        ]);
        return [
            "success" => true,
            "message" => "Comment added successfully",
            "comment" => $comment
        ];
    }

    public function addReply(ReplyCommentRequest $request): array
    {
        try {
            $parent = $this->comment::find($request->parent_id);
            if (!$parent) {
                return [
                    "success" => false,
                    "message" => "parent comment not found",
                ];
            }
            $auth = auth()->user();
            $project = $parent->project;
            if (!$project->allow_view($auth)) {
                return [
                    "success" => false,
                    "message" => "Not allowed to comment on this project",
                ];
            }

            $comment = $this->comment::create([
                "content" => $request->content,
                "user_id" => $auth->id,
                "project_id" => $project->id,
                "parent_comment_id" => $parent->id,
                'updated_at' => null
            ]);
            return [
                "success" => true,
                "message" => "Reply added successfully",
                "comment" => $comment
            ];
        } catch (\Exception $e) {
            return [
                "success" => false,
                "message" => "Something went wrong , please try again"
            ];
        }
    }

    public function readByProjectID($project_id): array
    {
        try {
            $project = $this->project::find($project_id);
            if (!$project) {
                return [
                    "success" => false,
                    "message" => "Project not found",
                ];
            }
            if (!$project->allow_view(auth()->user())) {
                return [
                    "success" => false,
                    "message" => "Not allowed to view this project comments",
                ];
            }

            return [
                "success" => true,
                "message" => "Comments gotten successfully",
                "comments" => $project->comments()->whereNull('parent_comment_id')->get()

            ];
        } catch (\Exception $e) {
            return [
                "success" => false,
                "message" => "Something went wrong , please try again"
            ];
        }
    }

    public function UpdateComment(UpdateCommentRequest $request, $id): array
    {
        try {
            $comment = $this->comment::find($id);
            if (!$comment) {
                return [
                    "success" => false,
                    "message" => "comment not found",
                ];
            }
            if (!$comment->belongs_to(auth()->id())) {
                return [
                    "success" => false,
                    "message" => "Not allowed to update this comment",
                ];
            }

            $comment->update([
                "content" => $request->content,
            ]);
            return [
                "success" => true,
                "message" => "comment updated successfully",
                "comment" => $comment
            ];
        } catch (\Exception $e) {
            return [
                "success" => false,
                "message" => "Something went wrong , please try again"
            ];
        }
    }

    public function DeleteComment($id): array
    {
        try {
            $comment = $this->comment::find($id);
            if (!$comment) {
                return [
                    "success" => false,
                    "message" => "comment not found",
                ];
            }
            if (!$comment->belongs_to(auth()->id())) {
                return [
                    "success" => false,
                    "message" => "Not allowed to delete this comment",
                ];
            }

            $comment->delete();
            return [
                "success" => true,
                "message" => "comment deleted successfully",
                "comment" => $comment->get()
            ];
        } catch (\Exception $e) {
            return [
                "success" => false,
                "message" => "Something went wrong , please try again"
            ];
        }
    }
}
