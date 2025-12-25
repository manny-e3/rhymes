<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Payout;

class PayoutStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public $payout;
    public $oldStatus;
    public $newStatus;
    public $adminNotes;

    /**
     * Create a new notification instance.
     */
    public function __construct(Payout $payout, $oldStatus, $newStatus, $adminNotes = null)
    {
        $this->payout = $payout;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
        $this->adminNotes = $adminNotes;
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
        $subject = 'Payout Request Update - ₦' . number_format($this->payout->amount_requested, 2);
        
        return (new MailMessage)
            ->subject($subject)
            ->view('emails.payout-status-changed', [
                'user' => $notifiable,
                'payout' => $this->payout,
                'newStatus' => $this->newStatus,
                'adminNotes' => $this->adminNotes,
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
            'type' => 'payout_status_changed',
            'payout_id' => $this->payout->id,
            'amount' => $this->payout->amount_requested,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'message' => $this->getStatusMessage(),
            'action_url' => route('author.payouts.index'),
            'title' => 'Payout Status Changed',
            'icon' => 'ni ni-wallet-out'
        ];
    }

    private function getStatusMessage()
    {
        $amount = '₦' . number_format($this->payout->amount_requested, 2);
        
        switch ($this->newStatus) {
            case 'approved':
                return 'Your payout request of ' . $amount . ' has been approved and will be processed soon.';
            case 'denied':
                return 'Your payout request of ' . $amount . ' was denied. You can submit a new request.';
            case 'completed':
                return 'Your payout of ' . $amount . ' has been completed and sent to your account.';
            default:
                return 'Your payout request of ' . $amount . ' status changed to ' . $this->newStatus;
        }
    }
}