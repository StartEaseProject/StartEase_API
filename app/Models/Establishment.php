<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Establishment extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'establishments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'logo',
        'short_name',
        'description'
    ];


    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }
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
    public function headmaster(): HasOne
    {
        return $this->hasOne(Headmaster::class);
    }
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }
    public function defences(): HasMany
    {
        return $this->hasMany(Defence::class);
    }
    public function periods(): HasMany
    {
        return $this->hasMany(Period::class);
    }
    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class);
    }
    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }
}
