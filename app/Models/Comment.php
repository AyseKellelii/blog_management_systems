<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Comment extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    /**
     * Mass assignable fields
     */
    protected $fillable = [
        'user_id',
        'post_id',
        'content',
        'approved', // Admin onayı
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()      // Sadece değişen alanları loglar
            ->logFillable()       // Fillable alanları logla
            ->useLogName('comment');
    }

    /**
     * Casts
     */
    protected $casts = [
        'approved' => 'boolean',
    ];

    /**
     * Relationships
     */

    // Yorumun yazarı
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Yorumun ait olduğu post
    public function post()
    {
        return $this->belongsTo(Post::class);
    }
    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Helper fonksiyonlar
     */

    // Yorum onaylandı mı?
    public function isApproved()
    {
        return $this->approved === true;
    }

    // Yorum onayla
    public function approve()
    {
        $this->approved = true;
        $this->save();
    }

    // Yorum onayı kaldır
    public function disapprove()
    {
        $this->approved = false;
        $this->save();
    }
}
