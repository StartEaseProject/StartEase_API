<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Period extends Model
{
    use HasFactory;

    const PROJECT_PERIODS = [
        'SUBMISSION' => 'projects submission period',
        'VALIDATION' => 'projects validation period',
        'RECOURSE' => 'projects recourse period',
        'RECOURSE_VALIDATION' => 'projects recourse validation period',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'periods';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'establishment_id'
    ];


    public function establishment(): BelongsTo
    {
        return $this->belongsTo(Establishment::class);
    }


    public static function is_period($name, $estb_id)
    {
        $currentDate = Carbon::today();
        $period = self::firstWhere([
            'name' => $name,
            'establishment_id' => $estb_id
        ]);
        return ($currentDate->gte($period->start_date) && $currentDate->lte($period->end_date));
    }

    public static function is_after_period($name, $estb_id)
    {
        $currentDate = Carbon::today();
        $last_period = Period::firstWhere([
            'name' => $name,
            'establishment_id' => $estb_id
        ]);
        return $currentDate->gt($last_period->end_date);
    }

    public function can_update(User $user): bool
    {
        return  $this->establishment_id === $user->person->establishment_id;
    }
}
