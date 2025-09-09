<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use App\Models\Comment;

class NewCommentNotification extends Notification
{
    use Queueable;

    public $comment;

    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    public function via($notifiable)
    {
        return ['database', 'mail', 'broadcast'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'comment_id' => $this->comment->id,
            'post_id' => $this->comment->post_id,
            'post_title' => $this->comment->post->title,
            'user_name' => $this->comment->user->first_name . ' ' . $this->comment->user->last_name,
            'message' => 'Yazınıza yeni bir yorum yapıldı.',
            'type' => 'comment',
        ];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Yeni Yorum Bildirimi')
            ->line("Yazınız '{$this->comment->post->title}' yeni bir yorum aldı.")
            ->action('Yorumu Gör', url("/author/posts/{$this->comment->post_id}"))
            ->line('Teşekkürler!');
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'comment_id' => $this->comment->id,
            'post_id' => $this->comment->post_id,
            'post_title' => $this->comment->post->title,
            'user_name' => $this->comment->user->first_name . ' ' . $this->comment->user->last_name,
            'message' => 'Yazınıza yeni bir yorum yapıldı.',
            'type' => 'comment',
        ]);
    }
}
