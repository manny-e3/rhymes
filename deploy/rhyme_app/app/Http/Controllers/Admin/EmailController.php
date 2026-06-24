<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\EmailService;
use App\Services\EnhancedEmailService;
use App\Models\EmailTemplate;
use App\Models\EmailLog;
use Illuminate\Http\Request;
use App\Models\User;

class EmailController extends Controller
{
    protected $emailService;
    protected $enhancedEmailService;

    public function __construct(EmailService $emailService, EnhancedEmailService $enhancedEmailService)
    {
        $this->emailService = $emailService;
        $this->enhancedEmailService = $enhancedEmailService;
    }

    /**
     * Show the email dashboard/index page
     */
    public function index()
    {
        $stats = $this->enhancedEmailService->getEmailStats();
        $emailLogs = $this->enhancedEmailService->getEmailLogs(10);
        
        return view('admin.emails.index', [
            'stats' => $stats,
            'emailLogs' => $emailLogs,
        ]);
    }

    /**
     * Show the form for sending bulk emails
     */
    public function create(Request $request)
    {
        $type = $request->query('type', 'bulk');
        
        $authors = User::whereHas('roles', function ($query) {
            $query->where('name', 'author');
        })->get();
        
        $allUsers = User::all();
        $templates = EmailTemplate::active()->get();
        
        return view('admin.emails.create', [
            'authors' => $authors,
            'allUsers' => $allUsers,
            'templates' => $templates,
            'type' => $type,
        ]);
    }

    /**
     * Send bulk emails (legacy method)
     */
    public function sendBulk(Request $request)
    {
        $request->validate([
            'recipients' => 'required|array',
            'recipients.*' => 'exists:users,id',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'template' => 'nullable|string'
        ]);

        $results = $this->emailService->sendBulkEmails(
            $request->recipients,
            $request->subject,
            $request->message,
            $request->template
        );

        if ($results['failed'] === 0) {
            return redirect()->route('admin.emails.index')
                ->with('success', "Emails sent successfully! {$results['sent']} sent, {$results['failed']} failed.");
        } else {
            return redirect()->route('admin.emails.index')
                ->with('warning', "Emails sent with some failures. {$results['sent']} sent, {$results['failed']} failed.");
        }
    }

    /**
     * Send email to all authors (legacy method)
     */
    public function sendToAuthors(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'template' => 'nullable|string'
        ]);

        $results = $this->emailService->sendBulkEmailsToAuthors(
            $request->subject,
            $request->message,
            $request->template
        );

        if ($results['failed'] === 0) {
            return redirect()->route('admin.emails.index')
                ->with('success', "Emails sent to all authors successfully! {$results['sent']} sent, {$results['failed']} failed.");
        } else {
            return redirect()->route('admin.emails.index')
                ->with('warning', "Emails sent to authors with some failures. {$results['sent']} sent, {$results['failed']} failed.");
        }
    }

    /**
     * Send personal email to a specific user
     */
    public function sendPersonal(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'template' => 'nullable|string'
        ]);

        $user = User::findOrFail($request->user_id);
        $success = $this->emailService->sendPersonalEmail(
            $user,
            $request->subject,
            $request->message,
            $request->template
        );

        if ($success) {
            return redirect()->route('admin.emails.index')
                ->with('success', "Personal email sent to {$user->name} successfully!");
        } else {
            return redirect()->route('admin.emails.index')
                ->with('error', "Failed to send personal email to {$user->name}.");
        }
    }

    /**
     * Show form for sending to specific user
     */
    public function showPersonalForm($userId = null)
    {
        $users = User::all();
        $selectedUser = $userId ? User::find($userId) : null;
        
        return view('admin.emails.personal', [
            'users' => $users,
            'selectedUser' => $selectedUser
        ]);
    }

    /**
     * Send newsletter
     */
    public function sendNewsletter(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'author_ids' => 'nullable|array',
            'author_ids.*' => 'exists:users,id',
        ]);

        try {
            $emailLog = $this->enhancedEmailService->sendNewsletter(
                $request->title,
                $request->content,
                $request->author_ids
            );

            return redirect()->route('admin.emails.index')
                ->with('success', "Newsletter queued successfully! It will be sent to {$emailLog->total_recipients} authors.");
        } catch (\Exception $e) {
            return redirect()->route('admin.emails.index')
                ->with('error', "Failed to send newsletter: {$e->getMessage()}");
        }
    }

    /**
     * Send announcement
     */
    public function sendAnnouncement(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'author_ids' => 'nullable|array',
            'author_ids.*' => 'exists:users,id',
        ]);

        try {
            $emailLog = $this->enhancedEmailService->sendAnnouncement(
                $request->title,
                $request->content,
                $request->author_ids
            );

            return redirect()->route('admin.emails.index')
                ->with('success', "Announcement queued successfully! It will be sent to {$emailLog->total_recipients} authors.");
        } catch (\Exception $e) {
            return redirect()->route('admin.emails.index')
                ->with('error', "Failed to send announcement: {$e->getMessage()}");
        }
    }

    /**
     * Send sales report to specific author
     */
    public function sendSalesReport(Request $request)
    {
        $request->validate([
            'author_id' => 'required|exists:users,id',
            'period' => 'nullable|string|max:255',
        ]);

        try {
            $success = $this->enhancedEmailService->sendSalesReport(
                $request->author_id,
                $request->period ?? 'This Month'
            );

            if ($success) {
                $author = User::find($request->author_id);
                return redirect()->route('admin.emails.index')
                    ->with('success', "Sales report sent to {$author->name} successfully!");
            } else {
                return redirect()->route('admin.emails.index')
                    ->with('error', "Failed to send sales report.");
            }
        } catch (\Exception $e) {
            return redirect()->route('admin.emails.index')
                ->with('error', "Failed to send sales report: {$e->getMessage()}");
        }
    }

    /**
     * Send sales reports to all authors
     */
    public function sendBulkSalesReports(Request $request)
    {
        $request->validate([
            'period' => 'nullable|string|max:255',
            'author_ids' => 'nullable|array',
            'author_ids.*' => 'exists:users,id',
        ]);

        try {
            $results = $this->enhancedEmailService->sendBulkSalesReports(
                $request->period ?? 'This Month',
                $request->author_ids
            );

            return redirect()->route('admin.emails.index')
                ->with('success', "Sales reports sent! {$results['sent']} sent, {$results['failed']} failed out of {$results['total']} total.");
        } catch (\Exception $e) {
            return redirect()->route('admin.emails.index')
                ->with('error', "Failed to send sales reports: {$e->getMessage()}");
        }
    }

    /**
     * Show email log details
     */
    public function showLog($id)
    {
        $emailLog = EmailLog::with(['sender', 'template'])->findOrFail($id);
        
        return view('admin.emails.log-details', [
            'emailLog' => $emailLog,
        ]);
    }

    /**
     * Show all email logs
     */
    public function logs()
    {
        $emailLogs = $this->enhancedEmailService->getEmailLogs(20);
        
        return view('admin.emails.logs', [
            'emailLogs' => $emailLogs,
        ]);
    }
}