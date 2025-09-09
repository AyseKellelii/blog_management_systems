<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommentPolicy
{
    use HandlesAuthorization;

    // Admin yorumları onaylayabilir
    public function approve(User $user, Comment $comment)
    {
        return $user->role === 'admin';
    }

    // Admin veya yorum sahibi silebilir
    public function delete(User $user, Comment $comment)
    {
        return $user->role === 'admin' || $user->id === $comment->user_id;
    }
}
