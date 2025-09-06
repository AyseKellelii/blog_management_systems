<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Mass assignable fields
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'role',
    ];

    /**
     * Hidden fields (JSON çıktısında gözükmesin)
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Type casting
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Helper functions (rol kontrolü için)
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isAuthor(): bool
    {
        return $this->role === 'author';
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }
}
