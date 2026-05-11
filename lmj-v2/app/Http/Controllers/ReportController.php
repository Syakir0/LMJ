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
        $csv->insertOne(['ID', 'Nama', 'Username', 'IP PPPoE', 'Paket', 'Status', 'Tanggal Daftar']);
        
        // Add Data
        foreach ($customers as $customer) {
            $csv->insertOne([
                $customer->id,
                $customer->name,
                $customer->username,
                $customer->pppoe_ip ?? '-',
                $customer->package->name ?? '-',
                strtoupper($customer->status),
                $customer->created_at->toDateTimeString(),
            ]);
        }
        
        $filename = 'laporan_pelanggan_' . date('Y-m-d') . '.csv';
        
        return response((string) $csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
