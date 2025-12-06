<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class RajaOngkirService
{
    protected $client;
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('rajaongkir.api_key');
        $this->baseUrl = config('rajaongkir.base_url');
        
        if (!$this->apiKey) {
            Log::error('Komerce API Key not configured');
            throw new \Exception('Komerce API Key is missing');
        }
        
        // ✅ Ensure base URL ends with trailing slash
        $baseUri = rtrim($this->baseUrl, '/') . '/';
        
        $this->client = new Client([
            'base_uri' => $baseUri,
            'headers' => [
                'key' => $this->apiKey,
                'Accept' => 'application/json',
            ],
            'timeout' => 30,
            'verify' => false,
        ]);
        
        Log::info('Komerce Service Initialized', [
            'base_url' => $baseUri
        ]);
    }

    /**
     * ✅ Search destination by city name
     */
    public function searchCity($query, $limit = 20)
    {
        try {
            // ✅ NO leading slash - base_uri already has trailing slash
            $response = $this->client->get('destination/domestic-destination', [
                'query' => [
                    'search' => $query,
                    'limit' => $limit,
                    'offset' => 0
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            
            if (isset($data['data']) && is_array($data['data'])) {
                Log::info('✅ Komerce search results:', [
                    'query' => $query,
                    'count' => count($data['data'])
                ]);
                
                return array_map(function($item) {
                    return [
                        'subdistrict_id' => $item['id'],
                        'subdistrict_name' => $item['subdistrict_name'],
                        'city' => $item['city_name'],
                        'district' => $item['district_name'],
                        'province' => $item['province_name'],
                        'zip_code' => $item['zip_code'],
                    ];
                }, $data['data']);
            }

            Log::warning('No results in response', ['data' => $data]);
            return [];

        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
            $body = $response->getBody()->getContents();
            
            Log::error('Komerce ClientException:', [
                'status' => $response->getStatusCode(),
                'body' => $body,
            ]);
            
            return [];
        } catch (\Exception $e) {
            Log::error('Komerce searchCity Error:', [
                'message' => $e->getMessage(),
            ]);
            return [];
        }
    }

    /**
     * ✅ Get destination by subdistrict ID
     */
    public function getCityById($subdistrictId)
    {
        try {
            // ✅ NO leading slash
            $response = $this->client->get('destination/domestic-destination', [
                'query' => [
                    'search' => $subdistrictId,
                    'limit' => 1,
                    'offset' => 0
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            
            if (isset($data['data'][0])) {
                $item = $data['data'][0];
                
                return [
                    'subdistrict_id' => $item['id'],
                    'subdistrict_name' => $item['subdistrict_name'],
                    'city' => $item['city_name'],
                    'district' => $item['district_name'],
                    'province' => $item['province_name'],
                    'zip_code' => $item['zip_code'],
                ];
            }

            return null;

        } catch (\Exception $e) {
            Log::error('Komerce getCityById Error:', [
                'subdistrict_id' => $subdistrictId,
                'message' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * ✅ Get shipping cost
     */
    public function getShippingOptions($originSubdistrictId, $destinationSubdistrictId, $weight)
    {
        try {
            $payload = [
                'origin' => (string) $originSubdistrictId,
                'destination' => (string) $destinationSubdistrictId,
                'weight' => (int) $weight,
                'courier' => 'jne:jnt:sicepat:anteraja:ninja'
            ];

            Log::info('Komerce Cost Calculation Request:', $payload);

            $response = $this->client->post('calculate/domestic-cost', [
                'form_params' => $payload
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            
            Log::info('Komerce Cost Response:', [
                'status' => $response->getStatusCode(),
                'has_data' => isset($data['data']),
                'data_count' => isset($data['data']) ? count($data['data']) : 0
            ]);
            
            if (!isset($data['data']) || !is_array($data['data'])) {
                Log::warning('No shipping data from Komerce', ['response' => $data]);
                return [];
            }

            $results = [];
            
            // ✅ FIX: Komerce response structure is different
            foreach ($data['data'] as $service) {
                // Create or get delivery record
                $delivery = \App\Models\Delivery::firstOrCreate(
                    ['courier_code' => strtolower($service['code'])],
                    ['name' => $service['name']]
                );
                
                // ✅ FIX: Data structure is flat, not nested
                $results[] = [
                    'delivery_id' => $delivery->id,
                    'courier_code' => $service['code'],
                    'courier_name' => $service['name'],
                    'courier_service_name' => $service['service'],
                    'description' => $service['description'] ?? '',
                    'cost' => (int) $service['cost'], // ✅ Direct value, not nested
                    'etd' => $service['etd'] ?: 'N/A',
                    'display_name' => "{$service['name']} - {$service['service']}",
                ];
            }

            Log::info('✅ Komerce shipping options found:', [
                'count' => count($results),
                'options' => array_map(function($r) {
                    return [
                        'name' => $r['display_name'],
                        'cost' => $r['cost']
                    ];
                }, $results)
            ]);
            
            return $results;

        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
            $statusCode = $response->getStatusCode();
            $body = $response->getBody()->getContents();
            
            Log::error('Komerce Cost Calculation ClientException:', [
                'status' => $statusCode,
                'body' => $body,
                'request_payload' => $payload ?? null
            ]);
            
            return [];
        } catch (\Exception $e) {
            Log::error('Komerce getShippingOptions Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    /**
     * Test API connection
     */
    public function testConnection()
    {
        try {
            // ✅ NO leading slash
            $response = $this->client->get('destination/domestic-destination', [
                'query' => [
                    'search' => 'Jakarta',
                    'limit' => 1,
                    'offset' => 0
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            
            if ((isset($data['meta']['status']) && $data['meta']['status'] === 'success') ||
                (isset($data['data']) && is_array($data['data']) && count($data['data']) > 0)) {
                Log::info('✅ Komerce API connection successful');
                return true;
            }

            Log::warning('Komerce returned unexpected format:', ['data' => $data]);
            return false;

        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
            $body = $response->getBody()->getContents();
            
            Log::error('Komerce Connection ClientException:', [
                'status' => $response->getStatusCode(),
                'body' => $body
            ]);
            
            return false;
        } catch (\Exception $e) {
            Log::error('Komerce Connection Test Failed:', [
                'message' => $e->getMessage()
            ]);
            return false;
        }
    }
}