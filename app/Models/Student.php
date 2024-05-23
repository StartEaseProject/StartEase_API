<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Znck\Eloquent\Traits\BelongsToThrough;

class Student extends Model
{
    use HasFactory, BelongsToThrough;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'students';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'num_inscription',
        'first_name',
        'last_name',
        'birthday',
        'birth_place',
        'establishment_id',
        'speciality_id',
    ];


    public function establishment(): BelongsTo
    {
        return $this->belongsTo(Establishment::class);
    }
    public function filiere()
    {
        return $this->belongsToThrough(Filiere::class, Speciality::class);
    }
    public function speciality(): BelongsTo
    {
        return $this->belongsTo(Speciality::class);
    }

    public function deliberation(): HasOne
    {
        return $this->hasOne(Deliberation::class);
    }
    
    public function user(): MorphOne
    {
        return $this->morphOne(User::class, 'person');
    }
}
