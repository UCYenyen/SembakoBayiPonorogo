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
        $awb = trim($transaction->no_resi);
        $courier = strtolower($transaction->delivery->courier_code);
        $fullPhone = $transaction->user->phone_number;
        $cleanPhone = preg_replace('/[^0-9]/', '', $fullPhone);
        $lastPhone = strlen($cleanPhone) >= 5 ? substr($cleanPhone, -5) : $cleanPhone;

        try {
            $queryParams = [
                'awb' => $awb,
                'courier' => $courier,
            ];

            if ($courier === 'jne') {
                $queryParams['last_phone_number'] = $lastPhone;
            }

            $url = "https://rajaongkir.komerce.id/api/v1/track/waybill?" . http_build_query($queryParams);

            $response = Http::withHeaders([
                'key' => config('rajaongkir.api_key'),
                'Accept' => 'application/json'
            ])->post($url);

            if ($response->successful()) {
                $responseData = $response->json();

                if (isset($responseData['meta']) && ($responseData['meta']['status'] === 'error')) {
                    return response()->json([
                        'success' => false,
                        'message' => $responseData['meta']['message']
                    ], 404);
                }
                $status = strtolower($responseData['data']['summary']['status'] ?? '');
                if ($status === 'delivered') {
                    $transaction->status = 'delivered';
                    $transaction->save();
                }

                return response()->json([
                    'success' => true,
                    'data' => $responseData['data']
                ]);
            }

            return response()->json(['success' => false, 'message' => 'Gagal terhubung ke kurir'], 500);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
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
