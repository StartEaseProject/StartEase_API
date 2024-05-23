<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tasks';

    const STATUSES = [
        "IN_PROGRESS" => "in progress",
        "PENDING" => "pending",
        "COMPLETED" => "completed"
    ];


    protected $casts = [
        'resources' => 'array',
    ];
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'deadline',
        'status',
        'refusal_motif',
        'submission_date',
        'submission_description',
        'completed_date',
        'resources',
        'project_id'
    ];


    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
