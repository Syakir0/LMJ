@extends('layouts.app')

@section('title', 'Edit Pelanggan')
@section('breadcrumb', 'Pelanggan / Edit')

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
        <i class="fas fa-user-edit"></i> Edit Pelanggan: {{ $customer->name }}
    </h2>

    <form action="{{ route('customers.update', $customer) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="name">Nama Lengkap</label>
            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $customer->name) }}" required>
            @error('name') <div class="error-msg">{{ $message }}</div> @enderror
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label for="username">Username PPPoE</label>
                <input type="text" name="username" id="username" class="form-control" value="{{ old('username', $customer->username) }}" required>
                @error('username') <div class="error-msg">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label for="password">Password PPPoE (Kosongkan jika tidak ganti)</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="******">
                @error('password') <div class="error-msg">{{ $message }}</div> @enderror
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label for="package_id">Paket Layanan</label>
                <select name="package_id" id="package_id" class="form-control" required>
                    @foreach($packages as $package)
                        <option value="{{ $package->id }}" {{ old('package_id', $customer->package_id) == $package->id ? 'selected' : '' }}>
                            {{ $package->name }}
                        </option>
                    @endforeach
                </select>
                @error('package_id') <div class="error-msg">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label for="status">Status Internet</label>
                <select name="status" id="status" class="form-control" required>
                    <option value="active" {{ old('status', $customer->status) == 'active' ? 'selected' : '' }}>ACTIVE</option>
                    <option value="non-active" {{ old('status', $customer->status) == 'non-active' ? 'selected' : '' }}>NON-ACTIVE</option>
                    <option value="suspended" {{ old('status', $customer->status) == 'suspended' ? 'selected' : '' }}>SUSPENDED</option>
                </select>
                @error('status') <div class="error-msg">{{ $message }}</div> @enderror
            </div>
        </div>

        <!-- Billing Info -->
        <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #eee;">
            <h3 style="margin-top: 0; font-size: 1rem; color: #2c3e50;"><i class="fas fa-file-invoice-dollar"></i> Informasi Tagihan</h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="billing_date">Tanggal Tagihan (Tiap Bulan)</label>
                    <input type="number" name="billing_date" id="billing_date" class="form-control" value="{{ old('billing_date', $customer->billing_date) }}" min="1" max="31" required>
                    @error('billing_date') <div class="error-msg">{{ $message }}</div> @enderror
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="due_date">Jatuh Tempo</label>
                    <input type="date" name="due_date" id="due_date" class="form-control" value="{{ old('due_date', $customer->due_date ? \Carbon\Carbon::parse($customer->due_date)->format('Y-m-d') : '') }}">
                    @error('due_date') <div class="error-msg">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-group" style="margin-top: 15px; margin-bottom: 0;">
                <label for="payment_status">Status Pembayaran</label>
                <select name="payment_status" id="payment_status" class="form-control" required>
                    <option value="paid" {{ old('payment_status', $customer->payment_status) == 'paid' ? 'selected' : '' }}>Lunas (Paid)</option>
                    <option value="unpaid" {{ old('payment_status', $customer->payment_status) == 'unpaid' ? 'selected' : '' }}>Belum Bayar (Unpaid)</option>
                    <option value="isolated" {{ old('payment_status', $customer->payment_status) == 'isolated' ? 'selected' : '' }}>Isolir (Isolated)</option>
                </select>
                @error('payment_status') <div class="error-msg">{{ $message }}</div> @enderror
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label for="phone">Nomor WA/HP</label>
                <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone', $customer->phone) }}" required>
                <small style="color: #95a5a6;">Identitas utama untuk notifikasi Telegram.</small>
            </div>
            <div class="form-group">
                <label for="telegram_chat_id">Telegram Chat ID</label>
                <input type="text" name="telegram_chat_id" id="telegram_chat_id" class="form-control" value="{{ old('telegram_chat_id', $customer->telegram_chat_id) }}" readonly>
            </div>
        </div>

        <div style="margin-top: 30px; display: flex; gap: 10px;">
            <button type="submit" class="btn btn-primary" style="flex: 1;">
                <i class="fas fa-save"></i> Perbarui Data
            </button>
            <a href="{{ route('customers.index') }}" class="btn" style="background: #95a5a6; color: white; text-decoration: none; display: flex; align-items: center; justify-content: center;">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
