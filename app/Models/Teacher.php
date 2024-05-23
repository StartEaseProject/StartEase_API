<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Teacher extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'teachers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'birthday',
        'birth_place',
        'establishment_id',
        'speciality_id',
        'matricule',
        'grade_id'
    ];

    
    public function establishment(): BelongsTo
    {
        return $this->belongsTo(Establishment::class);
    }
    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class);
    }
    public function speciality(): BelongsTo
    {
        return $this->belongsTo(Speciality::class);
    }

    public function deliberation(): HasMany
    {
        return $this->hasMany(Deliberation::class);
    }

    
    public function user(): MorphOne
    {
        return $this->morphOne(User::class, 'person');
    }
}
