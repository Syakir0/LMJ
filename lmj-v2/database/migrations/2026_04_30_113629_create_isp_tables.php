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
        // 1. Tabel Paket Internet
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');           // Misal: Home-10Mbps
            $table->integer('speed_limit');   // In Mbps
            $table->decimal('price', 12, 2);
            $table->string('mikrotik_profile')->nullable();
            $table->timestamps();
        });

        // 2. Tabel Pelanggan (Relasi ke Radius standar nanti via username)
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username')->unique(); // Ini yang jadi radcheck.username
            $table->string('password');           // Ini yang jadi radcheck.value
            $table->string('pppoe_ip')->nullable();
            $table->foreignId('package_id')->constrained('packages');
            $table->string('telegram_id')->nullable();
            $table->enum('status', ['active', 'non-active', 'suspended'])->default('active');
            $table->timestamps();
        });

        // 3. Tabel Network Devices (Monitoring)
        Schema::create('network_devices', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('ip_address');
            $table->enum('type', ['mikrotik', 'tenda', 'olt', 'other']);
            $table->boolean('is_online')->default(false);
            $table->timestamp('last_seen')->nullable();
            $table->string('snmp_community')->default('public');
            $table->timestamps();
        });

        // 4. Tabel Alerts & Notifications
        Schema::create('alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->nullable()->constrained('network_devices')->onDelete('cascade');
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('cascade');
            $table->string('title');
            $table->text('message');
            $table->enum('level', ['info', 'warning', 'critical'])->default('info');
            $table->boolean('is_sent')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alerts');
        Schema::dropIfExists('network_devices');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('packages');
    }
};
