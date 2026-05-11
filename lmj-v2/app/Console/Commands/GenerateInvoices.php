<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Customer;
use App\Models\Invoice;
use App\Services\TelegramService;
use Carbon\Carbon;

class GenerateInvoices extends Command
{
    protected $signature = 'billing:generate';
    protected $description = 'Generate monthly invoices for customers based on their billing date';

    public function handle(TelegramService $tg)
    {
        $today = Carbon::today();
        $dayOfMonth = $today->day;
        $month = $today->month;
        $year = $today->year;

        $this->info("Checking for customers with billing date: $dayOfMonth...");

        $customers = Customer::where('billing_date', $dayOfMonth)
            ->get();

        if ($customers->isEmpty()) {
            $this->info("No customers found for billing today.");
            return;
        }

        foreach ($customers as $customer) {
            if ($customer->status !== 'active') {
                $this->line("Skipping {$customer->name} because status is {$customer->status}.");
                continue;
            }
            // Check if invoice already exists for this month/year
            $exists = Invoice::where('customer_id', $customer->id)
                ->where('month', $month)
                ->where('year', $year)
                ->exists();

            if ($exists) {
                $this->line("Invoice for {$customer->name} already exists. Skipping.");
                continue;
            }

            // Create Invoice
            $invoiceNumber = 'INV-' . $year . str_pad($month, 2, '0', STR_PAD_LEFT) . '-' . str_pad($customer->id, 4, '0', STR_PAD_LEFT);
            $amount = $customer->package->price;
            $dueDate = $today->copy()->addDays(7); // Default due date 7 days from now

            $invoice = Invoice::create([
                'customer_id' => $customer->id,
                'invoice_number' => $invoiceNumber,
                'month' => $month,
                'year' => $year,
                'amount' => $amount,
                'status' => 'unpaid',
                'due_date' => $dueDate,
            ]);

            // Update customer payment status to unpaid if it was paid
            if ($customer->payment_status === 'paid') {
                $customer->update(['payment_status' => 'unpaid', 'due_date' => $dueDate]);
            }

            $this->info("Generated: $invoiceNumber for {$customer->name}");

            // Send Telegram Notification
            $msg = "🧾 *TAGIHAN BARU TERBIT*\n\n"
                 . "Pelanggan: {$customer->name}\n"
                 . "No. Invoice: `{$invoiceNumber}`\n"
                 . "Jumlah: Rp " . number_format($amount, 0, ',', '.') . "\n"
                 . "Jatuh Tempo: " . $dueDate->format('d M Y') . "\n\n"
                 . "Silakan lakukan pembayaran sebelum tanggal jatuh tempo agar internet tetap aktif.";
            
            $tg->sendToAdmin($msg);
            if ($customer->telegram_chat_id) {
                $tg->sendMessage($customer->telegram_chat_id, $msg);
            }
        }

        $this->info("Billing generation completed.");
    }
}
