<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// Spatie Medialibrary
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Post extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    /**
     * Mass assignable fields
     */
    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'content',
        'status',
        'published_at',
    ];

    /**
     * Relationships
     */

    // Post'un yazarı
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Post'un kategorileri (çoktan çoğa)
    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    // Post'un yorumları
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Spatie Medialibrary - cover image için
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cover_image')->singleFile();
    }

    /**
     * Helper fonksiyonlar
     */

    // Post yayında mı?
    public function isPublished()
    {
        return $this->status === 'published';
    }

    // Post taslak mı?
    public function isDraft()
    {
        return $this->status === 'draft';
    }
}
