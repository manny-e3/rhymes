<?php

namespace App\Jobs;

use App\Models\EmailLog;
use App\Models\User;
use App\Notifications\EmailNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendBulkEmailJob implements ShouldQueue
{
    use Queueable;

    public $emailLogId;
    public $templateVariables;

    /**
     * Create a new job instance.
     */
    public function __construct(int $emailLogId, array $templateVariables = [])
    {
        $this->emailLogId = $emailLogId;
        $this->templateVariables = $templateVariables;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $emailLog = EmailLog::find($this->emailLogId);
        
        if (!$emailLog) {
            Log::error('Email log not found', ['email_log_id' => $this->emailLogId]);
            return;
        }

        // Update status to processing
        $emailLog->update([
            'status' => 'processing',
            'started_at' => now(),
        ]);

        $sentCount = 0;
        $failedCount = 0;

        // Get recipients
        $recipients = User::whereIn('id', $emailLog->recipients)->get();

        // Get template if available
        $template = $emailLog->template;
        
        foreach ($recipients as $recipient) {
            try {
                // Prepare email content
                if ($template) {
                    $variables = array_merge($this->templateVariables, [
                        'name' => $recipient->name,
                        'email' => $recipient->email,
                    ]);
                    
                    $subject = $template->renderSubject($variables);
                    $content = $template->render($variables);
                } else {
                    $subject = $emailLog->subject;
                    $content = $emailLog->content;
                }

                // Send email
                $recipient->notify(new EmailNotification($subject, $content));
                
                $sentCount++;
                
                Log::info('Bulk email sent', [
                    'email_log_id' => $this->emailLogId,
                    'recipient_id' => $recipient->id,
                    'recipient_email' => $recipient->email,
                ]);
            } catch (\Exception $e) {
                $failedCount++;
                
                Log::error('Failed to send bulk email', [
                    'email_log_id' => $this->emailLogId,
                    'recipient_id' => $recipient->id,
                    'recipient_email' => $recipient->email,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Update email log
        $emailLog->update([
            'status' => $failedCount === 0 ? 'completed' : ($sentCount === 0 ? 'failed' : 'completed'),
            'sent_count' => $sentCount,
            'failed_count' => $failedCount,
            'completed_at' => now(),
        ]);

        Log::info('Bulk email job completed', [
            'email_log_id' => $this->emailLogId,
            'sent' => $sentCount,
            'failed' => $failedCount,
        ]);
    }
}
