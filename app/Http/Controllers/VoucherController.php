<?php

namespace App\Http\Controllers;

use App\Models\BaseVoucher;
use App\Models\Voucher;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public function index()
    {
        $vouchers = BaseVoucher::all();
        return view('admin.vouchers.index', compact('vouchers'));
    }
    public function create()
    {
        return view('admin.vouchers.create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'base_voucher_id' => 'required|exists:base_vouchers,id',
            'user_id' => 'required|exists:users,id',
        ]);

        Voucher::create($request->all());

        return redirect()->route('admin.vouchers.index')->with('success', 'Voucher created successfully!');
    }
    public function update(Request $request, Voucher $voucher)
    {
        $request->validate([
            'transaction_id' => 'nullable|exists:transactions,id',
            'shopping_cart_id' => 'nullable|exists:shopping_carts,id',
        ]);

        $voucher->update($request->all());

        return redirect()->route('admin.vouchers.index')->with('success', 'Voucher updated successfully!');
    }
}
