<?php

namespace App\Http\Controllers;

use App\Http\Interfaces\AnnouncementInterface;
use App\Http\Requests\Announcement\CreateAnnouncementRequest;
use App\Http\Requests\Announcement\UpdateAnnouncementRequest;
use App\Http\Resources\AnnouncementResource;
use Illuminate\Http\Response;


class AnnouncementController extends BaseController
{
    public function __construct(
        private AnnouncementInterface $announcementrepository
    ){}

    public function index_public()
    {
        $response = $this->announcementrepository->getPublic();
        return !$response['success'] ? 
            $this->sendError($response['message'], Response::HTTP_INTERNAL_SERVER_ERROR):
            $this->sendResponse($response['message'], [
                'announcements' => AnnouncementResource::collection($response['announcements'])
            ]);
    }

    public function index_establishment()
    {
        $response = $this->announcementrepository->getEstablishmentAnnoucements();
        return !$response['success'] ? 
            $this->sendError($response['message'], Response::HTTP_INTERNAL_SERVER_ERROR):
            $this->sendResponse($response['message'], [
                'announcements' => AnnouncementResource::collection($response['announcements'])
            ]);
    }

    public function show($id)
    {
        $response = $this->announcementrepository->getById($id);
        return !$response['success'] ?
        $this->sendError($response['message'], Response::HTTP_INTERNAL_SERVER_ERROR) :
            $this->sendResponse($response['message'], [
                'announcement' => new AnnouncementResource($response['announcement'])
            ]);
    }

    public function store(CreateAnnouncementRequest $request)
    {
        $response = $this->announcementrepository->create($request);
        return !$response['success'] ? 
            $this->sendError($response['message'], Response::HTTP_INTERNAL_SERVER_ERROR):
            $this->sendResponse($response['message'], [
                'announcement' => new AnnouncementResource($response['announcement'])
            ]);
    }

    public function update(UpdateAnnouncementRequest $request, $id)
    {
        $response = $this->announcementrepository->update($request, $id);
        return !$response['success'] ?
            $this->sendError($response['message'], Response::HTTP_INTERNAL_SERVER_ERROR) :
            $this->sendResponse($response['message'], [
                'announcement' => new AnnouncementResource($response['announcement'])
            ]);
    }

    public function destroy($ann_id)
    {
        $response = $this->announcementrepository->delete($ann_id);
        return !$response['success'] ? 
            $this->sendError($response['message'], Response::HTTP_INTERNAL_SERVER_ERROR):
            $this->sendResponse($response['message'], [
                'announcement' => new AnnouncementResource($response['announcement'])
            ]);
    }
}
