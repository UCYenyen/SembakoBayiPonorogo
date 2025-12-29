<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DeliveryController extends Controller
{
    private $originLat = -7.8716564;
    private $originLong = 111.4726568;

    public function checkOngkir(Request $request, Address $address)
    {
        try {
            // Konversi gram ke kilogram (dibagi 1000)
            // GoSend biasanya menerima angka desimal (misal 0.5 untuk 500gr)
            $weightInGram = (int) $request->input('weight', 1000);
            $weightInKg = $weightInGram / 1000; 
            
            $courier = $request->input('courier');
            $itemValue = (int) $request->input('item_value', 50000);

            if ($courier === 'gosend') {
                // Pastikan alamat tujuan memiliki latitude dan longitude
                if (!$address->latitude || !$address->longitude) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Koordinat alamat tujuan (Lat/Long) belum diatur.'
                    ], 422);
                }

                $response = Http::withHeaders([
                    'Accept'    => 'application/json',
                    'x-api-key' => config('rajaongkir.api_shipping_delivery_key'), 
                ])->get('https://api-sandbox.collaborator.komerce.id/tariff/api/v1/calculate', [
                    'item_value'            => $itemValue, // Tidak di-hardcode lagi
                    'origin_pin_point'      => "{$this->originLat},{$this->originLong}",
                    'destination_pin_point' => "{$address->latitude},{$address->longitude}", // Diambil dari database
                    'weight'                => $weightInKg, // Sudah dalam Kilogram
                ]);

                if ($response->failed()) {
                    Log::error("Komerce Reject: " . $response->body());
                    return response()->json([
                        'success' => false, 
                        'message' => 'Layanan GoSend gagal dimuat atau lokasi di luar jangkauan.'
                    ], 422);
                }

                $res = $response->json();
                $instantOptions = $res['data']['calculate_instant'] ?? [];
                
                $formattedData = [];
                foreach ($instantOptions as $item) {
                    $formattedData[] = [
                        'courier' => 'GOSEND',
                        'service' => $item['service_name'] ?? 'Instant',
                        'cost'    => $item['shipping_cost'] ?? 0,
                        'etd'     => $item['etd'] ?? '1-2 jam',
                        'description' => 'Layanan Instant Gojek'
                    ];
                }
                return response()->json(['success' => true, 'data' => $formattedData]);
            }

            // Logika JNE/JNT tetap pakai GRAM (RajaOngkir standar menggunakan Gram)
            $response = Http::asForm()->withHeaders([
                'Accept' => 'application/json',
                'key'    => config('rajaongkir.api_key'),
            ])->post('https://rajaongkir.komerce.id/api/v1/calculate/domestic-cost', [
                'origin'      => 40737,
                'destination' => (int) $address->subdistrict_id,
                'weight'      => $weightInGram, // Tetap Gram untuk JNE/JNT
                'courier'     => $courier,
            ]);

            if ($response->successful()) {
                $shippingData = $response->json()['data'] ?? [];
                $formattedData = [];
                foreach ($shippingData as $option) {
                    if (!str_contains(strtoupper($option['service'] ?? ''), 'JTR')) {
                        $formattedData[] = [
                            'courier' => strtoupper($courier),
                            'service' => $option['service'] ?? '',
                            'cost'    => $option['cost'] ?? 0,
                            'etd'     => $option['etd'] ?? '-',
                            'description' => $option['description'] ?? ''
                        ];
                    }
                }
                return response()->json(['success' => true, 'data' => $formattedData]);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}