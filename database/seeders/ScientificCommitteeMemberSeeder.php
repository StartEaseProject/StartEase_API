<?php

namespace Database\Seeders;

use App\Models\Establishment;
use App\Models\Grade;
use App\Models\Role;
use App\Models\Scientific_committee_member;
use App\Models\Speciality;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ScientificCommitteeMemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $grade = Grade::firstWhere('type', Grade::TYPES['TEACHER']);
        $speciality = Speciality::firstWhere('name', 'Informatique');
        foreach (Establishment::all() as $estb) {
            $c =  Scientific_committee_member::create([
                'first_name' => 'committee_' . $estb->id,
                'last_name' => 'committee_' . $estb->id,
                'establishment_id' => $estb->id,
                'grade_id' => $grade->id,
                'speciality_id' => $speciality->id,
            ]);
            $u = User::create([
                "username" => 'committee_' . $estb->id,
                "email" => 'comm_' . $estb->id. '@gmail.com',
                "phone_number" => null,
                'password' => 'password',
                'is_enabled' => true,
                'person_type' => Scientific_committee_member::class,
                'person_id' => $c->id
            ]);
            $u->roles()->attach(Role::firstWhere('name', 'scientific_committee_member')->id);


            /************** */
            if($estb->short_name !== "ESI SBA"){
                $c =  Scientific_committee_member::create([
                    'first_name' => 'incubator_' . $estb->id,
                    'last_name' => 'incubator_' . $estb->id,
                    'establishment_id' => $estb->id,
                    'grade_id' => $grade->id,
                    'speciality_id' => $speciality->id,
                ]);
                $u = User::create([
                        "username" => 'incubator_' . $estb->id,
                        "email" => 'p_' . $estb->id . '@gmail.com',
                        "phone_number" => null,
                        'password' => 'password',
                        'is_enabled' => true,
                        'person_type' => Scientific_committee_member::class,
                        'person_id' => $c->id
                    ]);
                $u->roles()->attach(Role::firstWhere('name', 'scientific_committee_member')->id);
                $u->roles()->attach(Role::firstWhere('name', 'incubator_president')->id);
            }
        }

        $estb = Establishment::firstWhere('short_name', 'ESI SBA');
            $c =  Scientific_committee_member::create([
                'first_name' => 'Mohamed',
                'last_name' => 'Kechar',
                'establishment_id' => $estb->id,
                'grade_id' => $grade->id,
                'speciality_id' => $speciality->id,
            ]);
            $u = User::create([
                "username" => 'm_kechar',
                "email" => 'p.startease@gmail.com',
                "phone_number" => null,
                'photo_url' => 'http://localhost:8000/images/users/kechar.jpeg',
                'password' => 'password',
                'is_enabled' => true,
                'person_type' => Scientific_committee_member::class,
                'person_id' => $c->id
            ]);
            $u->roles()->attach(Role::firstWhere('name', 'scientific_committee_member')->id);
            $u->roles()->attach(Role::firstWhere('name', 'incubator_president')->id);
    }
}
