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
            'delivered' => Transaction::where('user_id', $user->id)->where('status', Transaction::STATUS_DELIVERED)->count(),
            'completed' => Transaction::where('user_id', $user->id)->whereIn('status', [Transaction::STATUS_DELIVERED, Transaction::STATUS_COMPLETED])->count(),
        ];

        $activeVouchers = $user->vouchers()
            ->whereNull('transaction_id')
            ->with('base_voucher')
            ->paginate(9, ['*'], 'voucher_page');

        $voucherData = [
            'vouchers' => $activeVouchers,
            'activeCount' => $activeVouchers->total(),
            'totalDiscount' => $user->vouchers()
                ->whereNull('transaction_id')
                ->with('base_voucher')
                ->get()
                ->sum(fn($v) => $v->base_voucher->disc_amt),
        ];

        return view('dashboard.user.index', [
            'transactions' => $transactions,
            'statusCounts' => $statusCounts,
            'currentStatus' => $status,
            'voucherData' => $voucherData, 
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