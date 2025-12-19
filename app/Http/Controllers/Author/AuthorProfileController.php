<?php

namespace App\Http\Controllers\Author;

use App\Http\Controllers\Controller;
use App\Services\BookService;
use App\Services\WalletService;
use App\Services\PayoutService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AuthorProfileController extends Controller
{
    protected $bookService;
    protected $walletService;
    protected $payoutService;

    public function __construct(BookService $bookService, WalletService $walletService, PayoutService $payoutService)
    {
        $this->bookService = $bookService;
        $this->walletService = $walletService;
        $this->payoutService = $payoutService;
    }

    /**
     * Display the user's profile form.
     */
    public function showProfile(Request $request): View
    {
        $user = $request->user();
        
        // Get author statistics
        $totalBooks = $this->bookService->getUserBooks($user, 1000)->total(); // Get total count
        $publishedBooks = $this->bookService->getUserBooksByStatus($user, 'published')->count();
        $walletOverview = $this->walletService->getWalletOverview($user);
        
        return view('author.profile.index', [
            'user' => $user,
            'totalBooks' => $totalBooks,
            'publishedBooks' => $publishedBooks,
            'walletBalance' => $walletOverview['balance'],
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'display_name' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'address' => 'nullable|string|max:500',
            'bio' => 'nullable|string|max:1000',
            'website' => 'nullable|url|max:255',
            'social_links.facebook' => 'nullable|url|max:255',
            'social_links.twitter' => 'nullable|url|max:255',
            'social_links.instagram' => 'nullable|url|max:255',
            'social_links.linkedin' => 'nullable|url|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        
        // Update basic user fields
        $user->name = $request->name;
        $user->phone = $request->phone;
        
        // Update profile data
        $profileData = $user->profile_data ?? [];
        $profileData['display_name'] = $request->display_name;
        $profileData['date_of_birth'] = $request->date_of_birth;
        $profileData['address'] = $request->address;
        $profileData['bio'] = $request->bio;
        $profileData['website'] = $request->website;
        $profileData['social_links'] = $request->social_links ?? [];
        
        $user->profile_data = $profileData;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully!'
        ]);
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect.'
            ], 422);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully!'
        ]);
    }

    /**
     * Upload user avatar.
     */
    public function uploadAvatar(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        // Delete old avatar if exists
        if ($user->avatar && Storage::disk('public')->exists('images/avatar/' . $user->avatar)) {
            Storage::disk('public')->delete('images/avatar/' . $user->avatar);
        }

        // Store new avatar
        $file = $request->file('avatar');
        $filename = time() . '_' . $user->id . '.' . $file->getClientOriginalExtension();
        
        // Ensure directory exists
        if (!Storage::disk('public')->exists('images/avatar')) {
            Storage::disk('public')->makeDirectory('images/avatar');
        }
        
        $file->storeAs('images/avatar', $filename, 'public');
        
        $user->avatar = $filename;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Avatar updated successfully!'
        ]);
    }

    /**
     * Update payment details.
     */
    public function updatePaymentDetails(Request $request): JsonResponse|RedirectResponse
    {
        // Define base validation rules
        $rules = [
            'payment_method' => 'required|in:bank_transfer,paypal,stripe',
            'account_holder_name' => 'required|string|max:255',
        ];
        
        // Add conditional validation rules based on payment method
        switch ($request->payment_method) {
            case 'bank_transfer':
                $rules['bank_name'] = 'required|string|max:255';
                $rules['account_number'] = 'required|string|max:255';
                break;
            case 'paypal':
                $rules['paypal_email'] = 'required|email|max:255';
                break;
            case 'stripe':
                $rules['stripe_account_id'] = 'required|string|max:255';
                break;
        }
        
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            // Check if this is an AJAX request
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            } else {
                // For non-AJAX requests, redirect back with errors
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('payment-error', 'Please correct the errors below.');
            }
        }

        try {
            $user = $request->user();
            $this->payoutService->updatePaymentDetails($user, $validator->validated());

            // Check if this is an AJAX request
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Payment details updated successfully!'
                ]);
            } else {
                // For non-AJAX requests, redirect back with success message
                return redirect()->back()
                    ->with('payment-success', 'Payment details updated successfully!');
            }
        } catch (\Exception $e) {
            // Log the exception for debugging
            Log::error('Payment details update error: ' . $e->getMessage());
            
            // Check if this is an AJAX request
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while updating payment details.'
                ], 500);
            } else {
                // For non-AJAX requests, redirect back with error message
                return redirect()->back()
                    ->withInput()
                    ->with('payment-error', 'An error occurred while updating payment details.');
            }
        }
    }

    /**
     * Delete the user's account.
     */
    public function deleteAccount(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}