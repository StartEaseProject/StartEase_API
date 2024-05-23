<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    const TYPES = [
        "STARTUP" => 'Un diplôme-Une startup',
        "BREVET" => 'Un diplôme-Un Brevet',
    ];

    const STATUSES = [
        "PENDING" => 'pending',
        "REFUSED" => 'refused',
        "ACCEPTED" => 'accepted',
        "RECOURSE" => 'recourse',
        "RECOURSE_ACCEPTED" => 'accepted after recourse',
        "RECOURSE_REFUSED" => 'refused after recourse'
    ];

    const UI_STATUSES = [
        "REFUSED" => self::STATUSES['REFUSED'],
        "ACCEPTED" => self::STATUSES['ACCEPTED'],
        "RECOURSE" => self::STATUSES['RECOURSE']
    ];

    const FILES_TYPES = [
        'COMMITTEE' => [
            'COMM_REGISTER' => 'commercial register',
            'PROJECT_PRESENTATION' => 'project presentation',
            'ECONOMIC_STUDY' => 'economic study',
        ],
        'AUTHORIZATION_FILE' => 'thesis defence authorization file',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'projects';
    const CREATED_AT = 'submission_date';
    const UPDATED_AT = 'updated_at';
    protected $casts = [
        'progress' => 'array',
        'files' => 'array'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type',
        'trademark_name',
        'scientific_name',
        'resume',
        'submission_date',
        'decision_date',
        'recourse_decision_date',
        'status',
        'establishment_id',
        'project_holder_id',
        'supervisor_id',
        'co_supervisor_id',
        'progress',
        'is_authorized_defence',
        'files',
        'defence_id'
    ];

    public function domicile_establishment(): BelongsTo
    {
        return $this->belongsTo(Establishment::class, 'establishment_id', 'id');
    }
    public function project_holder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'project_holder_id');
    }
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_members', 'project_id', 'member_id');
    }
    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }
    public function co_supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'co_supervisor_id');
    }
    public function remarks(): HasMany
    {
        return $this->hasMany(Remark::class);
    }
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
    public function defence(): BelongsTo
    {
        return $this->belongsTo(Defence::class);
    }


    public function belongs_to($user_id): bool
    {
        return $this->project_holder_id === $user_id;
    }
    public function allow_view(User $user): bool
    {
        switch ($user->person_type) {
            case Student::class:
                return $this->project_holder_id === $user->id ||
                    ProjectMember::where('member_id', $user->id)
                    ->where('project_id', $this->id)
                    ->exists();
            case Teacher::class:
                return in_array($user->id, [$this->supervisor_id, $this->co_supervisor_id, $this->project_holder_id])
                ||($this->defence ? $this->defence->jurys->find($user->id) : false);
            default:
                return $user->person ?
                    $user->person->establishment_id === $this->establishment_id :
                    false;
        }
    }
    public function allow_follow_up(User $user): bool
    {
        switch ($user->person_type) {
            case Student::class:
                return $this->project_holder_id === $user->id ||
                    ProjectMember::where('member_id', $user->id)
                    ->where('project_id', $this->id)
                    ->exists();
            case Teacher::class:
                return in_array($user->id, [$this->supervisor_id, $this->co_supervisor_id, $this->project_holder_id]);
            default:
                return false;
        }
    }
    public function same_establishment(User $user): bool
    {
        return $user->person && $user->person->establishment_id === $this->establishment_id;
    }
    public function is_supervised_by(User $user): bool
    {
        return $user->id === $this->supervisor_id;
    }
    public function has_member($user_id): bool
    {
        return $this->members()->find($user_id) ? true : false;
    }

    public function is_accepted(): bool
    {
        return $this->status === self::STATUSES['ACCEPTED'] || $this->status === self::STATUSES['RECOURSE_ACCEPTED'];
    }
    public function is_refused(): bool
    {
        return $this->status === self::STATUSES['REFUSED'] || $this->status === self::STATUSES['RECOURSE_REFUSED'];
    }
}
