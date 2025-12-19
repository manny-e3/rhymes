<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Book;
use Illuminate\Support\Facades\Log;

class BookStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public $book;
    public $oldStatus;
    public $newStatus;

    /**
     * Create a new notification instance.
     */
    public function __construct(Book $book, $oldStatus, $newStatus)
    {
        $this->book = $book;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        Log::info('BookStatusChanged: Determining notification channels', [
            'book_id' => $this->book->id,
            'book_title' => $this->book->title,
            'user_id' => $notifiable->id,
            'user_name' => $notifiable->name,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
        ]);
        
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        Log::info('BookStatusChanged: Preparing email notification', [
            'book_id' => $this->book->id,
            'book_title' => $this->book->title,
            'user_id' => $notifiable->id,
            'user_name' => $notifiable->name,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
        ]);
        
        return (new MailMessage)
            ->subject('Book Status Update: ' . $this->book->title)
            ->view('emails.book-status-changed', [
                'user' => $notifiable,
                'book' => $this->book,
                'newStatus' => $this->newStatus,
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        Log::info('BookStatusChanged: Preparing database notification', [
            'book_id' => $this->book->id,
            'book_title' => $this->book->title,
            'user_id' => $notifiable->id,
            'user_name' => $notifiable->name,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
        ]);
        
        return [
            'type' => 'book_status_changed',
            'book_id' => $this->book->id,
            'book_title' => $this->book->title,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'message' => $this->getStatusMessage(),
            'action_url' => $this->getActionUrl(),
        ];
    }

    private function getStatusMessage()
    {
        switch ($this->newStatus) {
            case 'pending_review':
                return 'Your book "' . $this->book->title . '" has been submitted and is pending review.';
            case 'send_review_copy':
                return 'A review copy of your book "' . $this->book->title . '" has been requested. Please check your dashboard for details.';
            case 'rejected':
                return 'Your book "' . $this->book->title . '" was not accepted. You can edit and resubmit.';
            case 'approved_awaiting_delivery':
                return 'Your book "' . $this->book->title . '" status has been changed to Send Review Copy. This status notifies you to deliver physical copies of the book.';
            case 'stocked':
                return 'Your book "' . $this->book->title . '" approved. Great News! Your book is now available in our inventory. Sales tracking is now active and you can monitor your earnings.';
            default:
                return 'Your book "' . $this->book->title . '" status changed to ' . str_replace('_', ' ', $this->newStatus);
        }
    }

    private function getActionUrl()
    {
        switch ($this->newStatus) {
            case 'pending_review':
            case 'send_review_copy':
            case 'approved_awaiting_delivery':
            case 'stocked':
                return route('dashboard');
            case 'rejected':
                return route('author.books.edit', $this->book);
            default:
                return route('author.books.show', $this->book);
        }
    }
    
    /**
     * Handle successful notification sending
     */
    public function afterCommit()
    {
        Log::info('BookStatusChanged: Notification sent successfully', [
            'book_id' => $this->book->id,
            'book_title' => $this->book->title,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
        ]);
    }
    
    /**
     * Handle failed notification sending
     */
    public function failed(\Exception $exception)
    {
        Log::error('BookStatusChanged: Notification failed to send', [
            'book_id' => $this->book->id,
            'book_title' => $this->book->title,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'exception_class' => get_class($exception),
            'exception_message' => $exception->getMessage(),
            'exception_trace' => $exception->getTraceAsString(),
        ]);
    }
}