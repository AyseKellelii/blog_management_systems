<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommentPolicy
{
    use HandlesAuthorization;

    /**
     * Yorum onaylama (sadece admin)
     */
    public function approve(User $user, Comment $comment)
    {
        return $user->role === 'admin';
    }

    /**
     * Yorum güncelleme
     * - Yorum sahibi veya admin olabilir
     */
    public function update(User $user, Comment $comment)
    {
        return $user->id === $comment->user_id || $user->role === 'admin';
    }

    /**
     * Yorum silme
     * - Yorum sahibi veya admin olabilir
     */
    public function delete(User $user, Comment $comment)
    {
        return $user->id === $comment->user_id || $user->role === 'admin';
    }

    /**
     * Admin özel yetkiler
     */
    public function adminOnly(User $user)
    {
        return $user->role === 'admin';
    }
}
