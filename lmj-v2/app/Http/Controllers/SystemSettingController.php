<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use App\Services\TelegramService;
use Illuminate\Http\Request;

class SystemSettingController extends Controller
{
    public function update(Request $request)
    {
        $settings = $request->except(['_token', '_method']);

        foreach ($settings as $key => $value) {
            SystemSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'group' => 'telegram']
            );
        }

        return back()->with('status', 'settings-updated');
    }

    public function testTelegram(TelegramService $tg)
    {
        $success = $tg->sendToNoc("Halo! Ini adalah pesan tes dari Sistem Monitoring ISP Anda. Bot sudah berhasil terhubung!");

        if ($success) {
            return response()->json(['message' => 'Pesan tes berhasil dikirim ke Telegram!']);
        }

        $error = session('tg_last_error', 'Gagal mengirim pesan. Pastikan Token & Chat ID benar, dan Anda sudah klik START di bot.');
        session()->forget('tg_last_error');

        return response()->json(['message' => $error], 500);
    }
}
