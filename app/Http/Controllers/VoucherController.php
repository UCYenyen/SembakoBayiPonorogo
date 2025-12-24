<?php

namespace App\Http\Controllers;

use App\Models\BaseVoucher;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:base_vouchers',
            'disc_amt' => 'required|numeric|min:0',
            'points_required' => 'required|numeric|min:0',
        ]);

        BaseVoucher::create($request->all());

        return redirect()->route('admin.vouchers.index')->with('success', 'Voucher created successfully!');
    }

    public function update(Request $request, BaseVoucher $baseVoucher)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:base_vouchers,name,' . $baseVoucher->id,
            'disc_amt' => 'required|numeric|min:0',
            'points_required' => 'required|numeric|min:0',
        ]);

        $baseVoucher->update($request->all());

        return redirect()->route('admin.vouchers.index')->with('success', 'Voucher updated successfully!');
    }

    public function destroy(BaseVoucher $baseVoucher)
    {
        $baseVoucher->delete();

        return redirect()->route('admin.vouchers.index')->with('success', 'Voucher deleted successfully!');
    }
}
