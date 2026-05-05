<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\NetworkDeviceController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SystemSettingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard-stats', [DashboardController::class, 'stats'])->name('dashboard.stats');
    Route::get('/pppoe-active', [DashboardController::class, 'pppoeActive'])->name('pppoe.active');
    Route::resource('customers', CustomerController::class);
    Route::resource('packages', PackageController::class);
    Route::resource('devices', NetworkDeviceController::class);
    Route::resource('alerts', AlertController::class);
    Route::get('/alerts-latest', [AlertController::class, 'latest'])->name('alerts.latest');
    Route::get('/reports/customers', [ReportController::class, 'customers'])->name('reports.customers');
    Route::post('/settings', [SystemSettingController::class, 'update'])->name('settings.update');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

use App\Services\RouterosAPI;

Route::get('/test-mikrotik', function () {
    $api = new RouterosAPI();
    $api->debug = true;
    
    $host = config('services.mikrotik.host');
    $user = config('services.mikrotik.user');
    $pass = config('services.mikrotik.pass');
    $port = (int) config('services.mikrotik.port', 8728);

    echo "<body style='background:#1a1a1a; color:#eee; font-family:monospace; padding:20px;'>";
    echo "<h3>MikroTik Connection Diagnostic (v2)</h3>";
    echo "Host: {$host}<br>";
    echo "User: {$user}<br>";
    echo "Port: {$port}<br>";
    echo "PHP Version: " . PHP_VERSION . "<br><br>";

    if ($api->connect($host, $user, $pass)) {
        echo "<span style='color:#00ff00'>[OK] Connected Successfully!</span><br><br>";
        $identity = $api->comm('/system/identity/print');
        echo "Router Identity: " . ($identity[0]['name'] ?? 'Unknown') . "<br>";
        $api->disconnect();
    } else {
        echo "<span style='color:#ff3333'>[ERROR] Connection Failed!</span><br>";
        echo "Cek kredensial di .env dan pastikan IP MikroTik bisa di-ping dari Windows.";
    }
    echo "</body>";
});

Route::get('/test-notif', function () {
    return \App\Models\Alert::create([
        'title' => 'Test Notifikasi',
        'message' => 'Ini adalah pesan percobaan untuk memastikan popup dan suara bekerja.',
        'level' => 'info'
    ]);
});

Route::get('/test-db', function () {
    try {
        $db = DB::connection()->getDatabaseName();
        $radius = DB::connection('radius')->getDatabaseName();
        
        echo "Main DB: " . $db . " [OK]<br>";
        echo "Radius DB: " . $radius . " [OK]<br>";
        
        $users = DB::table('radcheck')->count();
        echo "Total RadCheck Users: " . $users;
    } catch (\Exception $e) {
        echo "Connection Error: " . $e->getMessage();
    }
});

