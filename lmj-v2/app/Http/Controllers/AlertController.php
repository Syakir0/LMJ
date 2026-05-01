<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use Illuminate\Http\Request;

class AlertController extends Controller
{
    public function index()
    {
        $alerts = Alert::latest()->paginate(20);
        return view('alerts.index', compact('alerts'));
    }
}
