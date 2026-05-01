<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('group')->default('general');
            $table->timestamps();
        });

        // Seed default Telegram settings
        DB::table('system_settings')->insert([
            ['key' => 'telegram_admin_chat_id', 'value' => env('TELEGRAM_ADMIN_CHAT_ID'), 'group' => 'telegram'],
            ['key' => 'telegram_noc_chat_id', 'value' => env('TELEGRAM_NOC_CHAT_ID'), 'group' => 'telegram'],
            ['key' => 'telegram_bot_token', 'value' => env('TELEGRAM_BOT_TOKEN'), 'group' => 'telegram'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
