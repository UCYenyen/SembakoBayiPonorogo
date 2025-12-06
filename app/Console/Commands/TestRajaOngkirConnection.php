<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RajaOngkirService;
use GuzzleHttp\Client;

class TestRajaOngkirConnection extends Command
{
    protected $signature = 'test:rajaongkir';
    protected $description = 'Test RajaOngkir/Komerce API connection';

    public function handle()
    {
        $this->info('🔍 Testing Komerce API connection...');
        $this->newLine();

        // Check config
        $apiKey = config('rajaongkir.api_key');
        $baseUrl = config('rajaongkir.base_url');

        $this->info('📋 Configuration Check:');
        $this->line("  Base URL: {$baseUrl}");
        $this->line("  API Key: " . ($apiKey ? substr($apiKey, 0, 10) . '...' : '❌ NOT SET'));
        $this->newLine();

        if (!$apiKey) {
            $this->error('❌ KOMERCE_API_KEY is not set in .env');
            return 1;
        }

        // ✅ Test 1: Direct cURL-style request
        $this->info('🧪 Test 1: Direct HTTP Request...');
        try {
            // ✅ Ensure base URL ends with /
            $baseUri = rtrim($baseUrl, '/') . '/';
            
            $client = new Client([
                'base_uri' => $baseUri, // ✅ Use base_uri with trailing slash
                'headers' => [
                    'key' => $apiKey,
                    'Accept' => 'application/json',
                ],
                'timeout' => 30,
                'verify' => false,
            ]);

            $response = $client->get('destination/domestic-destination', [ // ✅ No leading slash
                'query' => [
                    'search' => 'Jakarta',
                    'limit' => 1,
                    'offset' => 0
                ]
            ]);

            $statusCode = $response->getStatusCode();
            $body = $response->getBody()->getContents();
            $data = json_decode($body, true);

            $this->info("  Status Code: {$statusCode}");
            $this->info("  Response Keys: " . implode(', ', array_keys($data ?? [])));
            
            if (isset($data['data'])) {
                $this->info("  Data Count: " . count($data['data']));
                
                if (!empty($data['data'])) {
                    $first = $data['data'][0];
                    $this->info("  First Result:");
                    $this->line("    - ID: " . ($first['id'] ?? 'N/A'));
                    $this->line("    - Subdistrict: " . ($first['subdistrict_name'] ?? 'N/A'));
                    $this->line("    - City: " . ($first['city_name'] ?? 'N/A'));
                }
            }

            $this->info('✅ Direct request successful!');
            $this->newLine();

        } catch (\Exception $e) {
            $this->error('❌ Direct request failed: ' . $e->getMessage());
            $this->newLine();
            return 1;
        }

        // ✅ Test 2: Via RajaOngkirService
        $this->info('🧪 Test 2: Via RajaOngkirService...');
        try {
            $komerce = app(RajaOngkirService::class);
            
            if ($komerce->testConnection()) {
                $this->info('✅ Service connection successful!');
                $this->newLine();
                
                // Test search
                $this->info('🔍 Testing city search...');
                $cities = $komerce->searchCity('Jakarta', 5);
                
                if (!empty($cities)) {
                    $this->info('✅ City search successful!');
                    $this->newLine();
                    
                    $this->table(
                        ['Subdistrict ID', 'Subdistrict', 'City', 'Province'],
                        array_map(function($city) {
                            return [
                                $city['subdistrict_id'] ?? '-',
                                $city['subdistrict_name'] ?? '-',
                                $city['city'] ?? '-',
                                $city['province'] ?? '-'
                            ];
                        }, $cities)
                    );
                    
                    $this->newLine();
                    $this->info('✅ All tests passed!');
                    return 0;
                } else {
                    $this->warn('⚠️ No cities found in search');
                    return 1;
                }
                
            } else {
                $this->error('❌ Service connection failed');
                $this->warn('Please check:');
                $this->line('  1. API key is correct');
                $this->line('  2. Internet connection');
                $this->line('  3. Komerce service status');
                return 1;
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Service test failed: ' . $e->getMessage());
            $this->newLine();
            $this->warn('Check storage/logs/laravel.log for details');
            return 1;
        }
    }
}
