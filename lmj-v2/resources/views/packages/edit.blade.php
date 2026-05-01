@extends('layouts.app')

@section('title', 'Edit Paket')
@section('breadcrumb', 'Paket / Edit')

@section('styles')
<style>
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #34495e; }
    .form-control { 
        width: 100%; 
        padding: 10px; 
        border: 1px solid #ddd; 
        border-radius: 4px; 
        box-sizing: border-box;
    }
    .form-control:focus { border-color: var(--accent-color); outline: none; }
</style>
@endsection

@section('content')
<div class="card" style="max-width: 600px; margin: 0 auto;">
    <h2 style="margin-top: 0; font-size: 1.2rem; margin-bottom: 25px;">
        <i class="fas fa-edit"></i> Edit Paket: {{ $package->name }}
    </h2>

    <form action="{{ route('packages.update', $package) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="name">Nama Paket</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $package->name) }}" required>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label for="speed_limit">Speed Limit (Mbps)</label>
                <input type="number" name="speed_limit" id="speed_limit" class="form-control" value="{{ old('speed_limit', $package->speed_limit) }}" required>
            </div>
            <div class="form-group">
                <label for="price">Harga (Rp)</label>
                <input type="number" name="price" id="price" class="form-control" value="{{ old('price', $package->price) }}" required>
            </div>
        </div>

        <div class="form-group">
            <label for="mikrotik_profile">MikroTik Profile (Opsional)</label>
            <input type="text" name="mikrotik_profile" id="mikrotik_profile" class="form-control" value="{{ old('mikrotik_profile', $package->mikrotik_profile) }}">
        </div>

        <div style="margin-top: 30px; display: flex; gap: 10px;">
            <button type="submit" class="btn btn-primary" style="flex: 1;">
                <i class="fas fa-save"></i> Perbarui Paket
            </button>
            <a href="{{ route('packages.index') }}" class="btn" style="background: #95a5a6; color: white; text-decoration: none; display: flex; align-items: center; justify-content: center;">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
