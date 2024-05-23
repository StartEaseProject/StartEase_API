<?php

namespace App\Http\Interfaces;

use App\Http\Requests\Announcement\CreateAnnouncementRequest;
use App\Http\Requests\Announcement\UpdateAnnouncementRequest;

interface AnnouncementInterface
{
    public function getPublic(): array;

    public function getEstablishmentAnnoucements(): array;

    public function getById($id): array;
    
    public function create(CreateAnnouncementRequest $request): array;

    public function update(UpdateAnnouncementRequest $request, $id): array;

    public function delete($ann_id) : array;
}
