<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use App\Http\Interfaces\CommentInterface;
use App\Http\Requests\Comment\CreateCommentRequest;
use App\Http\Requests\Comment\ReplyCommentRequest;
use App\Http\Requests\Comment\UpdateCommentRequest;
use App\Http\Resources\CommentResource;

class CommentController extends BaseController
{
    public function __construct(
        private CommentInterface $commentRepository
    ){}


    public function store(CreateCommentRequest $request)
    {
        $response = $this->commentRepository->store($request);
        return !$response['success'] ?
            $this->sendError($response['message'], Response::HTTP_BAD_REQUEST) :
            $this->sendResponse($response['message'], [
                'comment' => new CommentResource($response['comment'])
            ]);
    }
    public function addReplay(ReplyCommentRequest $request)
    {
        $response = $this->commentRepository->addReply($request);
        return !$response['success'] ?
            $this->sendError($response['message'], Response::HTTP_BAD_REQUEST) :
            $this->sendResponse($response['message'], [
                'comment' => new CommentResource($response['comment'])
            ]);
    }
    public function readByProjectID($project_id)
    {
        $response = $this->commentRepository->readByProjectID($project_id);
        return !$response['success'] ?
            $this->sendError($response['message'], Response::HTTP_BAD_REQUEST) :
            $this->sendResponse($response['message'], [
                'comments' => CommentResource::collection($response['comments'])
            ]);
    }
    public function update(UpdateCommentRequest $request, $project_id)
    {
        $response = $this->commentRepository->UpdateComment($request, $project_id);
        return !$response['success'] ?
            $this->sendError($response['message'], Response::HTTP_BAD_REQUEST) :
            $this->sendResponse($response['message'], [
                'comment' => new CommentResource($response['comment'])
            ]);
    }
    public function delete($project_id)
    {
        $response = $this->commentRepository->DeleteComment($project_id);
        return !$response['success'] ?
            $this->sendError($response['message'], Response::HTTP_BAD_REQUEST) :
            $this->sendResponse($response['message']);
    }
}
