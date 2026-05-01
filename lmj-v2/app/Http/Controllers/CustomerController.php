<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Package;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::with('package')->get();
        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        $packages = Package::all();
        return view('customers.create', compact('packages'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:customers,username',
            'password' => 'required|string|min:6',
            'package_id' => 'required|exists:packages,id',
            'telegram_id' => 'nullable|string',
        ]);

        Customer::create($validated);

        return redirect()->route('customers.index')->with('success', 'Customer created and synced to RADIUS successfully!');
    }

    public function edit(Customer $customer)
    {
        $packages = Package::all();
        return view('customers.edit', compact('customer', 'packages'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:customers,username,' . $customer->id,
            'password' => 'nullable|string|min:6',
            'package_id' => 'required|exists:packages,id',
            'telegram_id' => 'nullable|string',
            'status' => 'required|in:active,non-active,suspended',
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $customer->update($validated);

        return redirect()->route('customers.index')->with('success', 'Customer updated and synced to RADIUS successfully!');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Customer deleted from Dashboard and RADIUS!');
    }
}
