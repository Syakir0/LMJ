<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TelegramService;
use Illuminate\Support\Facades\Log;

class TelegramController extends Controller
{
    public function webhook(Request $request, TelegramService $telegramService)
    {
        Log::info('Telegram Webhook Received:', $request->all());
        
        $telegramService->handleUpdate($request->all());
        
        return response()->json(['status' => 'ok']);
    }
}
