@extends('layouts.app')

@section('title', 'Tambah Pelanggan')
@section('breadcrumb', 'Pelanggan / Create')

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
    .error-msg { color: #e74c3c; font-size: 0.85rem; margin-top: 5px; }
</style>
@endsection

@section('content')
<div class="card" style="max-width: 600px; margin: 0 auto;">
    <h2 style="margin-top: 0; font-size: 1.2rem; margin-bottom: 25px;">
        <i class="fas fa-user-plus"></i> Registrasi Pelanggan Baru
    </h2>

    <form action="{{ route('customers.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="name">Nama Lengkap</label>
            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Contoh: Budi Santoso" required>
            @error('name') <div class="error-msg">{{ $message }}</div> @enderror
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label for="username">Username PPPoE</label>
                <input type="text" name="username" id="username" class="form-control" value="{{ old('username') }}" placeholder="budi_malacca" required>
                @error('username') <div class="error-msg">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label for="password">Password PPPoE</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="******" required>
                @error('password') <div class="error-msg">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-group">
            <label for="package_id">Paket Layanan</label>
            <select name="package_id" id="package_id" class="form-control" required>
                <option value="">-- Pilih Paket --</option>
                @foreach($packages as $package)
                    <option value="{{ $package->id }}" {{ old('package_id') == $package->id ? 'selected' : '' }}>
                        {{ $package->name }} ({{ $package->speed_limit }} Mbps) - Rp {{ number_format($package->price, 0, ',', '.') }}
                    </option>
                @endforeach
            </select>
            @error('package_id') <div class="error-msg">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="telegram_id">ID Telegram (Opsional)</label>
            <input type="text" name="telegram_id" id="telegram_id" class="form-control" value="{{ old('telegram_id') }}" placeholder="Contoh: 123456789">
            <small style="color: #95a5a6;">Untuk pengiriman notifikasi tagihan dan alert.</small>
        </div>

        <div style="margin-top: 30px; display: flex; gap: 10px;">
            <button type="submit" class="btn btn-primary" style="flex: 1;">
                <i class="fas fa-save"></i> Simpan & Sinkron ke RADIUS
            </button>
            <a href="{{ route('customers.index') }}" class="btn" style="background: #95a5a6; color: white; text-decoration: none; display: flex; align-items: center; justify-content: center;">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
