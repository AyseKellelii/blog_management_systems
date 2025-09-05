<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    /**
     * Mass assignable fields
     */
    protected $fillable = [
        'user_id',
        'type',     // mail, database, broadcast
        'data',     // JSON veri
        'read',     // true/false
    ];

    /**
     * Casts
     */
    protected $casts = [
        'data' => 'array',  // JSON veri array olarak kullanılabilir
        'read' => 'boolean',
    ];

    /**
     * Relationships
     */

    // Bildirimin ait olduğu kullanıcı
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper fonksiyonlar
     */

    // Bildirimi okundu olarak işaretle
    public function markAsRead()
    {
        $this->read = true;
        $this->save();
    }

    // Bildirimi okunmadı olarak işaretle
    public function markAsUnread()
    {
        $this->read = false;
        $this->save();
    }

    // Bildirim tipi kontrolü
    public function isType($type)
    {
        return $this->type === $type;
    }
}
