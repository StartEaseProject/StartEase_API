<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'announcements';

    const VISIBILITY= [
        'PUBLIC' => 'public',
        'PRIVATE' => 'private'
    ];

    const TYPES = [
        'SINGLE_DAY' => 'single day',
        'PERIOD' => 'period'
    ];
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'location',
        'photo',
        'date',
        'start_date',
        'end_date',
        'type',
        'visibility',
        'establishment_id'
    ];

    public function establishment(): BelongsTo
    {
        return $this->belongsTo(Establishment::class, 'establishment_id', 'id');
    }

    public function belongs_to(User $user): bool
    {
        return  $this->establishment_id === $user->person->establishment_id;
    }

    public function can_view(User $user): bool
    {
        return ($this->visibility===self::VISIBILITY['PUBLIC']) || ($this->establishment_id === $user->person->establishment_id);
    }
}
