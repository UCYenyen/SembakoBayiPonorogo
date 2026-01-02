<?php

namespace App\Http\Controllers;

use App\Models\BaseVoucher;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;

class VoucherController extends Controller
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

    public function create()
    {
        $baseVouchers = BaseVoucher::orderBy('points_required', 'asc')->get();
        
        return view('dashboard.user.vouchers.create', [
            'baseVouchers' => $baseVouchers
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'base_voucher_id' => 'required|exists:base_vouchers,id'
        ]);

        $user = Auth::user();
        $baseVoucher = BaseVoucher::find($request->base_voucher_id);

        if ($user->points < $baseVoucher->points_required) {
            return redirect()->route('user.vouchers.create')
                ->with('error', 'Poin Anda tidak cukup untuk membeli voucher ini.');
        }

        $user->decrement('points', $baseVoucher->points_required);

        Voucher::create([
            'base_voucher_id' => $baseVoucher->id,
            'user_id' => $user->id,
            'transaction_id' => null,
            'shopping_cart_id' => null
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Voucher berhasil ditukar! Silakan gunakan pada pembelian berikutnya.');
    }
}
