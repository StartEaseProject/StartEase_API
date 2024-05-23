<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Grade extends Model
{
    use HasFactory;

    const TYPES = [
        "TEACHER" => "teacher",
        "INTERNSHIP_SERVICE_MEMBER" => "internship_service_member"
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'grades';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'type'
    ];


    public function teachers(): HasMany
    {
        return $this->hasMany(Teacher::class);
    }
    public function internship_service_members(): HasMany
    {
        return $this->hasMany(Internship_service_member::class);
    }
    public function scientific_committee_members(): HasMany
    {
        return $this->hasMany(Scientific_committee_member::class);
    }
    public function headmaster(): HasMany
    {
        return $this->hasMany(Headmaster::class);
    }
}
