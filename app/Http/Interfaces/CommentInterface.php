<?php

namespace App\Http\Interfaces;

use App\Http\Requests\Comment\CreateCommentRequest;
use App\Http\Requests\Comment\ReplyCommentRequest;
use App\Http\Requests\Comment\UpdateCommentRequest;

interface CommentInterface
{
    public function store(CreateCommentRequest $request): array;
    public function addReply(ReplyCommentRequest $request): array;
    public function readByProjectID($project_id): array;
    public function UpdateComment(UpdateCommentRequest $request, $id): array;
    public function DeleteComment($id): array;

    

    

}
