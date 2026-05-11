<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\NetworkDevice;
use App\Models\RadAcct;
use App\Models\Alert;
use Illuminate\Http\Request;

use App\Models\RadPostAuth;

class DashboardController extends Controller
{
    public function index(\App\Services\MikroTikService $mikrotik)
    {
        $totalCustomers = Customer::count();
        $onlineDevices = NetworkDevice::where('is_online', true)->count();
        
        $mikrotikStats = $mikrotik->getSystemResource();
        $interfaces = $mikrotik->getInterfaces();
        $activeSessions = count($mikrotik->getActivePppoe());
        
        $latestAlerts = Alert::latest()->take(5)->get();
        $failedLogins = RadPostAuth::where('reply', 'Access-Reject')
            ->orderBy('authdate', 'desc')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'totalCustomers', 
            'onlineDevices', 
            'activeSessions', 
            'latestAlerts', 
            'failedLogins',
            'mikrotikStats',
            'interfaces'
        ));
    }

    public function pppoeActive(\App\Services\MikroTikService $mikrotik)
    {
        $sessions = $mikrotik->getActivePppoe();
        return response()->json($sessions);
    }

    public function stats(\App\Services\MikroTikService $mikrotik)
    {
        return response()->json([
            'mikrotikStats' => $mikrotik->getSystemResource(),
            'interfaces' => $mikrotik->getInterfaces(),
            'latestAlerts' => \App\Models\Alert::latest()->take(5)->get(),
            'failedLogins' => \App\Models\RadPostAuth::where('reply', 'Access-Reject')
                ->orderBy('authdate', 'desc')
                ->take(5)
                ->get()
        ]);
    }
}
