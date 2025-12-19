<?php

namespace App\Http\Controllers\Author;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\WalletService;

class WalletController extends Controller
{
    public function __construct(
        private WalletService $walletService
    ) {
        $this->middleware(['auth', 'role:author|admin']);
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        
        $filters = $request->only(['type', 'date_from', 'date_to']);
        $walletData = $this->walletService->getWalletOverview($user, $filters);

        return view('author.wallet.index', [
            'balance' => $walletData['balance'],
            'transactions' => $walletData['transactions'],
            'salesByBook' => $walletData['salesByBook'],
            'analytics' => $walletData['analytics'],
        ]);
    }


    /**
     * Export wallet transactions
     */
    public function export(Request $request)
    {
        $user = auth()->user();
        $filters = $request->only(['type', 'date_from', 'date_to']);
        
        $exportData = $this->walletService->exportTransactions($user, $filters);
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $exportData['filename'] . '"',
        ];

        $callback = function() use ($exportData) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, $exportData['headers']);
            
            foreach ($exportData['data'] as $row) {
                fputcsv($file, $row);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
