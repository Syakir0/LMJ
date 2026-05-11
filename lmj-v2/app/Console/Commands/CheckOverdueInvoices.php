<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Invoice;
use App\Models\Alert;
use App\Services\TelegramService;
use App\Services\MikroTikService;
use Carbon\Carbon;

class CheckOverdueInvoices extends Command
{
    protected $signature = 'billing:check-overdue';
    protected $description = 'Check for unpaid invoices that are past their due date and suspend customers';

    public function handle(TelegramService $tg, MikroTikService $mt)
    {
        $today = Carbon::today();
        $this->info("Checking for overdue invoices as of $today...");

        $overdueInvoices = Invoice::where('status', 'unpaid')
            ->where('due_date', '<', $today)
            ->with('customer')
            ->get();

        if ($overdueInvoices->isEmpty()) {
            return;
        }

        foreach ($overdueInvoices as $invoice) {
            // Re-fetch to be absolutely sure about status
            $invoice->refresh();
            if ($invoice->status !== 'unpaid') continue;
            
            $customer = $invoice->customer;
            
            // Update Invoice Status
            $invoice->update(['status' => 'overdue']);

            // Update Customer Status & Sync Due Date
            $customer->update([
                'status' => 'suspended',
                'payment_status' => 'isolated',
                'due_date' => $invoice->due_date, // Sync master due date
            ]);

            $this->warn("Customer {$customer->name} suspended due to unpaid invoice {$invoice->invoice_number}");

            // Create System Alert
            Alert::create([
                'customer_id' => $customer->id,
                'title' => 'Layanan Terisolir',
                'message' => "Pelanggan {$customer->name} otomatis terisolir karena tagihan {$invoice->invoice_number} melewati jatuh tempo.",
                'level' => 'critical'
            ]);

            // Send Telegram Notification
            $invoiceNum = str_replace('_', '\_', $invoice->invoice_number);
            $custName = str_replace('_', '\_', $customer->name);
            
            $msg = "🚫 *LAYANAN TERISOLIR (OVERDUE)*\n\n"
                 . "Pelanggan: {$custName}\n"
                 . "No. Invoice: `{$invoiceNum}`\n"
                 . "Jatuh Tempo: " . $invoice->due_date->format('d M Y') . "\n\n"
                 . "Layanan internet Anda telah dinonaktifkan sementara. Silakan lakukan pembayaran segera untuk mengaktifkan kembali layanan.";
            
            $tg->sendToAdmin("🚫 *SUSPENDED:* {$custName} - {$invoiceNum}");
            if ($customer->telegram_chat_id) {
                $tg->sendMessage($customer->telegram_chat_id, $msg);
            }

            // Call MikroTik API to actually block the user
            $mt->suspendUser($customer->username);
        }

        $this->info("Overdue check completed.");
    }
}
