<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\NetworkDevice;
use App\Models\RadAcct;
use App\Models\Alert;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalCustomers = Customer::count();
        $onlineDevices = NetworkDevice::where('is_online', true)->count();
        $activeSessions = RadAcct::whereNull('acctstoptime')->count();
        $latestAlerts = Alert::latest()->take(5)->get();

        return view('dashboard', compact('totalCustomers', 'onlineDevices', 'activeSessions', 'latestAlerts'));
    }

    public function pppoeActive(\App\Services\MikroTikService $mikrotik)
    {
        $sessions = $mikrotik->getActivePppoe();
        return response()->json($sessions);
    }
}
