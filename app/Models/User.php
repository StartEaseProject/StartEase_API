<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use \Staudenmeir\EloquentHasManyDeep\HasRelationships;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRelationships;

    const DEFAULT_IMAGE = "/images/users/default.png";
    const MODELSTOTYPES = [
        Student::class => 'student',
        Teacher::class => 'teacher',
        Headmaster::class => 'headmaster',
        Internship_service_member::class => 'internship service member',
        Scientific_committee_member::class => 'scientific committee member'
    ];
    const TYPES = [
        'STUDENT' => 'student',
        'TEACHER' => 'teacher',
        'HEADMASTER' => 'headmaster',
        'INTERNSHIP' => 'internship service member',
        'COMMITTEE' => 'scientific committee member',
        'INCUBATOR_PRESIDENT' => 'incubator president'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'phone_number',
        'photo_url',
        'is_enabled',
        'tmp_phone_number',
        'phone_verif_code',
        'phone_verif_code_expires_at',
        'register_verification_hash',
        'person_type',
        'person_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'phone_verif_code',
        'phone_verif_code_expires_at',
        'tmp_phone_number'
    ];


    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_role');
    }
    public function permissions()
    {
        return $this->hasManyDeep(Permission::class, ['user_role', Role::class, 'role_permission']);
    }
    public function person(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'person_type', 'person_id');
    }
    public function submitted_project(): HasOne
    {
        return $this->hasOne(Project::class, "project_holder_id");
    }
    public function submitted_projects(): HasMany
    {
        return $this->hasMany(Project::class, "project_holder_id");
    }
    public function member_project(): HasOneThrough
    {
        return $this->hasOneThrough(Project::class, ProjectMember::class, 'member_id', 'id', 'id', 'project_id');
    }
    public function supervised_projects(): HasMany
    {
        return $this->hasMany(Project::class, "supervisor_id");
    }
    public function co_supervised_projects(): HasMany
    {
        return $this->hasMany(Project::class, "co_supervisor_id");
    }
    public function all_supervised_projects()
    {
        return $this->supervised_projects()->union($this->co_supervised_projects())->get();
    }
    public function all_projects(): Collection
    {
        return $this->submitted_projects()
            ->union($this->supervised_projects())
            ->union($this->co_supervised_projects())
            ->get();
    }
    public function remarks(): HasMany
    {
        return $this->hasMany(Remark::class);
    }
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
    public function defences()
    {
        return $this->belongsToMany(Defence::class, 'jurys', 'jury_id', 'defence_id')->withPivot('role');
    }


    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = Hash::make($password);
    }

    /**
     * Route notifications for the Vonage channel.
     *
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return string
     */
    public function routeNotificationForVonage($notification)
    {
        return $this->tmp_phone_number;
    }


    public static function generateHash()
    {
        return hash('sha256', str_pad(rand(0, pow(10, 10) - 1), 10, '0', STR_PAD_LEFT));
    }
}
