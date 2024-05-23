<?php

namespace App\Models;

use App\Models\Defence;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Jury extends Model
{
    use HasFactory;
    protected $table = 'jurys';
    protected $fillable = [
        'jury_id',
        'defence_id',
        'role',
    ];
}
