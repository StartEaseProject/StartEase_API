<?php

namespace Database\Seeders;

use App\Models\Grade;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GradeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (Grade::TYPES as $key=>$val) {
            for ($i=1; $i <=5 ; $i++) { 
                DB::table('grades')->insert([
                    'name' => $val.'_grade_'.$i,
                    'type' => $val
                ]);
            }
        }
    }
}
