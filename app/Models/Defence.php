<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Defence extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'defences';

    const DURATION = 2; //hours

    const FILES_TYPES = [
        'MEMORY' => 'memory',
        'BMC' => 'business model canvas',
        'LABEL-BREVET' => 'label-brevet',
        'PV' => 'thesis PV'
    ];

    const MODES = [
        "ON_SITE" => 'on site',
        "REMOTE" => 'remote',
    ];
    const NATURES = [
        "OPEN" => 'open',
        "CLOSED" => 'closed',
    ];
    const JURY_ROLES = [
        'PRESIDENT' => 'president',
        'EXAMINER' => 'examiner',
        'GUEST' => 'guest',
        'SUPERVISOR' => 'supervisor',
        'CO_SUPERVISOR' => 'co-supervisor'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'date',
        'time',
        'establishment_id',
        'room_id',
        'other_place',
        'mode',
        'nature',
        'reserves',
        'files',
        'guest'
    ];

    protected $casts = [
        'files' => 'array'
    ];


    public function project(): HasOne
    {
        return $this->hasOne(Project::class);
    }
    public function deliberations(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'deliberations', 'defence_id', 'member_id')->withPivot(['mark', 'mention', 'appreciation', 'diploma_url']);
    }
    public function jurys(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'jurys', 'defence_id', 'jury_id')->withPivot('role');
    }
    public function establishment(): BelongsTo
    {
        return $this->belongsTo(Establishment::class);
    }
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function jurys_formatted(): array
    {
        $formattedJurys = [
            'examiners' => [],
        ];

        foreach ($this->jurys as $jury) {
            $juryAttributes = [
                'email' => $jury->email,
                'photo' => $jury->photo,
                'id' => $jury->id,
            ];

            $role = $jury->pivot->role;

            if ($role === self::JURY_ROLES['PRESIDENT']) {
                $formattedJurys['president'] = $juryAttributes;
            } elseif ($role === self::JURY_ROLES['EXAMINER']) {
                array_push($formattedJurys['examiners'], $juryAttributes);
            } elseif ($role === self::JURY_ROLES['SUPERVISOR']) {
                $formattedJurys['supervisor'] = $juryAttributes;
            } elseif ($role === self::JURY_ROLES['CO_SUPERVISOR']) {
                $formattedJurys['co_supervisor'] = $juryAttributes;
            }
        }
        if (!isset($formattedJurys['co_supervisor'])) {
            $formattedJurys['co_supervisor'] = null;
        }
        return $formattedJurys;
    }


    public function allow_view(User $user): bool
    {
        switch ($user->person_type) {
            case Student::class:
            case Teacher::class:
                return $this->jurys()->find($user->id) || $this->project->allow_view($user);
            default:
                return $user->person ?
                    $user->person->establishment_id === $this->establishment_id :
                    false;
        }
    }

    public function same_establishment(User $user): bool
    {
        return $user->person && $user->person->establishment_id === $this->establishment_id;
    }
}
