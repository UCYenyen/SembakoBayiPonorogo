<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class RajaOngkirService
{
    protected $client;
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('rajaongkir.api_key');
        $this->baseUrl = config('rajaongkir.base_url');
        
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'headers' => [
                'key' => $this->apiKey,
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    /**
     * Get list of provinces
     */
    public function getProvinces()
    {
        try {
            $response = $this->client->get('/province');
            $data = json_decode($response->getBody(), true);
            
            return $data['rajaongkir']['results'] ?? [];
        } catch (\Exception $e) {
            Log::error('RajaOngkir getProvinces Error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get cities by province ID
     */
    public function getCities($provinceId = null)
    {
        try {
            $url = $provinceId ? "/city?province={$provinceId}" : '/city';
            $response = $this->client->get($url);
            $data = json_decode($response->getBody(), true);
            
            return $data['rajaongkir']['results'] ?? [];
        } catch (\Exception $e) {
            Log::error('RajaOngkir getCities Error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Calculate shipping cost
     * 
     * @param int $origin City ID of origin
     * @param int $destination City ID of destination
     * @param int $weight Weight in grams
     * @param string $courier Courier code (jne, pos, tiki, etc)
     * @return array
     */
    public function calculateCost($origin, $destination, $weight, $courier = 'jne')
    {
        try {
            $response = $this->client->post('/cost', [
                'form_params' => [
                    'origin' => $origin,
                    'destination' => $destination,
                    'weight' => $weight,
                    'courier' => $courier,
                ]
            ]);

            $data = json_decode($response->getBody(), true);
            
            if (isset($data['rajaongkir']['results'][0]['costs'])) {
                return $data['rajaongkir']['results'][0]['costs'];
            }
            
            return [];
        } catch (\Exception $e) {
            Log::error('RajaOngkir calculateCost Error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get shipping options with costs
     * 
     * @param int $origin
     * @param int $destination
     * @param int $weight
     * @return array
     */
    public function getShippingOptions($origin, $destination, $weight)
    {
        $couriers = ['jne', 'pos', 'tiki', 'sicepat', 'jnt'];
        $results = [];

        foreach ($couriers as $courier) {
            try {
                $response = $this->client->post('/cost', [
                    'form_params' => [
                        'origin' => $origin,
                        'destination' => $destination,
                        'weight' => $weight,
                        'courier' => $courier,
                    ]
                ]);

                $data = json_decode($response->getBody(), true);
                
                if (isset($data['rajaongkir']['results'][0])) {
                    $courierData = $data['rajaongkir']['results'][0];
                    
                    // ✅ Find delivery_id from deliveries table
                    $delivery = \App\Models\Delivery::where('courier_code', $courier)->first();
                    
                    if ($delivery) {
                        foreach ($courierData['costs'] as $cost) {
                            $results[] = [
                                'delivery_id' => $delivery->id, // ✅ Add delivery_id
                                'courier_code' => $courier,
                                'courier_name' => $courierData['name'],
                                'service' => $cost['service'],
                                'description' => $cost['description'],
                                'cost' => $cost['cost'][0]['value'],
                                'etd' => $cost['cost'][0]['etd'],
                                'display_name' => "{$courierData['name']} - {$cost['service']} ({$cost['description']})",
                            ];
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::error("RajaOngkir Error for {$courier}: " . $e->getMessage());
                continue;
            }
        }

        return $results;
    }
}