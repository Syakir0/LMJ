<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Customer;
use App\Services\TelegramService;
use App\Services\MikroTikService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with('customer')->orderBy('created_at', 'desc')->paginate(15);
        return view('invoices.index', compact('invoices'));
    }

    public function markAsPaid(Invoice $invoice, TelegramService $tg, MikroTikService $mt)
    {
        if ($invoice->status === 'paid') {
            return back()->with('error', 'Tagihan ini sudah dibayar.');
        }

        $invoice->update([
            'status' => 'paid',
            'paid_at' => Carbon::now(),
            'payment_method' => 'manual',
        ]);

        // Update Customer Status & Sync Due Date
        $customer = $invoice->customer;
        
        // Calculate next due date: Add 1 month to the CURRENT invoice due date
        // This makes sure that if they pay May 10, the next one is June 10.
        $nextDueDate = Carbon::parse($invoice->due_date)->addMonth();

        $customer->update([
            'status' => 'active',
            'payment_status' => 'paid',
            'due_date' => $nextDueDate, // Move forward to the next month's deadline
        ]);

        // Send Telegram Notification
        $invoiceNum = str_replace('_', '\_', $invoice->invoice_number);
        $custName = str_replace('_', '\_', $customer->name);

        $msg = "✅ *PEMBAYARAN DITERIMA*\n\n"
             . "Pelanggan: {$custName}\n"
             . "No. Invoice: `{$invoiceNum}`\n"
                 . "Jumlah: Rp " . number_format($invoice->amount, 0, ',', '.') . "\n"
             . "Status: *LUNAS*\n\n"
             . "Terima kasih telah melakukan pembayaran. Layanan internet Anda telah aktif kembali.";
        
        $tg->sendToAdmin("✅ *PAID:* {$custName} - {$invoiceNum}");
        if ($customer->telegram_chat_id) {
            $tg->sendMessage($customer->telegram_chat_id, $msg);
        }

        // Call MikroTik API to reactivate user
        $mt->reactivateUser($customer->username);

        return back()->with('success', 'Pembayaran berhasil dikonfirmasi dan layanan diaktifkan kembali.');
    }
}
