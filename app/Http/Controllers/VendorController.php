<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'type' => 'required|in:online,offline',
        ];

        // Conditional validation based on type
        if ($request->type === 'offline') {
            $rules['phone_number'] = 'required|string|regex:/^[0-9\s]+$/|min:10|max:20';
            $rules['link'] = 'nullable|url|max:255';
        } elseif ($request->type === 'online') {
            $rules['link'] = 'required|url|max:255';
            $rules['phone_number'] = 'nullable|string|regex:/^[0-9\s]+$/|min:10|max:20';
        }

        $validated = $request->validate($rules);

        // Clear unnecessary fields based on type
        if ($request->type === 'offline') {
            $validated['link'] = null;
        } elseif ($request->type === 'online') {
            $validated['phone_number'] = null;
        }

        Vendor::create($validated);

        return redirect()->route('admin.vendors.index')->with('success', 'Vendor berhasil ditambahkan!');
    }

    public function update(Request $request, Vendor $vendor)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'type' => 'required|in:online,offline',
        ];

        // Conditional validation based on type
        if ($request->type === 'offline') {
            $rules['phone_number'] = 'required|string|regex:/^[0-9\s]+$/|min:10|max:20';
            $rules['link'] = 'nullable|url|max:255';
        } elseif ($request->type === 'online') {
            $rules['link'] = 'required|url|max:255';
            $rules['phone_number'] = 'nullable|string|regex:/^[0-9\s]+$/|min:10|max:20';
        }

        $validated = $request->validate($rules);

        // Clear unnecessary fields based on type
        if ($request->type === 'offline') {
            $validated['link'] = null;
        } elseif ($request->type === 'online') {
            $validated['phone_number'] = null;
        }

        $vendor->update($validated);

        return redirect()->route('admin.vendors.index')->with('success', 'Vendor berhasil diperbarui!');
    }

    public function destroy(Vendor $vendor)
    {
        $vendor->delete();

        return redirect()->route('admin.vendors.index')->with('success', 'Vendor berhasil dihapus!');
    }
}
