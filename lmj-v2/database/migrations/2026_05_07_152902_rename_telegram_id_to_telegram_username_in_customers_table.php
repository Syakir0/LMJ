<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            // Kita gunakan kolom phone sebagai identitas utama untuk WA/Telegram
            // Dan kita pastikan sistem bisa menyimpan Chat ID secara otomatis nanti jika mereka berinteraksi
            $table->string('telegram_chat_id')->nullable()->after('phone');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('telegram_chat_id');
        });
    }
};
