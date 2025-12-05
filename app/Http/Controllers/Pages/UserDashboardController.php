<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $status = $request->get('status', 'all');

        // Base query
        $query = Transaction::with(['transaction_items.product', 'payment', 'delivery', 'address'])
            ->where('user_id', $user->id)
            ->latest();

        // Filter by status
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        // Get transactions
        $transactions = $query->paginate(10);

        // Count by status for tabs
        $statusCounts = [
            'all' => Transaction::where('user_id', $user->id)->count(),
            'pending_payment' => Transaction::where('user_id', $user->id)->where('status', Transaction::STATUS_PENDING_PAYMENT)->count(),
            'paid' => Transaction::where('user_id', $user->id)->where('status', Transaction::STATUS_PAID)->count(),
            'processing' => Transaction::where('user_id', $user->id)->where('status', Transaction::STATUS_PROCESSING)->count(),
            'shipped' => Transaction::where('user_id', $user->id)->where('status', Transaction::STATUS_SHIPPED)->count(),
            'completed' => Transaction::where('user_id', $user->id)->whereIn('status', [Transaction::STATUS_DELIVERED, Transaction::STATUS_COMPLETED])->count(),
        ];

        return view('dashboard.user.index', [
            'transactions' => $transactions,
            'statusCounts' => $statusCounts,
            'currentStatus' => $status,
        ]);
    }

    public function show(Transaction $transaction)
    {
        // Check if user owns this transaction
        if ($transaction->user_id !== Auth::id()) {
            abort(403);
        }

        $transaction->load(['transaction_items.product', 'payment', 'delivery', 'address']);

        return view('dashboard.user.transaction-detail', [
            'transaction' => $transaction,
        ]);
    }
}
