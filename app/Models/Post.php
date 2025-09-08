<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'content',
        'cover_image', // Dosya yolu burada saklanacak
        'status',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    // Kategorilerle ilişki (many-to-many)
    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    // Cover image için full URL üret
    public function getCoverImageUrlAttribute()
    {
        return $this->cover_image
            ? asset('storage/' . $this->cover_image)
            : null;
    }

    // Kullanıcı ile ilişki
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
