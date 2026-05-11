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

        <!-- Billing Info -->
        <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #eee;">
            <h3 style="margin-top: 0; font-size: 1rem; color: #2c3e50;"><i class="fas fa-file-invoice-dollar"></i> Informasi Tagihan</h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="billing_date">Tanggal Tagihan (Setiap Bulan)</label>
                    <input type="number" name="billing_date" id="billing_date" class="form-control" value="{{ old('billing_date', 1) }}" min="1" max="31" required>
                    @error('billing_date') <div class="error-msg">{{ $message }}</div> @enderror
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="due_date">Jatuh Tempo (Bulan Ini)</label>
                    <input type="date" name="due_date" id="due_date" class="form-control" value="{{ old('due_date') }}">
                    @error('due_date') <div class="error-msg">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-group" style="margin-top: 15px; margin-bottom: 0;">
                <label for="payment_status">Status Pembayaran</label>
                <select name="payment_status" id="payment_status" class="form-control" required>
                    <option value="paid" {{ old('payment_status') == 'paid' ? 'selected' : '' }}>Lunas (Paid)</option>
                    <option value="unpaid" {{ old('payment_status') == 'unpaid' ? 'selected' : '' }}>Belum Bayar (Unpaid)</option>
                    <option value="isolated" {{ old('payment_status') == 'isolated' ? 'selected' : '' }}>Isolir (Isolated)</option>
                </select>
                @error('payment_status') <div class="error-msg">{{ $message }}</div> @enderror
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label for="phone">Nomor WA/HP (Wajib)</label>
                <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone') }}" placeholder="08123456789" required>
                <small style="color: #95a5a6;">Gunakan format 08xxx atau 62xxx. Digunakan untuk identitas Telegram.</small>
            </div>
            <div class="form-group">
                <label for="telegram_chat_id">Telegram Chat ID</label>
                <input type="text" name="telegram_chat_id" id="telegram_chat_id" class="form-control" value="{{ old('telegram_chat_id') }}" placeholder="Otomatis terisi saat chat bot" readonly>
            </div>
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
