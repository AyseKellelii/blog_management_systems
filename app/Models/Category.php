<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Mass assignable fields
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    /**
     * Relationships
     */

    // Kategoriye ait olan postlar (çoktan çoğa)
    public function posts()
    {
        return $this->belongsToMany(Post::class);
    }

    /**
     * Helper fonksiyonlar
     */

    // Kategoriye ait post sayısını al
    public function postsCount()
    {
        return $this->posts()->count();
    }
}
