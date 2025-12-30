<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class WhatsAppService
{
    public static function sendMessage($phone, $message)
    {
        $apiUrl = env('WHATSAPP_API_URL') . '/send/message';
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        } elseif (str_starts_with($phone, '8')) {
            $phone = '62' . $phone;
        }

        if (!str_contains($phone, '@')) {
            $phone = $phone . '@s.whatsapp.net';
        }



        try {
            $response = Http::withBasicAuth(
                env('WHATSAPP_AUTH_USER'),
                env('WHATSAPP_AUTH_PASS')
            )->withHeaders([
                'X-Device-Id' => env('WHATSAPP_DEVICE_ID')
            ])->timeout(10)
                ->post($apiUrl, [
                    'phone'   => $phone,
                    'message' => $message,
                ]);

            if ($response->failed()) {
                Log::error('WA Error Response: ' . $response->body());
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('WA Notification Error: ' . $e->getMessage());
            return false;
        }
    }
}
