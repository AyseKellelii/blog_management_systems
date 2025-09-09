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


    // Kullanıcı ile ilişki
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Kategorilerle ilişki (many-to-many)
    public function categories() {
        return $this->belongsToMany(Category::class, 'category_post', 'post_id', 'category_id');
    }

    // Cover image için full URL üret
    public function getCoverImageUrlAttribute()
    {
        return $this->cover_image
            ? asset('storage/' . $this->cover_image)
            : null;
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }



}
