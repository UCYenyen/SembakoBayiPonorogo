<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|regex:/^[0-9+\-\s]+$/|min:10|max:20',
        ]);

        Vendor::create($request->all());

        return redirect()->route('admin.vendors.index')->with('success', 'Vendor created successfully!');
    }

    public function update(Request $request, Vendor $vendor)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|regex:/^[0-9+\-\s]+$/|min:10|max:20',
        ]);

        $vendor->update($request->all());

        return redirect()->route('admin.vendors.index')->with('success', 'Vendor updated successfully!');
    }

    public function destroy(Vendor $vendor)
    {
        $vendor->delete();

        return redirect()->route('admin.vendors.index')->with('success', 'Vendor deleted successfully!');
    }
}
