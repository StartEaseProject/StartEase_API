<?php

namespace App\Http\Repositories;

use App\Http\Interfaces\SpecialityInterface;
use App\Models\Speciality;
use Exception;

class SpecialityRepository implements SpecialityInterface
{
    public function __construct(
        private Speciality $speciality
    ){}


    public function all(): array
    {
        try {
            $specialities = $this->speciality::all();
            return [
                'success' => true,
                'message' => 'All specialities are gotten successfully',
                'specialities' => $specialities,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => "Could not retreive specialities",
            ];
        }
    }
}
