<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'roles';
    const DEFAULT_ROLES = [
        "ADMIN" => "super_admin",
        "INTERNSHIP" => "internship_service_member",
        "COMMITTEE" => "scientific_committee_member",
        "HEADMASTER" => "headmaster",
        "PROJECT_HOLDER" => "project_holder",
        "SUPERVISOR" => "supervisor",
        "INCUBATOR_PRESIDENT" => "incubator_president",
        "JURY"  => "jury_member",
        "PROJECT_MEMBER" => "project_member",
        "TEACHER" => "teacher",
        "STUDENT" => "student"
    ];
     
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'type'
    ];

    const TYPES = [
        "DEFAULT" => "default",
        "CUSTOM" => "custom",
    ];

    /**
     * The users that belong to the role.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_role');
    }

    /**
     * The permissions that belong to the role.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permission');
    }
}
