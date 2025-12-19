<?php

namespace App\Http\Controllers\Author;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\PayoutService;

class PayoutController extends Controller
{
    public function __construct(
        private PayoutService $payoutService
    ) {
        $this->middleware(['auth', 'role:author|admin']);
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $filters = $request->only(['status']);
        
        $payoutData = $this->payoutService->getPayoutOverview($user, $filters);
        
        return view('author.payouts.index', [
            'payouts' => $payoutData['payouts'],
            'walletBalance' => $payoutData['walletBalance'],
            'availableBalance' => $payoutData['availableBalance'],
            'payoutStats' => $payoutData['payoutStats'],
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'amount_requested' => 'required|numeric|min:300000', // Updated minimum to ₦300,000
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
        $user = auth()->user();
        return view('author.payouts.payment-details', compact('user'));
    }

    /**
     * Update payment details
     */
    public function updatePaymentDetails(Request $request)
    {
        $user = auth()->user();
        
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