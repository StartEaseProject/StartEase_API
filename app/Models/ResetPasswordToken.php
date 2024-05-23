<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class ResetPasswordToken extends Model
{
    use HasFactory;

    const TTL = 10*60; //10 min
    
    public $timestamps = false;
    
    protected $table = 'password_reset_tokens';

    protected $primaryKey = 'email';

    protected $fillable = [
        'code',
        'email'
    ];

    protected static function booted(): void
    {
        static::saving(function ($token) {
            $token->expires_at = Carbon::now()->addSeconds(self::TTL);
            $token->code = Hash::make($token->code);
            $token->created_at = Carbon::now();
        });
    }

    public function is_expired(): bool
    {
        return $this->expires_at < Carbon::now();
    }
}
