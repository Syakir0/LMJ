<?php

namespace App\Http\Controllers;

use App\Models\Package;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::all();
        return view('packages.index', compact('packages'));
    }

    public function create()
    {
        return view('packages.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'speed_limit' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'mikrotik_profile' => 'nullable|string',
        ]);

        Package::create($validated);

        return redirect()->route('packages.index')->with('success', 'Paket berhasil ditambahkan!');
    }

    public function edit(Package $package)
    {
        return view('packages.edit', compact('package'));
    }

    public function update(Request $request, Package $package)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'speed_limit' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'mikrotik_profile' => 'nullable|string',
        ]);

        $package->update($validated);

        return redirect()->route('packages.index')->with('success', 'Paket berhasil diperbarui!');
    }

    public function destroy(Package $package)
    {
        $package->delete();
        return redirect()->route('packages.index')->with('success', 'Paket berhasil dihapus!');
    }
}
