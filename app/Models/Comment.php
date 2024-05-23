<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Comment extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'comments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'content',
        'user_id',
        'project_id',
        'parent_comment_id'
    ];


    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function father_comment(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_comment_id');
    }
    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_comment_id');
    }


    public function belongs_to($user_id): bool
    {
        return $this->user_id === $user_id;
    }
}
