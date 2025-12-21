<?php

namespace App\Http\Controllers\Author;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\PayoutService;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;

class PayoutController extends Controller
{
    public function __construct(
        private PayoutService $payoutService
    ) {
        $this->middleware(['auth', 'role:author|admin']);
        // Removed OTP middleware
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $filters = $request->only(['status']);
        
        $payoutData = $this->payoutService->getPayoutOverview($user, $filters);
        $payoutInfo = $this->payoutService->getPayoutInformation();
        
        // Debug: Log the payout info
        Log::info('Payout Info in Controller', ['payoutInfo' => $payoutInfo]);
        
        return view('author.payouts.index', [
            'payouts' => $payoutData['payouts'],
            'walletBalance' => $payoutData['walletBalance'],
            'availableBalance' => $payoutData['availableBalance'],
            'payoutStats' => $payoutData['payoutStats'],
            'payoutInfo' => $payoutInfo,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Get minimum payout amount from settings
        $minPayoutAmount = Setting::get('min_payout_amount', 300000);
        
        $validated = $request->validate([
            'amount_requested' => 'required|numeric|min:' . $minPayoutAmount,
        ]);
        
        try {
            $this->payoutService->createPayoutRequest($user, $validated);
            return back()->with('success', 'Payout request submitted successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors());
        }
    }

    /**
     * Show payment details form
     */
    public function paymentDetails()
    {
        $user = Auth::user();
        return view('author.payouts.payment-details', compact('user'));
    }

    /**
     * Update payment details
     */
    public function updatePaymentDetails(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'payment_method' => 'required|in:bank_transfer,paypal,stripe',
            'account_holder_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'paypal_email' => 'nullable|email|max:255',
            'stripe_account_id' => 'nullable|string|max:255',
        ]);

        try {
            $this->payoutService->updatePaymentDetails($user, $validated);
            return back()->with('success', 'Payment details updated successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors());
        }
    }
}