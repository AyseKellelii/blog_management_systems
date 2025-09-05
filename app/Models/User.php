<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

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
     * Hidden fields for arrays
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casts
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Relationships
     */

    // Kullanıcının yazdığı blog yazıları
    public function posts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Post::class);
    }

    // Kullanıcının yaptığı yorumlar
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // Kullanıcının bildirimleri
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Helper functions
     */

    // Kullanıcı admin mi?
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    // Kullanıcı yazar mı?
    public function isAuthor()
    {
        return $this->role === 'author';
    }

    // Kullanıcı normal kullanıcı mı?
    public function isUser()
    {
        return $this->role === 'user';
    }
}
