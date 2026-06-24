<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Book;
use Illuminate\Support\Facades\Log;

class BookEditedForApproval extends Notification implements ShouldQueue
{
    use Queueable;

    public $book;
    public $author;

    /**
     * Create a new notification instance.
     */
    public function __construct(Book $book)
    {
        $this->book = $book;
        $this->author = $book->user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        Log::info('BookEditedForApproval: Determining notification channels', [
            'book_id' => $this->book->id,
            'book_title' => $this->book->title,
            'user_id' => $notifiable->id,
            'user_name' => $notifiable->name,
        ]);
        
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        Log::info('BookEditedForApproval: Preparing email notification', [
            'book_id' => $this->book->id,
            'book_title' => $this->book->title,
            'user_id' => $notifiable->id,
            'user_name' => $notifiable->name,
        ]);
        
        return (new MailMessage)
            ->subject('Book Edited: Approval Required - ' . $this->book->title)
            ->view('emails.book-edited-for-approval', [
                'user' => $notifiable,
                'book' => $this->book,
                'author' => $this->author,
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        Log::info('BookEditedForApproval: Preparing database notification', [
            'book_id' => $this->book->id,
            'book_title' => $this->book->title,
            'user_id' => $notifiable->id,
            'user_name' => $notifiable->name,
        ]);
        
        return [
            'type' => 'book_edited_for_approval',
            'book_id' => $this->book->id,
            'book_title' => $this->book->title,
            'author_name' => $this->author->name,
            'message' => 'Author ' . $this->author->name . ' has edited the stocked book "' . $this->book->title . '". Please review and approve the changes.',
            'action_url' => route('admin.books.show', $this->book),
            'title' => 'Book Edited - Approval Required',
            'icon' => 'ni ni-edit'
        ];
    }
}
