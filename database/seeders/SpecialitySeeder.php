<?php

namespace Database\Seeders;

use App\Models\Filiere;
use App\Models\Speciality;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SpecialitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (Filiere::all() as $f) {
            for ($i = 1; $i <= 3; $i++) {
                DB::table('specialities')->insert([
                    'name' => 'student_filiere_'.$f->id.'_speciality_' . $i,
                    'type' => Speciality::TYPES['STUDENT'],
                    'filiere_id' => $f->id
                ]);
            }
        }
        for ($i = 1; $i <= 5; $i++) {
            DB::table('specialities')->insert([
                'name' => 'teacher_speciality_' . $i,
                'type' => Speciality::TYPES['TEACHER']
            ]);
        }
    }
}
