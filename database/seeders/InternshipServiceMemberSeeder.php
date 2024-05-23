<?php

namespace Database\Seeders;

use App\Models\Establishment;
use App\Models\Grade;
use App\Models\Internship_service_member;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InternshipServiceMemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $grade = Grade::firstWhere('type', Grade::TYPES['INTERNSHIP_SERVICE_MEMBER']);
        foreach (Establishment::all() as $estb) {
            $i = Internship_service_member::create([
                'first_name' => 'internship_'. $estb->id,
                'last_name' => 'internship_'. $estb->id,
                'establishment_id' => $estb->id,
                'grade_id' => $grade->id,
            ]);

            $u = User::create([
                "username" => 'internship_' . $estb->id,
                "email" => 'i_' . $estb->id.'@gmail.com',
                "phone_number" => null,
                'password' => 'password',
                'is_enabled' => true,
                'person_type' => Internship_service_member::class,
                'person_id' => $i->id
            ]);

            $u->roles()->attach(Role::firstWhere('name', 'internship_service_member')->id);
        }

        $estb = Establishment::firstWhere('short_name', 'ESI SBA');
        $i =  Internship_service_member::create([
            'first_name' => 'Omar',
            'last_name' => 'Majid_',
            'establishment_id' => $estb->id,
            'grade_id' => $grade->id,
        ]);
        $u = User::create([
            "username" => 'o_majid',
            "email" => 'i.startease@gmail.com',
            "phone_number" => null,
            'password' => 'password',
            'is_enabled' => true,
            'person_type' => Internship_service_member::class,
            'person_id' => $i->id
        ]);
        $u->roles()->attach(Role::firstWhere('name', 'internship_service_member')->id);
    }
}
