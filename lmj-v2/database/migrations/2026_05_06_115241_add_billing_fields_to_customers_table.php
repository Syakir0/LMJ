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
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'billing_date')) {
                $table->integer('billing_date')->default(1)->after('package_id'); // Tanggal tagihan setiap bulan
            }
            if (!Schema::hasColumn('customers', 'due_date')) {
                $table->date('due_date')->nullable()->after('billing_date'); // Tanggal jatuh tempo
            }
            if (!Schema::hasColumn('customers', 'payment_status')) {
                $table->enum('payment_status', ['paid', 'unpaid', 'isolated'])->default('paid')->after('due_date'); // Status pembayaran
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['billing_date', 'due_date', 'payment_status']);
        });
    }
};
