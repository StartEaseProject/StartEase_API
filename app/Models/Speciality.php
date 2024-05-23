<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Speciality extends Model
{
    use HasFactory;

    const TYPES = [
        "STUDENT" => "student",
        "TEACHER" => "teacher",
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'specialities';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'type',
        'filiere_id'
    ];


    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }
    public function filiere(): BelongsTo
    {
        return $this->belongsTo(Filiere::class);
    }
    public function teachers(): HasMany
    {
        return $this->hasMany(Teacher::class);
    }
    public function scientific_committee_members(): HasMany
    {
        return $this->hasMany(Scientific_committee_member::class);
    }
    public function speciality(): BelongsTo
    {
        return $this->belongsTo(Filiere::class);
    }
}
