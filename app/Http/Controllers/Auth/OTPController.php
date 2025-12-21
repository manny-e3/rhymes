<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class OTPController extends Controller
{
    /**
     * Show the OTP verification view.
     *
     * @return \Illuminate\View\View
     */
    public function showOTPForm(Request $request)
    {
        // Check if we have a pending login attempt
        if (!$request->session()->has('pending_login_email')) {
            return redirect()->route('login');
        }

        return view('auth.otp');
    }

    /**
     * Verify the OTP code.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verifyOTP(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'string', 'size:6'],
        ]);

        // Get the pending login email from session
        $email = $request->session()->get('pending_login_email');
        
        if (!$email) {
            return redirect()->route('login')->with('error', 'Invalid login attempt.');
        }

        // Find the user
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Invalid login attempt.');
        }

        // Verify OTP
        if (!$user->verifyOTP($request->otp)) {
            throw ValidationException::withMessages([
                'otp' => ['The OTP code is invalid or has expired.'],
            ]);
        }

        // Clear OTP
        $user->clearOTP();

        // Authenticate the user
        Auth::login($user, $request->session()->get('pending_login_remember', false));

        // Clear session data
        $request->session()->forget(['pending_login_email', 'pending_login_remember']);

        // Check if user has verified their email
        if (!$user->hasVerifiedEmail()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return redirect()->route('login')->with('error', 'You must verify your email address before logging in. Please check your email for the verification link.');
        }
        
        // Redirect based on user role
        if ($user->hasRole('admin')) {
            return redirect()->intended(route('dashboard'))->with('success', 'Welcome back, Admin!');
        } elseif ($user->hasRole('author')) {
            return redirect()->intended(route('dashboard'))->with('success', 'Welcome back to your Author Dashboard!');
        } else {
            return redirect()->intended(route('dashboard'))->with('success', 'Welcome back! Ready to submit your next book?');
        }
    }

    /**
     * Resend OTP code.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resendOTP(Request $request)
    {
        // Get the pending login email from session
        $email = $request->session()->get('pending_login_email');
        
        if (!$email) {
            return redirect()->route('login')->with('error', 'Invalid login attempt.');
        }

        // Find the user
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Invalid login attempt.');
        }

        // Generate new OTP
        $user->generateOTP();

        return back()->with('success', 'A new OTP code has been sent to your email.');
    }
    
    /**
     * Show the OTP verification view for payout requests.
     *
     * @return \Illuminate\View\View
     */
    public function showPayoutOTPForm(Request $request)
    {
        // Check if we have a pending payout request
        if (!$request->session()->has('pending_payout_data')) {
            return redirect()->route('author.payouts.index');
        }

        return view('auth.otp-payout');
    }

    /**
     * Verify the OTP code for payout requests.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verifyPayoutOTP(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'string', 'size:6'],
        ]);

        // Get the authenticated user
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Authentication required.');
        }

        // Verify OTP
        if (!$user->verifyOTP($request->otp)) {
            throw ValidationException::withMessages([
                'otp' => ['The OTP code is invalid or has expired.'],
            ]);
        }

        // Clear OTP
        $user->clearOTP();

        // Mark OTP as verified for payout in session
        $request->session()->put('otp_verified_for_payout', true);
        
        // Get pending payout data
        $payoutData = $request->session()->get('pending_payout_data');
        $payoutUrl = $request->session()->get('pending_payout_url');
        
        // Clear session data
        $request->session()->forget(['pending_payout_data', 'pending_payout_url']);

        // Redirect to the original payout request with the data
        return redirect($payoutUrl)->with('payout_data', $payoutData);
    }

    /**
     * Resend OTP code for payout requests.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resendPayoutOTP(Request $request)
    {
        // Get the authenticated user
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Authentication required.');
        }

        // Generate new OTP
        $user->generateOTP();

        return back()->with('success', 'A new OTP code has been sent to your email.');
    }
}