<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'role',
        'member_since',
        'points',
        'email_verified_at',
    ];

    protected $hidden = [
        'remember_token',
    ];

    protected $attributes = [
        'role' => 'guest',
        'points' => 0,
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'member_since' => 'datetime',
            'points' => 'integer',
        ];
    }

    public function hasCompletedProfile(): bool
    {
        return !is_null($this->phone_number);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
