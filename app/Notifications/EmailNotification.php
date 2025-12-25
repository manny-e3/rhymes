<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailNotification extends Notification
{
    use Queueable;

    protected $subject;
    protected $message;
    protected $template;

    /**
     * Create a new notification instance.
     */
    public function __construct($subject, $message, $template = null)
    {
        $this->subject = $subject;
        $this->message = $message;
        $this->template = $template;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        // Ensure message is a string and handle potential MailMessage objects
        $messageContent = $this->message;
        
        // Extra safety check to ensure we have a string
        if (is_object($messageContent)) {
            if (get_class($messageContent) === 'Illuminate\\Mail\\Message') {
                // If it's a MailMessage object, use a default message
                $messageContent = 'Mail message object received instead of string';
            } elseif (method_exists($messageContent, '__toString')) {
                // If object has __toString method, use it
                $messageContent = (string) $messageContent;
            } else {
                // Otherwise, use class name as string
                $messageContent = get_class($messageContent);
            }
        } elseif (!is_string($messageContent)) {
            $messageContent = (string) $messageContent;
        }
        
        if ($this->template) {
            return (new MailMessage)
                ->subject($this->subject)
                ->view($this->template, [
                    'user' => $notifiable,
                    'message' => $messageContent,
                    'subject' => $this->subject,
                ]);
        }

        return (new MailMessage)
            ->subject($this->subject)
            ->view('emails.generic-email', [
                'user' => $notifiable,
                'message' => $messageContent,
                'subject' => $this->subject,
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'subject' => $this->subject,
            'message' => $this->message,
        ];
    }
}