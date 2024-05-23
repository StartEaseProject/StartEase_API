<?php

namespace App\Http\Repositories;

use App\Http\Interfaces\AnnouncementInterface;
use App\Http\Requests\Announcement\CreateAnnouncementRequest;
use App\Http\Requests\Announcement\UpdateAnnouncementRequest;
use App\Models\Announcement;
use App\Models\Establishment;
use App\Models\User;
use Exception;

class AnnouncementRepository implements AnnouncementInterface
{
    public function __construct(
        private Announcement $announcement
    ) {
    }


    public function getPublic(): array
    {
        $announcements = $this->announcement::where('visibility', $this->announcement::VISIBILITY['PUBLIC'])->get();
        return [
            'success' => true,
            'message' => 'announcements retreived',
            'announcements' => $announcements
        ];
    }

    public function getEstablishmentAnnoucements(): array
    {
        $user = auth()->user();
        return [
            'success' => true,
            'message' => 'establishment announcements retreived',
            'announcements' => $user->person ? $user->person->establishment->announcements : []
        ];
    }

    public function getById($id): array
    {
        $announcement = $this->announcement::find($id);
        if (!$announcement) {
            return [
                "success" => false,
                "message" => "announcement not found",
            ];
        }
        if (!$announcement->can_view(auth()->user())) {
            return [
                "success" => false,
                "message" => "Not allowed to view this announcement",
            ];
        }
        return [
            'success' => true,
            'message' => 'announcement retreived successfully',
            'announcement' => $announcement
        ];
    }

    public function create(CreateAnnouncementRequest $request): array
    {
        try {
            $auth = auth()->user();
            $is_period = $request->type === $this->announcement::TYPES['PERIOD'];
            $announcement = $this->announcement::create([
                'title' => $request->title,
                'establishment_id' => $auth->person->establishment_id,
                'description' => $request->description,
                'location' => $request->location,
                'photo' => '',
                'visibility' => $request->visibility,
                'type' => $request->type,
                'date' => $is_period ? null : $request->date,
                'start_date' => $is_period ? $request->start_date : null,
                'end_date' => $is_period ? $request->end_date : null
            ]);
            $announcement->update([
                'photo' => $this->upload_image($request->photo, $announcement->id)
            ]);
            return [
                'success' => true,
                'message' => 'announcement created successfully',
                'announcement' => $announcement
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => "something went wrong. please try again"
            ];
        }
    }

    public function update(UpdateAnnouncementRequest $request, $id): array
    {
        try {
            $announcement = $this->announcement::find($id);
            if (!$announcement) {
                return [
                    "success" => false,
                    "message" => "announcement not found",
                ];
            }
            if (!$announcement->belongs_to(auth()->user())) {
                return [
                    "success" => false,
                    "message" => "Not allowed to update this announcement",
                ];
            }
            $is_period = $request->type === $this->announcement::TYPES['PERIOD'];
            $announcement->update([
                'title' => $request->title,
                'description' => $request->description,
                'location' => $request->location,
                'visibility' => $request->visibility,
                'photo' => $request->photo ? $this->upload_image($request->photo, $announcement->id) : $announcement->photo,
                'type' => $request->type,
                'date' => $is_period ? null : $request->date,
                'start_date' => $is_period ? $request->start_date : null,
                'end_date' => $is_period ? $request->end_date : null
            ]);
            return [
                'success' => true,
                'message' => 'announcement updated successfully',
                'announcement' => $announcement
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => "something went wrong. please try again"
            ];
        }
    }

    public function delete($ann_id): array
    {
        try {
            $announcement = $this->announcement::find($ann_id);
            if (!$announcement) {
                return [
                    "success" => false,
                    "message" => "announcement not found",
                ];
            }
            if (!$announcement->belongs_to(auth()->user())) {
                return [
                    "success" => false,
                    "message" => "Not allowed to update this announcement",
                ];
            }
            $announcement->delete();
            return [
                'success' => true,
                'message' => 'announcement deleted',
                'announcement' => $announcement
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => "something went wrong. please try again"
            ];
        }
    }

    public function upload_image($image, $ann_id): string
    {
        $path = $image->storeAs('announcements', $ann_id.'.'. $image->getClientOriginalExtension(), 'public');
        $url = asset('storage/' . $path);
        return $url;
    }
}
