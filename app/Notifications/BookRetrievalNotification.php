<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Book;
use Illuminate\Support\Facades\Log;

class BookRetrievalNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $book;
    public $author;
    public $reason;

    public function __construct(Book $book, ?string $reason = null)
    {
        $this->book = $book;
        $this->author = $book->user;
        $this->reason = $reason;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Book Retrieval Request: ' . $this->book->title)
            ->view('emails.book-retrieval-notification', [
                'user' => $notifiable,
                'book' => $this->book,
                'author' => $this->author,
                'reason' => $this->reason,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'book_retrieval_request',
            'book_id' => $this->book->id,
            'book_title' => $this->book->title,
            'author_id' => $this->author->id,
            'author_name' => $this->author->name,
            'retrieval_reason' => $this->reason,
            'message' => $this->getNotificationMessage(),
            'action_url' => route('admin.books.show', $this->book),
            'title' => 'Book Retrieval Request',
            'icon' => 'ni ni-book'
        ];
    }

    private function getNotificationMessage()
    {
        $message = 'Author "' . $this->author->name . '" has requested to retrieve the book "' . $this->book->title . '"';
        
        if ($this->reason) {
            $message .= ' with the following reason: "' . $this->reason . '"';
        }
        
        $message .= '.';
        
        return $message;
    }
}