<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\Establishment;
use App\Models\Filiere;
use App\Models\Grade;
use App\Models\Headmaster;
use App\Models\Role;
use App\Models\Speciality;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $establishments = json_decode(file_get_contents(database_path('seeders/data/establishments.json')));

        $filieres = json_decode(file_get_contents(database_path('seeders/data/filieres.json')));

        $specailities = json_decode(file_get_contents(database_path('seeders/data/specialities.json')));

        $grades = json_decode(file_get_contents(database_path('seeders/data/grades.json')));

        $user = User::create([
            'username' => 'admin',
            'email' => 'admin@gmail.com',
            'phone_number' => null,
            'password' => 'password'
        ]);
        $user->roles()->attach(Role::firstWhere('name', Role::DEFAULT_ROLES['ADMIN'])->id);

        foreach ($filieres as $filiere) {
            $f = Filiere::create([
                'name' => $filiere->name,
            ]);

            foreach ($filiere->specialities as $sp) {
                Speciality::create([
                    'name' => $sp,
                    'type' => 'student',
                    'filiere_id' => $f->id
                ]);
            }
        }

        foreach ($grades as $g) {
            Grade::create([
                'name' => $g->name,
                'type' => $g->type
            ]);
        }

        foreach ($specailities as $sp) {
            Speciality::create([
                'name' => $sp,
                'type' => "teacher"
            ]);
        }
        $grade = Grade::firstWhere('type', Grade::TYPES['INTERNSHIP_SERVICE_MEMBER']);
        foreach (Establishment::all() as $estb) {
            
        }
        foreach ($establishments as $est) {
            $es = Establishment::create([
                'name' => $est->name,
                'logo' => $est->logo,
                'short_name' => $est->short_name,
                'description' => $est->description,
            ]);

            $head = Headmaster::create([
                'first_name' => $est->headmaster->person->first_name,
                'last_name' => $est->headmaster->person->last_name,
                'establishment_id' => $es->id,
                'grade_id' => Grade::firstWhere('name', 'Professor')->id
            ]);

            $u = User::create([
                "username" => $est->headmaster->username,
                "email" => $est->headmaster->email,
                "phone_number" => $est->headmaster->phone_number,
                "photo_url" => $est->headmaster->photo_url,
                'password' => 'password',
                'is_enabled' => true,
                'person_type' => Headmaster::class,
                'person_id' => $head->id
            ]);

            $u->roles()->attach(Role::firstWhere('name', 'headmaster')->id);

            /* rooms */
            for ($j = 0; $j < 5; $j++) {
                DB::table('rooms')->insert([
                    'name' => 'room_' . $j,
                    'establishment_id' => $es->id
                ]);
            }

            /* Annoucements */
            foreach ($est->announcements as $an) {
                Announcement::create([
                    'title' => $an->title,
                    'description' => $an->description,
                    'establishment_id' => $es->id,
                    'location' => $an->location,
                    'photo' => $an->photo,
                    'type' => $an->type,
                    'date' => $an->date,
                    'start_date' => $an->start_date,
                    'end_date' => $an->end_date,
                    'visibility' => $an->visibility,
                    'created_at' => Carbon::now(),
                ]);
            }
        }        
    }
}
