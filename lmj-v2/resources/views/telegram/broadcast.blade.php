@extends('layouts.app')

@section('title', 'Broadcast Telegram')
@section('breadcrumb', 'Telegram / Broadcast')

@section('content')
<div class="card" style="max-width: 800px; margin: 0 auto;">
    <h2 style="margin-top:0; font-size: 1.2rem; border-bottom: 1px solid #eee; padding-bottom: 10px;">
        <i class="fas fa-bullhorn"></i> Kirim Pengumuman (Broadcast)
    </h2>

    <div style="background: #e3f2fd; padding: 15px; border-radius: 8px; margin: 20px 0; color: #1976d2; font-size: 0.9rem;">
        <i class="fas fa-info-circle"></i> Saat ini terdapat <strong>{{ $totalSubscribers }}</strong> pelanggan yang terhubung ke Bot Telegram.
    </div>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('telegram.broadcast.send') }}" method="POST">
        @csrf
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; font-weight: bold;">Target Pelanggan</label>
            <select name="target" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                <option value="all">Semua yang Terhubung ({{ $totalSubscribers }})</option>
                <option value="active">Hanya Pelanggan Aktif</option>
                <option value="suspended">Hanya Pelanggan Terisolir (Suspended)</option>
            </select>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; font-weight: bold;">Pesan Pengumuman</label>
            <textarea name="message" rows="6" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-family: inherit;" placeholder="Tulis pengumuman di sini..."></textarea>
            <small style="color: #7f8c8d;">Tips: Gunakan *teks* untuk tebal dan _teks_ untuk miring.</small>
        </div>

        <div style="text-align: right;">
            <button type="submit" class="btn btn-primary" onclick="confirmAction(event, 'Konfirmasi Broadcast', 'Kirim pesan ini ke semua pelanggan terpilih?')">
                <i class="fas fa-paper-plane"></i> Kirim Sekarang
            </button>
        </div>
    </form>
</div>
@endsection
