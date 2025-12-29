<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class DeliveryController extends Controller
{
    public function checkOngkir(Request $request, Address $address)
    {
        // Validasi data yang masuk dari URL/Query String
        $request->validate([
            'weight' => 'required|numeric|min:1',
            'courier' => 'nullable|string'
        ]);

        $destinationId = $address->subdistrict_id;

        if (!$destinationId) {
            return response()->json([
                'success' => false,
                'message' => "ID Kecamatan tidak ditemukan di database."
            ], 422);
        }

        $weight = (int) $request->weight;
        $couriers = $request->courier ?? 'jne,jnt';
        
        $cacheKey = "shipping_cost_{$destinationId}_{$weight}_" . str_replace(',', '_', $couriers);

        $shippingData = Cache::remember($cacheKey, now()->addHours(12), function () use ($destinationId, $weight, $couriers) {
            try {
                $response = Http::asForm()->withHeaders([
                    'Accept' => 'application/json',
                    'key'    => config('rajaongkir.api_key'),
                ])->post('https://rajaongkir.komerce.id/api/v1/calculate/domestic-cost', [
                    'origin'      => 3656, // ID Origin Toko Anda
                    'destination' => (int) $destinationId,
                    'weight'      => $weight,
                    'courier'     => $couriers,
                ]);

                if ($response->successful()) {
                    return $response->json()['data'] ?? [];
                }

                return null;
            } catch (\Exception $e) {
                return null;
            }
        });

        if (!$shippingData) {
            Cache::forget($cacheKey);
            return response()->json([
                'success' => false, 
                'message' => "Gagal mendapatkan data dari API Komerce."
            ], 500);
        }

        return response()->json([
            'success' => true,
            'data'    => $shippingData
        ]);
    }
}