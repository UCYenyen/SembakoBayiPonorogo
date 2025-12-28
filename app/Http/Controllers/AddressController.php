<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class AddressController extends Controller
{
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
    public function create()
    {
        // Yang atas ini digunakan untuk ambil secara langsung dari API Raja Ongkir tapi makan request jadi aku hardcode aja (aku ambil sekali terus langsung dari json filenya tak masukin ke config jadi id pasti sama biar ga makan limit)
        //  $response = Http::withHeaders([
        //     'Accept' => 'application/json',
        //     'key' => config('rajaongkir.api_key'),

        // ])->get('https://rajaongkir.komerce.id/api/v1/destination/province');

        // if ($response->successful()) {

        //     $provinces = $response->json()['data'] ?? [];
        // }
        // return view('dashboard.user.addresses.create', compact('provinces'));

        $provinces = config('rajaongkir.provinces');
        $cities = config('rajaongkir.cities');

        return view('dashboard.user.addresses.create', compact('provinces', 'cities'));
    }

    public function getCities($provinceId)
    {
        // Mengambil data dari config yang sudah di-hardcode
        $cities = collect(config('rajaongkir.cities'));

        // Filter berdasarkan province_id dan kirim sebagai JSON
        $filtered = $cities->where('province_id', (int) $provinceId)->values();

        return response()->json($filtered);
    }

    public function getDistricts($cityId)
    {
        // Nama kunci unik per kota, misal: districts_city_152
        $cacheKey = "global_districts_city_" . $cityId;

        // rememberForever akan menyimpan data di server (bukan di browser user)
        // Jadi jika User A sudah akses, User B, C, dan D akan mengambil dari Cache yang sama.
        $districts = Cache::rememberForever($cacheKey, function () use ($cityId) {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'key' => config('rajaongkir.api_key'),
            ])->get("https://rajaongkir.komerce.id/api/v1/destination/district/{$cityId}");

            if ($response->successful()) {
                return $response->json()['data'] ?? [];
            }

            return null;
        });

        if (!$districts) {
            return response()->json(['error' => 'Gagal mengambil data atau Limit API habis'], 500);
        }

        return response()->json($districts);
    }
     
    public function getSubDistricts($districtId)
    {
        $cacheKey = "global_sub_districts_district_" . $districtId;

        $subDistricts = Cache::rememberForever($cacheKey, function () use ($districtId) {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'key' => config('rajaongkir.api_key'),
            ])->get("https://rajaongkir.komerce.id/api/v1/destination/sub-district/{$districtId}");

            if ($response->successful()) {
                return $response->json()['data'] ?? [];
            }

            return null;
        });

        if (!$subDistricts) {
            return response()->json(['error' => 'Gagal mengambil data atau Limit API habis'], 500);
        }

        return response()->json($subDistricts);
    }
}
