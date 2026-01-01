<?php

namespace App\Http\Controllers;

use App\Models\BaseVoucher;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VoucherController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $activeVouchers = $user->vouchers()
            ->whereNull('transaction_id')
            ->with('base_voucher')
            ->paginate(9);
        
        // Hitung total diskon yang tersedia
        $totalDiscount = $activeVouchers->sum(fn($v) => $v->base_voucher->disc_amt);
        
        return view('dashboard.user.vouchers.index', [
            'activeVouchers' => $activeVouchers,
            'totalDiscount' => $totalDiscount,
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
