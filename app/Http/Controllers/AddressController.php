<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Services\RajaOngkirService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AddressController extends Controller
{
    protected $rajaOngkir;

    public function __construct(RajaOngkirService $rajaOngkir)
    {
        $this->rajaOngkir = $rajaOngkir;
    }

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
        return view('dashboard.user.addresses.create');
    }

    /**
     * Store new address
     */
    public function store(Request $request)
    {
        $request->validate([
            'detail' => 'required|string|max:1000',
            'subdistrict_id' => 'required|string', // ✅ Changed to string
        ]);

        $user = Auth::user();

        // ✅ Get subdistrict details from Komerce
        $subdistrictDetails = $this->rajaOngkir->getCityById($request->subdistrict_id);

        if (!$subdistrictDetails) {
            return back()->withErrors([
                'subdistrict_id' => 'Invalid location selected.'
            ])->withInput();
        }

        $isFirstAddress = Address::where('user_id', $user->id)->count() === 0;

        Address::create([
            'user_id' => $user->id,
            'detail' => $request->detail,
            'subdistrict_id' => $subdistrictDetails['subdistrict_id'],
            'subdistrict_name' => $subdistrictDetails['subdistrict_name'],
            'city_name' => $subdistrictDetails['city'],
            'province' => $subdistrictDetails['province'],
            'is_default' => $isFirstAddress,
        ]);

        Log::info('Address created with Komerce:', [
            'subdistrict_id' => $subdistrictDetails['subdistrict_id'],
            'location' => $subdistrictDetails['subdistrict_name'],
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

        return view('dashboard.user.addresses.edit', [
            'address' => $address,
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
            'city_id' => 'required|integer',
        ]);

        // Get city details
        $cityDetails = $this->rajaOngkir->getCityById($request->city_id);

        if (!$cityDetails) {
            return back()->withErrors([
                'city_id' => 'Invalid city selected.'
            ])->withInput();
        }

        $address->update([
            'detail' => $request->detail,
            'city_id' => $cityDetails['city_id'],
            'city_name' => $cityDetails['type'] . ' ' . $cityDetails['city_name'],
            'province' => $cityDetails['province'],
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

        if ($address->is_default && Address::where('user_id', Auth::id())->count() > 1) {
            $nextAddress = Address::where('user_id', Auth::id())
                ->where('id', '!=', $address->id)
                ->first();
            
            if ($nextAddress) {
                $nextAddress->setAsDefault();
            }
        }

        $address->delete();

        return redirect()->route('user.addresses.index')
            ->with('success', 'Address deleted successfully!');
    }

    // ✅ API endpoint for city search
    public function searchCities(Request $request)
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $cities = $this->rajaOngkir->searchCity($query);
        
        return response()->json($cities);
    }
}
