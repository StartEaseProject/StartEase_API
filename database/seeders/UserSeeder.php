<?php

namespace Database\Seeders;

use App\Models\Headmaster;
use App\Models\Internship_service_member;
use App\Models\Role;
use App\Models\Scientific_committee_member;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $types = [
            //Headmaster::class => Role::DEFAULT_ROLES['HEADMASTER'], 
            Scientific_committee_member::class => Role::DEFAULT_ROLES['COMMITTEE'], 
            Internship_service_member::class => Role::DEFAULT_ROLES['INTERNSHIP'], 
            Teacher::class => Role::DEFAULT_ROLES['TEACHER'], 
            Student::class => Role::DEFAULT_ROLES['STUDENT']
        ];
        $user = User::create([
            'username' => 'super_admin',
            'email' => 'super_admin@gmail.com',
            'phone_number' => $faker->phoneNumber,
            'password' => 'password'
        ]);

        $user->roles()->attach(Role::firstWhere('name', Role::DEFAULT_ROLES['ADMIN'])->id);

        foreach ($types as $class=>$role) {
            foreach ($class::all() as $person) {
                $test = $person->first_name ? $person->first_name : '';
                $user = User::create([
                    'username' => $person->first_name . '_username',
                    'email' => $person->first_name . '@gmail.com',
                    'phone_number' => $faker->phoneNumber,
                    'password' => 'password',
                    'person_type' => $class,
                    'person_id' => $person->getKey()
                ]);
                DB::table('user_role')->insert([
                    'role_id' => Role::firstWhere('name', $role)->id,
                    'user_id' => $user->id
                ]);
                if (substr($test, 0, strlen('scientific_committee_member_3')) === 'scientific_committee_member_3') {
                    $user->roles()->attach(Role::firstWhere('name', Role::DEFAULT_ROLES['INCUBATOR_PRESIDENT'])->id);
                }
            }
        }
    }
}
