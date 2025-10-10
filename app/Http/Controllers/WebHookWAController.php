<?php

namespace App\Http\Controllers;

use GreenApi\RestApi\GreenApiClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebHookWAController extends Controller
{
    public function webHook(Request $request)
    {
        $greenApi = new GreenApiClient('7105339334', 'f0317185b97246c6bba0d986105e50af8fa7986db8804c74a6');

        $greenApi->webhooks->startReceivingNotifications(function ($typeWebhook, $body) {
            Log::info("Webhook type: {$typeWebhook}", ['body' => $body]);
        });

        Log::info('Webhook received: ', $request->all());
        return response()->json(['status' => 'success'], 200);
    }
}
