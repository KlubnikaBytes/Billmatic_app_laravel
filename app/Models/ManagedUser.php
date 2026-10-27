<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class ManagedUser extends Authenticatable implements JWTSubject
{
    use HasFactory;

    protected $table = 'managed_users';

    protected $fillable = [
        'owner_user_id',
        'role',
        'name',
        'phone',
        'email',
        'otp',
        'otp_sent_at',
        'otp_expires_at',
        'is_verified',
    ];

    protected $hidden = [
        'otp',
    ];

    protected function casts(): array
    {
        return [
            'otp_sent_at' => 'datetime',
            'otp_expires_at' => 'datetime',
            'is_verified' => 'boolean',
        ];
    }

    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [
            'account_type' => 'managed_user',
        ];
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }
}