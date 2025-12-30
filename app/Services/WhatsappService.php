<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class WhatsAppService
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('GOWA_URL'); 
    }

    public function sendText($phone, $message)
    {
        return Http::post("{$this->baseUrl}/send-message", [
            'receiver' => $phone,
            'message'  => $message,
        ]);
    }
}