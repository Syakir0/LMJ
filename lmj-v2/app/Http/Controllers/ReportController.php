<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use League\Csv\Writer;
use SplTempFileObject;

class ReportController extends Controller
{
    public function customers()
    {
        $customers = Customer::with('package')->get();
        
        $csv = Writer::createFromFileObject(new SplTempFileObject());
        
        // Add Header
        $csv->insertOne([
            'ID', 'Nama', 'Username', 'Paket', 'Harga', 
            'Tgl Tagihan', 'Jatuh Tempo', 'Status Pembayaran', 
            'Status Akun', 'Phone', 'Telegram ID'
        ]);
        
        // Add Data
        foreach ($customers as $c) {
            $csv->insertOne([
                $c->id,
                $c->name,
                $c->username,
                $c->package->name ?? '-',
                $c->package->price ?? 0,
                $c->billing_date,
                $c->due_date ? \Carbon\Carbon::parse($c->due_date)->format('Y-m-d') : '-',
                strtoupper($c->payment_status),
                strtoupper($c->status),
                $c->phone,
                $c->telegram_chat_id
            ]);
        }
        
        $filename = 'daftar_pelanggan_' . date('Y-m-d') . '.csv';
        
        return response((string) $csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
