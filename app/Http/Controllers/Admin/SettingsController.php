<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index()
    {
        // Get current settings from database
        $settings = [
            'site_description' => Setting::get('site_description', ''),
            'contact_email' => Setting::get('contact_email', ''),
            'support_email' => Setting::get('support_email', ''),
            'min_payout_amount' => Setting::get('min_payout_amount', 50),
            'payout_frequency_days' => Setting::get('payout_frequency_days', 30),
            'payout_processing_time_min' => Setting::get('payout_processing_time_min', 3),
            'payout_processing_time_max' => Setting::get('payout_processing_time_max', 5),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        // Log the incoming request data for debugging
        Log::info('Settings update request received', [
            'input_data' => $request->all()
        ]);

        try {
            $validated = $request->validate([
                'site_name' => 'required|string|max:255',
                'site_url' => 'required|url',
                'site_description' => 'nullable|string|max:1000',
                'contact_email' => 'required|email',
                'support_email' => 'required|email',
                'min_payout_amount' => 'required|numeric|min:1',
                'payout_frequency_days' => 'required|integer|min:1|max:365',
                'payout_processing_time_min' => 'required|integer|min:1|max:30',
                'payout_processing_time_max' => 'required|integer|min:1|max:30',
            ]);

            Log::info('Settings validation passed', ['validated_data' => $validated]);

            // Additional validation to ensure max is >= min
            if ($validated['payout_processing_time_max'] < $validated['payout_processing_time_min']) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Maximum processing time must be greater than or equal to minimum processing time.'
                    ], 422);
                }
                return back()->withErrors([
                    'payout_processing_time_max' => 'Maximum processing time must be greater than or equal to minimum processing time.'
                ])->withInput();
            }

            // Update environment variables
            $this->updateEnvFile([
                'APP_NAME' => '"' . addslashes($validated['site_name']) . '"',
                'APP_URL' => $validated['site_url'],
            ]);

            // Store other settings in database
            Setting::set('site_description', $validated['site_description']);
            Setting::set('contact_email', $validated['contact_email']);
            Setting::set('support_email', $validated['support_email']);
            Setting::set('min_payout_amount', $validated['min_payout_amount']);
            Setting::set('payout_frequency_days', $validated['payout_frequency_days']);
            Setting::set('payout_processing_time_min', $validated['payout_processing_time_min']);
            Setting::set('payout_processing_time_max', $validated['payout_processing_time_max']);

            // Clear cache to apply new settings
            Artisan::call('config:clear');
            Artisan::call('cache:clear');

            Log::info('Settings updated successfully in database');

            // Check if this is an AJAX request
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Settings updated successfully!'
                ]);
            }

            // For traditional form submission
            return back()->with('success', 'Settings updated successfully!');

        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Settings update failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'input' => $request->except(['_token'])
            ]);

            // Check if this is an AJAX request
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update settings: ' . $e->getMessage()
                ], 500);
            }

            // For traditional form submission
            return back()->withErrors([
                'error' => 'Failed to update settings: ' . $e->getMessage()
            ])->withInput();
        }
    }

    public function clearCache()
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('view:clear');
            Artisan::call('route:clear');

            return response()->json([
                'success' => true,
                'message' => 'Cache cleared successfully!'
            ]);

        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Cache clear failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cache: ' . $e->getMessage()
            ], 500);
        }
    }

    public function testEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        try {
            Mail::raw('This is a test email from your Rhymes Platform admin panel.', function ($message) use ($request) {
                $message->to($request->email)
                        ->subject('Test Email - Rhymes Platform');
            });

            return response()->json([
                'success' => true,
                'message' => 'Test email sent successfully!'
            ]);

        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Test email failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'email' => $request->email
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to send test email: ' . $e->getMessage()
            ], 500);
        }
    }

    private function updateEnvFile($data)
    {
        $envFile = base_path('.env');
        
        // Check if file exists and is writable
        if (!file_exists($envFile)) {
            throw new \Exception('Environment file (.env) not found');
        }
        
        if (!is_writable($envFile)) {
            throw new \Exception('Environment file (.env) is not writable. Please check file permissions.');
        }
        
        $str = file_get_contents($envFile);
        
        if ($str === false) {
            throw new \Exception('Failed to read environment file');
        }

        foreach ($data as $key => $value) {
            // Escape special characters in the value properly
            $escapedValue = '"' . addslashes(trim($value, '"')) . '"';
            
            // Check if the key already exists
            if (preg_match("/^{$key}=.*/m", $str)) {
                $str = preg_replace("/^{$key}=.*/m", "{$key}={$escapedValue}", $str);
            } else {
                // If key doesn't exist, append it to the file
                $str .= "\n{$key}={$escapedValue}";
            }
        }

        $result = file_put_contents($envFile, $str);
        
        if ($result === false) {
            throw new \Exception('Failed to write to environment file');
        }
    }
}