<?php

namespace App\Http\Controllers;

use App\Models\NetworkDevice;
use Illuminate\Http\Request;

class NetworkDeviceController extends Controller
{
    public function index()
    {
        $devices = NetworkDevice::all();
        return view('devices.index', compact('devices'));
    }
}
