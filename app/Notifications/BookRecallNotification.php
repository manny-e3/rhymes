<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Book;
use Illuminate\Support\Facades\Log;

class BookRecallNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $book;
    public $author;
    public $recallReason;

    public function __construct(Book $book, ?string $recallReason = null)
    {
        $this->book = $book;
        $this->author = $book->user;
        $this->recallReason = $recallReason;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Book Recall Request: ' . $this->book->title)
            ->view('emails.book-recall-notification', [
                'user' => $notifiable,
                'book' => $this->book,
                'author' => $this->author,
                'recallReason' => $this->recallReason,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'book_recall_request',
            'book_id' => $this->book->id,
            'book_title' => $this->book->title,
            'author_id' => $this->author->id,
            'author_name' => $this->author->name,
            'recall_reason' => $this->recallReason,
            'message' => $this->getNotificationMessage(),
            'action_url' => route('admin.books.show', $this->book),
            'title' => 'Book Recall Request',
            'icon' => 'ni ni-book'
        ];
    }

    private function getNotificationMessage()
    {
        $message = 'Author "' . $this->author->name . '" has requested to recall the book "' . $this->book->title . '"';
        
        if ($this->recallReason) {
            $message .= ' with the following reason: "' . $this->recallReason . '"';
        }
        
        $message .= '.';
        
        return $message;
    }
}