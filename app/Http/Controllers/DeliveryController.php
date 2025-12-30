<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Transaction;

class DeliveryController extends Controller
{
    public function trackDelivery(Transaction $transaction)
    {
        $awb = $transaction->no_resi;
        $courier = strtolower($transaction->delivery->courier_code ?? 'jne');

        $fullPhone = optional($transaction->user)->phone ?? '';
        $lastPhone = substr(preg_replace('/[^0-9]/', '', $fullPhone), -5);

        $url = "https://rajaongkir.komerce.id/api/v1/track/waybill?awb={$awb}&courier={$courier}";

        if ($courier === 'jne' && !empty($lastPhone)) {
            $url .= "&last_phone_number={$lastPhone}";
        }

        try {
            $response = Http::withHeaders([
                'key' => config('rajaongkir.api_key')
            ])->post($url);

            if ($response->successful()) {
                $responseData = $response->json();

                if (isset($responseData['meta']) && $responseData['meta']['status'] === 'error') {
                    return response()->json([
                        'success' => false,
                        'message' => $responseData['meta']['message']
                    ], 404);
                }
                // Jika status delivery == delivered, update status transaksi jadi done
                if (
                    isset($responseData['data']['summary']['status']) &&
                    strtolower($responseData['data']['summary']['status']) === 'delivered'
                ) {
                    $transaction->status = 'delivered';
                    $transaction->save();
                }

                return response()->json([
                    'success' => true,
                    'data' => $responseData['data'] ?? []
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Gagal terhubung ke server kurir'
            ], $response->status());
        } catch (\Exception $e) {
            Log::error('Error tracking delivery: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem'
            ], 500);
        }
    }

    public function checkOngkir(Request $request, Address $address)
    {
        try {
            $weightInGram = (int) $request->input('weight', 1000);
            $courier = $request->input('courier');

            $response = Http::asForm()->withHeaders([
                'Accept' => 'application/json',
                'key'    => config('rajaongkir.api_key'),
            ])->post('https://rajaongkir.komerce.id/api/v1/calculate/domestic-cost', [
                'origin'      => 40737,
                'destination' => (int) $address->subdistrict_id,
                'weight'      => $weightInGram,
                'courier'     => $courier,
            ]);

            if ($response->successful()) {
                $shippingData = $response->json()['data'] ?? [];
                $formattedData = [];
                foreach ($shippingData as $option) {
                    $formattedData[] = [
                        'courier' => strtoupper($courier),
                        'service' => $option['service'] ?? '',
                        'cost'    => $option['cost'] ?? 0,
                        'etd'     => $option['etd'] ?? '-',
                        'description' => $option['description'] ?? ''
                    ];
                }
                return response()->json(['success' => true, 'data' => $formattedData]);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
