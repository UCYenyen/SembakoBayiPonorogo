<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BaseVoucher;

class BaseVoucherController extends Controller
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

    public function showVouchers()
    {
        $vouchers = BaseVoucher::orderBy('created_at', 'desc')->paginate(15);

        return view('dashboard.admin.vouchers.index', [
            'vouchers' => $vouchers
        ]);
    }

    public function createVoucher()
    {
        return view('dashboard.admin.vouchers.create');
    }

    public function editVoucher(BaseVoucher $baseVoucher)
    {
        return view('dashboard.admin.vouchers.edit', [
            'voucher' => $baseVoucher
        ]);
    }
}
