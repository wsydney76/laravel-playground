<?php

namespace App\Notifications;

use App\Models\Article;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class ArticleCreated extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(protected Article $article) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('A new article has been added by ' . $this->article->user->name . ':')
            ->line($this->article->title)
            ->line('State: ' . $this->article->state->label())
            ->line(Str::limit($this->article->body, 120, preserveWords: true))
            ->action('Review it', $this->article->url);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'article_id' => $this->article->id,
            'article_slug' => $this->article->slug,
            'article_title' => $this->article->title,
            'creator_id' => $this->article->user->id,
            'creator_name' => $this->article->user->name,
        ];
    }
}
