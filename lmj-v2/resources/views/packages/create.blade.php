@extends('layouts.app')

@section('title', 'Tambah Paket')
@section('breadcrumb', 'Paket / Create')

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
        <i class="fas fa-box"></i> Tambah Paket Layanan Baru
    </h2>

    <form action="{{ route('packages.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="name">Nama Paket</label>
            <input type="text" name="name" id="name" class="form-control" placeholder="Contoh: Home-10M" required>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label for="speed_limit">Speed Limit (Mbps)</label>
                <input type="number" name="speed_limit" id="speed_limit" class="form-control" placeholder="10" required>
            </div>
            <div class="form-group">
                <label for="price">Harga (Rp)</label>
                <input type="number" name="price" id="price" class="form-control" placeholder="150000" required>
            </div>
        </div>

        <div class="form-group">
            <label for="mikrotik_profile">MikroTik Profile (Opsional)</label>
            <input type="text" name="mikrotik_profile" id="mikrotik_profile" class="form-control" placeholder="UP-10M">
        </div>

        <div style="margin-top: 30px; display: flex; gap: 10px;">
            <button type="submit" class="btn btn-primary" style="flex: 1;">
                <i class="fas fa-save"></i> Simpan Paket
            </button>
            <a href="{{ route('packages.index') }}" class="btn" style="background: #95a5a6; color: white; text-decoration: none; display: flex; align-items: center; justify-content: center;">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
