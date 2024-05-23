<?php

namespace App\Http\Repositories;

use App\Http\Interfaces\GradeInterface;
use App\Models\Grade;
use Exception;

class GradeRepository implements GradeInterface
{
    public function __construct(
        private Grade $grade
    ){}


    public function all(): array
    {
        try {
            $grades = $this->grade::all();
            return [
                'success' => true,
                'message' => 'All grades are gotten successfully',
                'grades' => $grades,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => "Could not retreive grades",
            ];
        }
    }
}
