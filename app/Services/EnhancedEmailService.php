<?php

namespace App\Services;

use App\Models\User;
use App\Models\Book;
use App\Models\EmailLog;
use App\Models\EmailTemplate;
use App\Notifications\EmailNotification;
use App\Jobs\SendBulkEmailJob;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class EnhancedEmailService
{
    /**
     * Send newsletter to authors
     */
    public function sendNewsletter(string $title, string $content, array $authorIds = null): EmailLog
    {
        $template = EmailTemplate::where('name', 'newsletter')->firstOrFail();
        
        // Get recipients
        $query = User::whereHas('roles', function ($q) {
            $q->where('name', 'author');
        });
        
        if ($authorIds) {
            $query->whereIn('id', $authorIds);
        }
        
        $recipients = $query->get();
        
        // Create email log
        $emailLog = EmailLog::create([
            'type' => 'newsletter',
            'sent_by' => auth()->id(),
            'template_id' => $template?->id,
            'subject' => str_replace('{{newsletter_title}}', $title, $template->subject),
            'content' => $content,
            'recipients' => $recipients->pluck('id')->toArray(),
            'total_recipients' => $recipients->count(),
            'status' => 'pending',
            'metadata' => [
                'newsletter_title' => $title,
            ],
        ]);
        
        // Dispatch job to send emails
        SendBulkEmailJob::dispatch($emailLog->id, [
            'newsletter_title' => $title,
            'newsletter_content' => $content,
        ]);
        
        return $emailLog;
    }

    /**
     * Send announcement to authors
     */
    public function sendAnnouncement(string $title, string $content, array $authorIds = null): EmailLog
    {
        $template = EmailTemplate::where('name', 'announcement')->firstOrFail();
        
        // Get recipients
        $query = User::whereHas('roles', function ($q) {
            $q->where('name', 'author');
        });
        
        if ($authorIds) {
            $query->whereIn('id', $authorIds);
        }
        
        $recipients = $query->get();
        
        // Create email log
        $emailLog = EmailLog::create([
            'type' => 'announcement',
            'sent_by' => auth()->id(),
            'template_id' => $template?->id,
            'subject' => str_replace('{{announcement_title}}', $title, $template->subject),
            'content' => $content,
            'recipients' => $recipients->pluck('id')->toArray(),
            'total_recipients' => $recipients->count(),
            'status' => 'pending',
            'metadata' => [
                'announcement_title' => $title,
            ],
        ]);
        
        // Dispatch job to send emails
        SendBulkEmailJob::dispatch($emailLog->id, [
            'announcement_title' => $title,
            'announcement_content' => $content,
        ]);
        
        return $emailLog;
    }

    /**
     * Send sales performance report to a specific author
     */
    public function sendSalesReport(int $authorId, string $period = 'This Month'): bool
    {
        $author = User::findOrFail($authorId);
        $template = EmailTemplate::where('name', 'sales_report')->firstOrFail();
        
        // Get author's sales data
        $salesData = $this->getAuthorSalesData($author);
        
        // Generate book details HTML
        $bookDetailsHtml = $this->generateBookDetailsHtml($salesData['books']);
        
        // Prepare template variables
        $variables = [
            'author_name' => $author->name,
            'period' => $period,
            'total_books' => $salesData['total_books'],
            'total_sales' => $salesData['total_sales'],
            'total_revenue' => number_format($salesData['total_revenue'], 2),
            'wallet_balance' => number_format($salesData['wallet_balance'], 2),
            'book_details' => $bookDetailsHtml,
        ];
        
        // Create email log
        $emailLog = EmailLog::create([
            'type' => 'sales_report',
            'sent_by' => auth()->id(),
            'template_id' => $template?->id,
            'subject' => $template->renderSubject($variables),
            'content' => $template->render($variables),
            'recipients' => [$author->id],
            'total_recipients' => 1,
            'status' => 'pending',
            'metadata' => [
                'period' => $period,
                'sales_data' => $salesData,
            ],
        ]);
        
        try {
            // Send email immediately for sales reports
            $author->notify(new EmailNotification(
                $emailLog->subject,
                $emailLog->content
            ));
            
            $emailLog->update([
                'status' => 'completed',
                'sent_count' => 1,
                'completed_at' => now(),
            ]);
            
            Log::info('Sales report sent successfully', [
                'author_id' => $author->id,
                'email_log_id' => $emailLog->id,
            ]);
            
            return true;
        } catch (\Exception $e) {
            $emailLog->update([
                'status' => 'failed',
                'failed_count' => 1,
                'metadata' => array_merge($emailLog->metadata ?? [], [
                    'error' => $e->getMessage(),
                ]),
            ]);
            
            Log::error('Failed to send sales report', [
                'author_id' => $author->id,
                'error' => $e->getMessage(),
            ]);
            
            return false;
        }
    }

    /**
     * Send sales reports to all authors
     */
    public function sendBulkSalesReports(string $period = 'This Month', array $authorIds = null): array
    {
        $query = User::whereHas('roles', function ($q) {
            $q->where('name', 'author');
        });
        
        if ($authorIds) {
            $query->whereIn('id', $authorIds);
        }
        
        $authors = $query->get();
        
        $results = [
            'sent' => 0,
            'failed' => 0,
            'total' => $authors->count(),
        ];
        
        foreach ($authors as $author) {
            if ($this->sendSalesReport($author->id, $period)) {
                $results['sent']++;
            } else {
                $results['failed']++;
            }
        }
        
        return $results;
    }

    /**
     * Get author's sales data
     */
    private function getAuthorSalesData(User $author): array
    {
        $books = Book::where('user_id', $author->id)
            ->where('status', 'stocked')
            ->get();
        
        $totalSales = 0;
        $totalRevenue = 0;
        $bookData = [];
        
        foreach ($books as $book) {
            $salesCount = $book->getSalesCount();
            $salesAmount = $book->getTotalSales();
            
            $totalSales += $salesCount;
            $totalRevenue += $salesAmount;
            
            $bookData[] = [
                'title' => $book->title,
                'sales_count' => $salesCount,
                'sales_amount' => $salesAmount,
                'price' => $book->price,
            ];
        }
        
        // Get wallet balance
        $walletBalance = method_exists($author, 'getWalletBalance') ? $author->getWalletBalance() : 0;
        
        return [
            'total_books' => $books->count(),
            'total_sales' => $totalSales,
            'total_revenue' => $totalRevenue,
            'wallet_balance' => $walletBalance,
            'books' => $bookData,
        ];
    }

    /**
     * Generate HTML for book details
     */
    private function generateBookDetailsHtml(array $books): string
    {
        if (empty($books)) {
            return '<p style="color: #666;">No sales data available yet.</p>';
        }
        
        $html = '<table style="width: 100%; border-collapse: collapse; margin-top: 10px;">';
        $html .= '<thead>';
        $html .= '<tr style="background: #f8f9fa;">';
        $html .= '<th style="padding: 10px; text-align: left; border-bottom: 2px solid #dee2e6;">Book Title</th>';
        $html .= '<th style="padding: 10px; text-align: center; border-bottom: 2px solid #dee2e6;">Sales</th>';
        $html .= '<th style="padding: 10px; text-align: right; border-bottom: 2px solid #dee2e6;">Revenue</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';
        
        foreach ($books as $book) {
            $html .= '<tr>';
            $html .= '<td style="padding: 10px; border-bottom: 1px solid #dee2e6;">' . htmlspecialchars($book['title']) . '</td>';
            $html .= '<td style="padding: 10px; text-align: center; border-bottom: 1px solid #dee2e6;">' . $book['sales_count'] . '</td>';
            $html .= '<td style="padding: 10px; text-align: right; border-bottom: 1px solid #dee2e6;">₦' . number_format($book['sales_amount'], 2) . '</td>';
            $html .= '</tr>';
        }
        
        $html .= '</tbody>';
        $html .= '</table>';
        
        return $html;
    }

    /**
     * Get email logs with pagination
     */
    public function getEmailLogs(int $perPage = 15)
    {
        return EmailLog::with(['sender', 'template'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get email statistics
     */
    public function getEmailStats(): array
    {
        $totalSent = EmailLog::where('status', 'completed')->sum('sent_count');
        $totalFailed = EmailLog::where('status', 'failed')->sum('failed_count');
        $pendingEmails = EmailLog::where('status', 'pending')->count();
        
        $recentLogs = EmailLog::orderBy('created_at', 'desc')->take(5)->get();
        
        return [
            'total_sent' => $totalSent,
            'total_failed' => $totalFailed,
            'pending_emails' => $pendingEmails,
            'recent_logs' => $recentLogs,
            'total_authors' => User::whereHas('roles', function ($q) {
                $q->where('name', 'author');
            })->count(),
        ];
    }
}
