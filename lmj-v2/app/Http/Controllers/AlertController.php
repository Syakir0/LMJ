<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use Illuminate\Http\Request;

class AlertController extends Controller
{
    public function index()
    {
        $alerts = Alert::latest()->paginate(15);
        return view('alerts.index', compact('alerts'));
    }

    public function latest(Request $request)
    {
        $lastId = $request->query('last_id', 0);
        $alerts = Alert::where('id', '>', $lastId)
            ->orderBy('id', 'asc')
            ->get();

        return response()->json($alerts);
    }
}
