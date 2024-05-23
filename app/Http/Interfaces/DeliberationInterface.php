<?php

namespace App\Http\Interfaces;

use App\Http\Requests\Deliberation\CreateDeliberationRequest;

interface DeliberationInterface
{
    public function create(CreateDeliberationRequest $request, $defence_id): array;
    public function getByDefence($defence_id) : array;
}