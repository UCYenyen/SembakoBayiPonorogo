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
                    'origin'      => 40737,
                    'destination' => (int) $destinationId,
                    'weight'      => $weight,
                    'courier'     => $couriers,
                ]);

                Log::info('Cek Ongkir:', [
                    'dest' => $destinationId,
                    'response' => $response->json()
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

        $filteredData = array_filter($shippingData, function ($option) {
            $serviceName = strtoupper($option['service'] ?? '');
            return !str_contains($serviceName, 'JTR');
        });

        $formattedData = array_map(function ($option) {
            return [
                'courier' => $option['courier'] ?? '',
                'service' => $option['service'] ?? '',
                'cost'    => $option['cost'] ?? 0,
                'etd'     => $option['etd'] ?? '-', // Ini adalah field estimasi hari
                'description' => $option['description'] ?? ''
            ];
        }, array_values($filteredData));

        return response()->json([
            'success' => true,
            'data'    => $formattedData
        ]);
    }
}
