<?php

namespace Database\Seeders;

use App\Models\Establishment;
use App\Models\Period;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PeriodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (Establishment::all() as $estb) {
            DB::table('periods')->insert([
                'name' => Period::PROJECT_PERIODS['SUBMISSION'],
                'start_date' => Carbon::today(),
                'end_date' => Carbon::today()->addDays(1),
                'establishment_id' => $estb->id
            ]);
            DB::table('periods')->insert([
                'name' => Period::PROJECT_PERIODS['VALIDATION'],
                'start_date' => Carbon::today()->addDays(2),
                'end_date' => Carbon::today()->addDays(3),
                'establishment_id' => $estb->id
            ]);
            DB::table('periods')->insert([
                'name' => Period::PROJECT_PERIODS['RECOURSE'],
                'start_date' => Carbon::today()->addDays(4),
                'end_date' => Carbon::today()->addDays(5),
                'establishment_id' => $estb->id
            ]);
            DB::table('periods')->insert([
                'name' => Period::PROJECT_PERIODS['RECOURSE_VALIDATION'],
                'start_date' => Carbon::today()->addDays(6),
                'end_date' => Carbon::today()->addDays(7),
                'establishment_id' => $estb->id
            ]);
        }
    }
}
