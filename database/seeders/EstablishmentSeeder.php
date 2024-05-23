<?php

namespace Database\Seeders;

use App\Models\Establishment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstablishmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /* for ($i=1; $i <=10 ; $i++) { 
            $e = Establishment::create([
                'name' => 'establishment_'.$i,
                'short_name' => 'E'.$i,
                'logo' => '',
                'description' => ''
            ]);
            for ($j=0; $j < 5 ; $j++) { 
                DB::table('rooms')->insert([
                    'name' => 'room '.$j,
                    'establishment_id' => $e->id
                ]);
            }
        } */

    }
}
