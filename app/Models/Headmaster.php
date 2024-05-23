<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Headmaster extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'headmasters';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'establishment_id',
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
    public function user(): MorphOne
    {
        return $this->morphOne(User::class, 'person');
    }
}
