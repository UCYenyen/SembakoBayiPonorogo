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

        $query = Transaction::with(['transaction_items.product', 'delivery', 'address'])
            ->where('user_id', $user->id)
            ->latest();

        if ($status !== 'all') {
            if ($status === 'completed') {
                $query->whereIn('status', [Transaction::STATUS_DELIVERED, Transaction::STATUS_COMPLETED]);
            } else {
                $query->where('status', $status);
            }
        }

        $transactions = $query->paginate(10);

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
        if ($transaction->user_id !== Auth::id()) {
            abort(403);
        }

        $transaction->load(['transaction_items.product', 'delivery', 'address']);

        return view('dashboard.user.transaction-detail', [
            'transaction' => $transaction,
        ]);
    }
}