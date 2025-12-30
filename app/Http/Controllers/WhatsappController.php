<?php

namespace App\Http\Controllers;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;

class WhatsappController extends Controller
{
    public function sendMessage(Request $request, WhatsAppService $wa)
    {
        $request->validate(['phone' => 'required', 'message' => 'required']);

        $response = $wa->sendText($request->phone, $request->message);

        if ($response->successful()) {
            return back()->with('success', 'Message sent!');
        }
        return back()->with('error', 'Failed to send message.');
    }
}
