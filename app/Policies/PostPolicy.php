<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Post;

class PostPolicy
{
    public function update(User $user, Post $post)
    {
        return $user->role === 'admin' || $post->user_id === $user->id;
    }

    public function delete(User $user, Post $post)
    {
        return $user->role === 'admin' || $post->user_id === $user->id;
    }
}
