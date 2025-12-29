<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function index()
    {
        $addresses = Address::where('user_id', Auth::id())
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('dashboard.user.addresses.index', compact('addresses'));
    }
    public function create()
    {
        $provinces = collect(config('rajaongkir.provinces'));
        return view('dashboard.user.addresses.create', compact('provinces'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'province'  => 'required',
            'city'      => 'required',
            'district'  => 'required',
            'subdistrict' => 'required',
            'postal_code'  => 'required|string|max:20',
            'extra_detail' => 'required|string|max:500',
        ]);

        $provinceData = explode('|', $request->province);
        $cityData = explode('|', $request->city);
        $districtData = explode('|', $request->district);
        $subdistrictData = explode('|', $request->subdistrict);

        $geoResult = $this->getGeoLocation(
            $provinceData[1],
            $cityData[1],
            $districtData[1],
            $subdistrictData[1]
        );

        Address::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'province_id' => $provinceData[0],
            'province_name' => $provinceData[1],
            'city_id' => $cityData[0],
            'city_name' => $cityData[1],
            'district_id' => $districtData[0],
            'district_name' => $districtData[1],
            'subdistrict_id' => $subdistrictData[0],
            'subdistrict_name' => $subdistrictData[1],
            'postal_code' => $request->postal_code,
            'extra_detail' => $request->extra_detail,
            'latitude' => $geoResult['status'] === 'success' ? $geoResult['lat'] : null,
            'longitude' => $geoResult['status'] === 'success' ? $geoResult['lon'] : null,
            'is_default' => !Address::where('user_id', Auth::id())->exists(),
        ]);

        return redirect()->route('user.addresses.index')->with('success', 'Alamat berhasil ditambahkan.');
    }
    public function getGeoLocation($province, $city, $district, $subdistrict)
    {
        // Menyusun alamat tanpa extra_detail
        $address = "$subdistrict, $district, $city, $province, Indonesia";

        // Menggunakan Laravel HTTP Client (lebih bersih)
        $response = Http::withHeaders([
            'User-Agent' => 'AplikasiTokoSaya (admin@domain.com)' // Wajib diisi untuk Nominatim
        ])->timeout(10)->get('https://nominatim.openstreetmap.org/search', [
            'q' => $address,
            'format' => 'json',
            'limit' => 1
        ]);

        if ($response->successful()) {
            $data = $response->json();

            if (!empty($data)) {
                return [
                    'status' => 'success',
                    'lat'    => $data[0]['lat'],
                    'lon'    => $data[0]['lon']
                ];
            }
        }

        return [
            'status' => 'error',
            'message' => 'Lokasi tidak ditemukan'
        ];
    }

    public function getCities($provinceId)
    {
        $cities = collect(config('rajaongkir.cities'));
        $filtered = $cities->where('province_id', (int) $provinceId)->values();
        return response()->json($filtered);
    }

    public function getDistricts($cityId)
    {
        $cacheKey = "global_districts_city_" . $cityId;
        $districts = Cache::rememberForever($cacheKey, function () use ($cityId) {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'key' => config('rajaongkir.api_key'),
            ])->get("https://rajaongkir.komerce.id/api/v1/destination/district/{$cityId}");
            return $response->successful() ? $response->json()['data'] : null;
        });
        return response()->json($districts ?? []);
    }

    public function getSubDistricts($districtId)
    {
        $cacheKey = "global_sub_districts_district_" . $districtId;
        $subDistricts = Cache::rememberForever($cacheKey, function () use ($districtId) {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'key' => config('rajaongkir.api_key'),
            ])->get("https://rajaongkir.komerce.id/api/v1/destination/sub-district/{$districtId}");
            return $response->successful() ? $response->json()['data'] : null;
        });
        return response()->json($subDistricts ?? []);
    }

    public function setDefault(Address $address)
    {
        $address->setAsDefault();
        return redirect()->route('user.addresses.index')->with('success', 'Alamat default berhasil diatur.');
    }

    public function destroy(Address $address)
    {
        $address->delete();
        return redirect()->route('user.addresses.index')->with('success', 'Alamat berhasil dihapus.');
    }
}
