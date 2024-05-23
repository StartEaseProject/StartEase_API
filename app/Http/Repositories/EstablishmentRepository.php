<?php

namespace App\Http\Repositories;

use App\Http\Interfaces\EstablishmentInterface;
use App\Models\Establishment;
use Exception;

class EstablishmentRepository implements EstablishmentInterface
{
    public function __construct(
        private Establishment $establishment
    ){}

    
    public function all(): array
    {
        try {
            $establishments = $this->establishment::all();
            return [
                'success' => true,
                'message' => 'All establishments are gotten successfully',
                'establishments' => $establishments,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => "Could not retreive establishments",
            ];
        }
    }
}
