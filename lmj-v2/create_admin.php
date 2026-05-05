<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

DB::table('users')->where('email', 'admin@lmj.com')->delete();

$user = User::create([
    'name' => 'Admin LMJ',
    'email' => 'admin@lmj.com',
    'password' => Hash::make('admin123'),
]);

echo "User created successfully: " . $user->email . "\n";
