<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Customer;
use App\Models\Package;
use App\Models\RadCheck;

class RadiusSyncTest extends TestCase
{
    /**
     * Test sync customer to radius.
     */
    public function test_customer_creation_syncs_to_radius(): void
    {
        // Cleanup old test data
        Customer::where('username', 'testuser_123')->delete();
        RadCheck::where('username', 'testuser_123')->delete();

        // 1. Create a Package first
        $package = Package::firstOrCreate(
            ['name' => 'Test Package'],
            ['speed_limit' => 10, 'price' => 100000]
        );

        // 2. Create a Customer
        $customerData = [
            'name' => 'Test User',
            'username' => 'testuser_123',
            'password' => 'secret123',
            'package_id' => $package->id,
            'status' => 'active'
        ];

        $customer = Customer::create($customerData);

        // 3. Assert data exists in lmj_dashboard (mysql)
        $this->assertDatabaseHas('customers', [
            'username' => 'testuser_123'
        ], 'mysql');

        // 4. Assert data exists in radius (radius connection)
        $this->assertDatabaseHas('radcheck', [
            'username' => 'testuser_123',
            'attribute' => 'Cleartext-Password',
            'value' => 'secret123'
        ], 'radius');

        // Cleanup after test
        $customer->delete();
        RadCheck::where('username', 'testuser_123')->delete();
    }
}
