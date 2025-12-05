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
            'city_name' => 'required|string', // ✅ Required from Google Maps
        ]);

        $user = Auth::user();

        // ✅ Auto-match city_id dari RajaOngkir berdasarkan city_name dari Google Maps
        $cityId = $this->matchCityId($request->city_name);

        if (!$cityId) {
            return back()->withErrors([
                'city_name' => 'Could not find matching city in delivery service database. Please contact support.'
            ])->withInput();
        }

        // Check if this is the first address
        $isFirstAddress = Address::where('user_id', $user->id)->count() === 0;

        Address::create([
            'user_id' => $user->id,
            'detail' => $request->detail,
            'city_id' => $cityId,
            'city_name' => $request->city_name,
            'is_default' => $isFirstAddress,
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
            'city_name' => 'required|string',
        ]);

        // ✅ Auto-match city_id
        $cityId = $this->matchCityId($request->city_name);

        if (!$cityId) {
            return back()->withErrors([
                'city_name' => 'Could not find matching city in delivery service database.'
            ])->withInput();
        }

        $address->update([
            'detail' => $request->detail,
            'city_id' => $cityId,
            'city_name' => $request->city_name,
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

    /**
     * ✅ Match city name dari Google Maps dengan RajaOngkir city_id
     */
    private function matchCityId(string $cityName): ?string
    {
        try {
            // Get all cities dari RajaOngkir
            $cities = $this->rajaOngkir->getCities();

            // Clean city name dari Google Maps
            $cleanCityName = $this->cleanCityName($cityName);

            Log::info('Matching city:', [
                'google_maps_city' => $cityName,
                'cleaned_city' => $cleanCityName,
                'total_cities' => count($cities)
            ]);

            $matches = [];

            // Cari exact match atau partial match
            foreach ($cities as $city) {
                $rajaOngkirCityName = strtolower($city['city_name']);
                $rajaOngkirType = strtolower($city['type'] ?? ''); // "Kota" atau "Kabupaten"

                // Remove "Kota" or "Kabupaten" prefix
                $rajaOngkirCityNameClean = str_replace(['kota ', 'kabupaten '], '', $rajaOngkirCityName);

                // ✅ Strategy 1: Exact match
                if ($cleanCityName === $rajaOngkirCityNameClean) {
                    Log::info('City matched (exact):', [
                        'city_id' => $city['city_id'], 
                        'city_name' => $city['city_name'],
                        'type' => $city['type']
                    ]);
                    return (string) $city['city_id'];
                }

                // ✅ Strategy 2: Contains match (dengan scoring)
                $score = 0;
                if (str_contains($cleanCityName, $rajaOngkirCityNameClean)) {
                    $score = strlen($rajaOngkirCityNameClean) / strlen($cleanCityName) * 100;
                } elseif (str_contains($rajaOngkirCityNameClean, $cleanCityName)) {
                    $score = strlen($cleanCityName) / strlen($rajaOngkirCityNameClean) * 100;
                }

                if ($score > 50) { // Match jika similarity > 50%
                    $matches[] = [
                        'city_id' => $city['city_id'],
                        'city_name' => $city['city_name'],
                        'type' => $city['type'] ?? '',
                        'score' => $score
                    ];
                }

                // ✅ Strategy 3: Word-based matching (split by space)
                $cleanWords = explode(' ', $cleanCityName);
                $rajaWords = explode(' ', $rajaOngkirCityNameClean);
                
                $wordMatches = 0;
                foreach ($cleanWords as $word) {
                    if (strlen($word) > 3 && in_array($word, $rajaWords)) {
                        $wordMatches++;
                    }
                }

                if ($wordMatches > 0 && count($cleanWords) > 0) {
                    $wordScore = ($wordMatches / count($cleanWords)) * 100;
                    if ($wordScore > 50) {
                        $matches[] = [
                            'city_id' => $city['city_id'],
                            'city_name' => $city['city_name'],
                            'type' => $city['type'] ?? '',
                            'score' => $wordScore
                        ];
                    }
                }
            }

            // Sort matches by score (highest first)
            usort($matches, function($a, $b) {
                return $b['score'] <=> $a['score'];
            });

            // Remove duplicates based on city_id
            $uniqueMatches = [];
            $seenIds = [];
            foreach ($matches as $match) {
                if (!in_array($match['city_id'], $seenIds)) {
                    $uniqueMatches[] = $match;
                    $seenIds[] = $match['city_id'];
                }
            }

            if (!empty($uniqueMatches)) {
                $bestMatch = $uniqueMatches[0];
                Log::info('City matched (best):', $bestMatch);
                return (string) $bestMatch['city_id'];
            }

            Log::warning('City not found in RajaOngkir database', [
                'city_name' => $cityName,
                'cleaned' => $cleanCityName,
                'tried_strategies' => ['exact', 'contains', 'word-based']
            ]);
            
            return null;

        } catch (\Exception $e) {
            Log::error('Error matching city ID:', [
                'message' => $e->getMessage(),
                'city_name' => $cityName,
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Clean city name untuk matching
     */
    private function cleanCityName(string $cityName): string
    {
        $cleaned = strtolower($cityName);
        
        // Remove common prefixes/suffixes
        $cleaned = str_replace([
            'kota ', 'kabupaten ', 'kab. ', 'kab ', 'kab.', 
            ' city', ' regency', 'city of ', 'regency of ',
            'kabupaten administratif ', 'kota administratif ',
            ', indonesia', ' - indonesia'
        ], '', $cleaned);
        
        // Remove province names (common in Google Maps results)
        $provinces = [
            'dki jakarta', 'jakarta', 
            'jawa barat', 'west java',
            'jawa tengah', 'central java',
            'jawa timur', 'east java',
            'banten', 
            'yogyakarta', 'daerah istimewa yogyakarta',
            'bali', 
            'sumatera utara', 'north sumatra',
            'sumatera barat', 'west sumatra',
            'sumatera selatan', 'south sumatra',
            'lampung',
            'kalimantan timur', 'east kalimantan',
            'kalimantan barat', 'west kalimantan',
            'kalimantan selatan', 'south kalimantan',
            'kalimantan tengah', 'central kalimantan',
            'sulawesi selatan', 'south sulawesi',
            'sulawesi utara', 'north sulawesi',
            'sulawesi tengah', 'central sulawesi',
            'papua', 'west papua',
            'maluku', 'maluku utara',
            'nusa tenggara barat', 'west nusa tenggara',
            'nusa tenggara timur', 'east nusa tenggara'
        ];
        
        foreach ($provinces as $province) {
            $cleaned = str_replace([', ' . $province, ' ' . $province], '', $cleaned);
        }

        // Remove special characters but keep spaces
        $cleaned = preg_replace('/[^a-z0-9\s]/', '', $cleaned);
        
        // Remove extra spaces
        $cleaned = preg_replace('/\s+/', ' ', $cleaned);

        return trim($cleaned);
    }
}
