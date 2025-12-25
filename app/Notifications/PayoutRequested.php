<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Payout;

class PayoutRequested extends Notification implements ShouldQueue
{
    use Queueable;

    public $payout;

    /**
     * Create a new notification instance.
     */
    public function __construct(Payout $payout)
    {
        $this->payout = $payout->load('user');
    }

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
        $subject = 'New Payout Request - ₦' . number_format($this->payout->amount_requested, 2);
        
        return (new MailMessage)
            ->subject($subject)
            ->view('emails.admin-payout-requested', [
                'admin' => $notifiable,
                'payout' => $this->payout,
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
            'type' => 'payout_requested',
            'payout_id' => $this->payout->id,
            'author_name' => $this->payout->user->name,
            'amount_requested' => $this->payout->amount_requested,
            'message' => 'New payout request of ₦' . number_format($this->payout->amount_requested, 2) . ' from ' . $this->payout->user->name,
            'action_url' => url('/admin/payouts/' . $this->payout->id),
            'title' => 'New Payout Request',
            'icon' => 'ni ni-wallet-out'
        ];
    }
}