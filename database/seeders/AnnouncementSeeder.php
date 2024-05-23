<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\Establishment;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
class AnnouncementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        foreach (Establishment::all() as $estb) 
        {
            for ($i=0; $i<5 ; $i++) 
            {
                $type = $faker->randomElement(array_values(Announcement::TYPES));
                $date = $type === Announcement::TYPES['SINGLE_DAY'] ? $faker->dateTimeBetween('now', '+1 year') : null;
                $startDate = $type === Announcement::TYPES['PERIOD'] ? Carbon::today()->addYear() : null;
                $endDate = $type === Announcement::TYPES['PERIOD'] ? $startDate->copy()->addDays(rand(1, 8))->format('Y-m-d') : null;

                DB::table('announcements')->insert([
                    'title' => $faker->sentence,
                    'description' => $faker->paragraph,
                    'establishment_id' => $estb->id,
                    'location' => $faker->city,
                    'photo' => $faker->imageUrl(),
                    'type' => $type,
                    'date' => $date,
                    'start_date' => $startDate ? $startDate->format('Y-m-d') : null,
                    'end_date' => $endDate,
                    'visibility' => Announcement::VISIBILITY['PUBLIC'],
                    'created_at' => Carbon::now(),
                ]);
                DB::table('announcements')->insert([
                    'title' => $faker->sentence,
                    'description' => $faker->paragraph,
                    'establishment_id' => $estb->id,
                    'location' => $faker->city,
                    'photo' => $faker->imageUrl(),
                    'type' => $type,
                    'date' => $date,
                    'start_date' => $startDate ? $startDate->format('Y-m-d') : null,
                    'end_date' => $endDate,
                    'visibility' => Announcement::VISIBILITY['PRIVATE'],
                    'created_at' => Carbon::now(),
                ]);
            }
        }
    }
}
