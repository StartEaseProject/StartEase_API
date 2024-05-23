<?php

namespace Database\Seeders;

use App\Models\Establishment;
use App\Models\Filiere;
use App\Models\Role;
use App\Models\Speciality;
use App\Models\Student;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $speciality = Speciality::firstWhere('name', 'Software Development');
        foreach (Establishment::all() as $estb) {
            for ($i = 1; $i <=5; $i++) {
                $s = Student::create([
                    'num_inscription' => 'num_' . $i . '_' . $estb->id,
                    'first_name' => 'student_' . $i . '_' . $estb->id,
                    'last_name' => 'student_' . $i . '_' . $estb->id,
                    'birthday' => "2000-07-07",
                    'birth_place' => $faker->city,
                    'establishment_id' => $estb->id,
                    'speciality_id' => $speciality->id,
                ]);
                $u = User::create([
                    "username" => 'stud_' . $i . '_' . $estb->id,
                    "email" => 's_' . $i . '_' . $estb->id . '@gmail.com',
                    "phone_number" => null,
                    'password' => 'password',
                    'is_enabled' => true,
                    'person_type' => Student::class,
                    'person_id' => $s->id
                ]);
                $u->roles()->attach(Role::firstWhere('name', 'student')->id);
            }
        }

        $estb = Establishment::firstWhere('short_name', 'ESI SBA');
        $s = Student::create([
            'num_inscription' => 'code_' . $i . '_' . $estb->id,
            'first_name' => 'bahaa',
            'last_name' => 'bouzeboudja',
            'birthday' => "2003-07-07",
            'birth_place' => "Oran",
            'establishment_id' => $estb->id,
            'speciality_id' => $speciality->id,
        ]);
        $u = User::create([
            "username" => 'b_bouzebdj',
            "email" => 's2.startease@gmail.com',
            "phone_number" => null,
            'password' => 'password',
            'is_enabled' => true,
            'person_type' => Student::class,
            'person_id' => $s->id
        ]);
        $u->roles()->attach(Role::firstWhere('name', 'student')->id);
    }
}
