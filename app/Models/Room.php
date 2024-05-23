<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rooms';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'establishment_id'
    ];

    /**
     * The users that belong to the role.
     */
    public function establishment(): BelongsTo
    {
        return $this->belongsTo(Establishment::class);
    }
    public function defences(): HasMany
    {
        return $this->hasMany(Defence::class);
    }
}
