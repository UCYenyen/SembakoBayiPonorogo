<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    /**
     * Display user's addresses
     */
    public function index()
    {
        $addresses = Address::where('user_id', Auth::id())
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('dashboard.user.addresses.index', [
            'addresses' => $addresses,
        ]);
    }

    /**
     * Show create address form
     */
    public function create()
    {
        $googleMapsApiKey = config('services.google.maps_api_key');
        
        return view('dashboard.user.addresses.create', [
            'googleMapsApiKey' => $googleMapsApiKey,
        ]);
    }

    /**
     * Store new address
     */
    public function store(Request $request)
    {
        $request->validate([
            'detail' => 'required|string|max:1000',
        ]);

        $user = Auth::user();

        // Check if this is the first address
        $isFirstAddress = !Address::where('user_id', $user->id)->exists();

        $address = Address::create([
            'user_id' => $user->id,
            'detail' => $request->detail,
            'is_default' => $isFirstAddress, // Set as default if first address
        ]);

        return redirect()->route('user.addresses.index')
            ->with('success', 'Address added successfully!');
    }

    /**
     * Show edit address form
     */
    public function edit(Address $address)
    {
        // Check if user owns this address
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $googleMapsApiKey = config('services.google.maps_api_key');

        return view('dashboard.user.addresses.edit', [
            'address' => $address,
            'googleMapsApiKey' => $googleMapsApiKey,
        ]);
    }

    /**
     * Update address
     */
    public function update(Request $request, Address $address)
    {
        // Check if user owns this address
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'detail' => 'required|string|max:1000',
        ]);

        $address->update([
            'detail' => $request->detail,
        ]);

        return redirect()->route('user.addresses.index')
            ->with('success', 'Address updated successfully!');
    }

    /**
     * Set address as default
     */
    public function setDefault(Address $address)
    {
        // Check if user owns this address
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $address->setAsDefault();

        return back()->with('success', 'Default address updated!');
    }

    /**
     * Delete address
     */
    public function destroy(Address $address)
    {
        // Check if user owns this address
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        // Don't allow deleting the only address
        if (Address::where('user_id', Auth::id())->count() === 1) {
            return back()->with('error', 'You must have at least one address!');
        }

        // If deleting default address, set another as default
        if ($address->is_default) {
            $newDefault = Address::where('user_id', Auth::id())
                ->where('id', '!=', $address->id)
                ->first();
            
            if ($newDefault) {
                $newDefault->setAsDefault();
            }
        }

        $address->delete();

        return redirect()->route('user.addresses.index')
            ->with('success', 'Address deleted successfully!');
    }
}
