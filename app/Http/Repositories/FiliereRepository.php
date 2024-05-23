<?php

namespace App\Http\Repositories;

use App\Http\Interfaces\FiliereInterface;
use App\Models\Filiere;
use Exception;

class FiliereRepository implements FiliereInterface
{
    public function __construct(
        private Filiere $filiere
    ){}

    
    public function all(): array
    {
        try {
            $filieres = $this->filiere::all();
            return [
                'success' => true,
                'message' => 'All filieres are gotten successfully',
                'filieres' => $filieres,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => "Could not retreive filieres",
            ];
        }
    }
}
