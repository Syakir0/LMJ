<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Services\TelegramService;
use Illuminate\Http\Request;

class TelegramBroadcastController extends Controller
{
    public function index()
    {
        $totalSubscribers = Customer::whereNotNull('telegram_chat_id')->count();
        return view('telegram.broadcast', compact('totalSubscribers'));
    }

    public function send(Request $request, TelegramService $tg)
    {
        $request->validate([
            'message' => 'required|string',
            'target' => 'required|in:all,active,suspended'
        ]);

        $query = Customer::whereNotNull('telegram_chat_id');

        if ($request->target === 'active') {
            $query->where('status', 'active');
        } elseif ($request->target === 'suspended') {
            $query->where('status', 'suspended');
        }

        $customers = $query->get();
        $successCount = 0;

        foreach ($customers as $customer) {
            $formattedMsg = "📢 *PENGUMUMAN*\n\n" . $request->message;
            if ($tg->sendMessage($customer->telegram_chat_id, $formattedMsg)) {
                $successCount++;
            }
        }

        return back()->with('success', "Pesan berhasil dikirim ke $successCount pelanggan.");
    }
}
