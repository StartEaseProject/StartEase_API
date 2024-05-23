<?php

namespace App\Http\Interfaces;

use App\Http\Requests\SetPeriodRequest;

interface PeriodInterface
{
    public function setPeriod(SetPeriodRequest $request): array;
    public function getByEstablishment(): array;
}
