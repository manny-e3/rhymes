<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class CustomAuthController extends Controller
{
    /**
     * Display the registration view.
     */
    public function showRegister(): View|RedirectResponse
    {
        // If user is already authenticated, redirect to dashboard
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function register(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
        ]);

        // Assign default 'author' role
        $user->assignRole('author');

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('verification.notice', absolute: false))->with('success', 'Welcome to Rhymes Platform! Please verify your email address to continue.');
    }

    /**
     * Display the login view.
     */
    public function showLogin(): View|RedirectResponse
    {
        // If user is already authenticated, redirect to dashboard
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        // First, validate credentials without actually logging in
        $credentials = $request->only('email', 'password');
        if (!Auth::validate($credentials)) {
            // This will trigger the normal authentication failure
            $request->authenticate();
            // We shouldn't reach here, but just in case:
            return back()->withErrors([
                'email' => 'Invalid credentials.',
            ]);
        }

        // Get the user
        $user = User::where('email', $request->email)->first();

        // Check if the user account is active
        if (!$user->isActive()) {
            return back()->withErrors([
                'email' => 'Your account has been deactivated. Please contact support.',
            ]);
        }

        // Generate and send OTP
        $user->generateOTP();

        // Store pending login data in session
        $request->session()->put('pending_login_email', $request->email);
        $request->session()->put('pending_login_remember', $request->boolean('remember'));

        // Redirect to OTP verification page
        return redirect()->route('otp.show');
    }

    /**
     * Destroy an authenticated session.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'You have been logged out successfully.');
    }

    /**
     * Show user profile for editing
     */
    public function showProfile(): View
    {
        return view('profile.edit', [
            'user' => Auth::user()
        ]);
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'payment_details' => ['nullable', 'array'],
            'payment_details.bank_name' => ['nullable', 'string', 'max:255'],
            'payment_details.account_number' => ['nullable', 'string', 'max:255'],
        ]);

        // Handle payment details
        if ($request->has('payment_details')) {
            $paymentDetails = array_filter($validated['payment_details']);
            $validated['payment_details'] = !empty($paymentDetails) ? $paymentDetails : null;
        }

        $user->update($validated);

        return back()->with('success', 'Profile updated successfully!');
    }

    /**
     * Update user password
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Password updated successfully!');
    }

    /**
     * Delete user account
     */
    public function deleteAccount(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Your account has been deleted successfully.');
    }
}