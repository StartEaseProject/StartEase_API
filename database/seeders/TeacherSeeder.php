<?php

namespace Database\Seeders;

use App\Models\Establishment;
use App\Models\Grade;
use App\Models\Role;
use App\Models\Speciality;
use App\Models\Teacher;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $grade = Grade::firstWhere('type', Grade::TYPES['TEACHER']);
        $speciality = Speciality::firstWhere('name', 'Informatique');
        foreach (Establishment::all() as $estb) {
            for ($i = 1; $i <= 8; $i++) {
                $t = Teacher::create([
                    'matricule' => 'matricule_' . $i . '_' . $estb->id,
                    'first_name' => 'teach_' . $i . '_' . $estb->id,
                    'last_name' => 'teach_' . $i . '_' . $estb->id,
                    'birthday' => "1990-05-05",
                    'birth_place' => $faker->city,
                    'establishment_id' => $estb->id,
                    'grade_id' => $grade->id,
                    'speciality_id' => $speciality->id,
                ]);

                $u = User::create([
                    "username" => 'teach_' . $i . '_' . $estb->id,
                    "email" => 't_' . $i . '_' . $estb->id . '@gmail.com',
                    "phone_number" => null,
                    'password' => 'password',
                    'is_enabled' => true,
                    'person_type' => Teacher::class,
                    'person_id' => $t->id
                ]);
                $u->roles()->attach(Role::firstWhere('name', 'teacher')->id);
            }
        }

        $estb = Establishment::firstWhere('short_name', 'ESI SBA');
        $t = Teacher::create([
            'matricule' => 'matricule_' . 222 . '_' . $estb->id,
            'first_name' => 'Oussama',
            'last_name' => 'Serhane',
            'birthday' => "1990-05-05",
            'birth_place' => "Sidi Bel Abbes",
            'establishment_id' => $estb->id,
            'grade_id' => $grade->id,
            'speciality_id' => $speciality->id,
        ]);

        $u = User::create([
            "username" => 'o_serhane',
            "email" => 't.startease@gmail.com',
            "phone_number" => null,
            'photo_url' => 'http://localhost:8000/images/users/serhane.jpeg',
            'password' => 'password',
            'is_enabled' => true,
            'person_type' => Teacher::class,
            'person_id' => $t->id
        ]);
        $u->roles()->attach(Role::firstWhere('name', 'teacher')->id);

        $t = Teacher::create([
            'matricule' => 'matricule_' . 111 . '_' . $estb->id,
            'first_name' => 'Nesrine',
            'last_name' => 'Lehireche',
            'birthday' => "1990-05-05",
            'birth_place' => "Sidi Bel Abbes",
            'establishment_id' => $estb->id,
            'grade_id' => $grade->id,
            'speciality_id' => $speciality->id,
        ]);

        $u = User::create([
            "username" => 'n_lehirech',
            "email" => 'j.startease@gmail.com',
            "phone_number" => null,
            'photo_url' => 'http://localhost:8000/images/users/lehireche.jpeg',
            'password' => 'password',
            'is_enabled' => true,
            'person_type' => Teacher::class,
            'person_id' => $t->id
        ]);
        $u->roles()->attach(Role::firstWhere('name', 'teacher')->id);
    }
}
