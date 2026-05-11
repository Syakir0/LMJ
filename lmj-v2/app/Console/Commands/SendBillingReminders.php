<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Invoice;
use App\Services\TelegramService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SendBillingReminders extends Command
{
    protected $signature = 'billing:send-reminders';
    protected $description = 'Send automatic billing reminders (H-3 and H-1) to customers';

    public function handle(TelegramService $tg)
    {
        $today = Carbon::today();
        $this->info("Checking for billing reminders on {$today->toDateString()}...");

        $unpaidInvoices = Invoice::where('status', 'unpaid')->with('customer')->get();
        $sentCount = 0;

        foreach ($unpaidInvoices as $invoice) {
            if (!$invoice->due_date || !$invoice->customer || !$invoice->customer->telegram_chat_id) {
                continue;
            }

            $customer = $invoice->customer;
            // Calculate exact days remaining (positive means future, negative means past)
            // Ensure we compare dates without time components for accuracy
            $dueDate = Carbon::parse($invoice->due_date)->startOfDay();
            $daysUntilDue = (int) $today->diffInDays($dueDate, false);

            $msg = "";
            $invoiceNum = str_replace('_', '\_', $invoice->invoice_number);
            $custName = str_replace('_', '\_', $customer->name);

            if ($daysUntilDue === 3) {
                // Reminder H-3
                $msg = "🔔 *PENGINGAT TAGIHAN (H-3)*\n\n"
                     . "Halo {$custName},\n"
                     . "Ini adalah pengingat otomatis bahwa tagihan internet Anda akan jatuh tempo dalam *3 hari*.\n\n"
                     . "No. Invoice: `{$invoiceNum}`\n"
                     . "Jumlah: Rp " . number_format($invoice->amount, 0, ',', '.') . "\n"
                     . "Jatuh Tempo: " . $invoice->due_date->format('d M Y') . "\n\n"
                     . "Abaikan pesan ini jika Anda sudah melakukan pembayaran. Terima kasih.";
            } elseif ($daysUntilDue === 1) {
                // Reminder H-1 (Besok)
                $msg = "⚠️ *PERINGATAN JATUH TEMPO (BESOK)*\n\n"
                     . "Halo {$custName},\n"
                     . "Tagihan internet Anda akan jatuh tempo *BESOK*.\n\n"
                     . "No. Invoice: `{$invoiceNum}`\n"
                     . "Jumlah: Rp " . number_format($invoice->amount, 0, ',', '.') . "\n"
                     . "Jatuh Tempo: " . $invoice->due_date->format('d M Y') . "\n\n"
                     . "Mohon segera lakukan pembayaran untuk menghindari pemutusan layanan sementara. Terima kasih atas kerjasamanya.";
            }

            if ($msg !== "") {
                if ($tg->sendMessage($customer->telegram_chat_id, $msg)) {
                    $this->info("Reminder (H-{$daysUntilDue}) sent to {$customer->name}");
                    $sentCount++;
                }
            }
        }

        $this->info("Billing reminders completed. Total sent: $sentCount");
    }
}
