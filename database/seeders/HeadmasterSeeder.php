<?php

namespace Database\Seeders;

use App\Models\Establishment;
use App\Models\Grade;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HeadmasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /* $grade = Grade::firstWhere('type', Grade::TYPES['TEACHER']);
        foreach (Establishment::all() as $estb) {
            DB::table('headmasters')->insert([
                'first_name' => 'headmaster_'.$estb->name,
                'last_name' => 'headmaster_'.$estb->name,
                'establishment_id' => $estb->id,
                'grade_id' => $grade->id,
            ]);
        } */
    }
}
