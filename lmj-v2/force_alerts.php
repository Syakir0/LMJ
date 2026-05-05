<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$ports = ['ether1','ether2','ether3','ether4','ether5','wlan1','bridge-pppoe'];
foreach($ports as $p) {
    \App\Models\Alert::create([
        'title' => "Port $p is Connected",
        'level' => 'info',
        'message' => "✅ Kabel pada port $p baru saja Dicolok/Aktif."
    ]);
}
echo "Forced 7 Alerts successfully.\n";
