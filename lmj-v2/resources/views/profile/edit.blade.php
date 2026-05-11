@extends('layouts.app')

@section('title', 'Pengaturan Profil')
@section('breadcrumb', 'User / Profile')

@section('styles')
<style>
    .profile-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        padding: 30px;
        margin-bottom: 25px;
    }
    .profile-section-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        border-bottom: 1px solid #eee;
        padding-bottom: 10px;
    }
    .form-group { margin-bottom: 15px; }
    .form-group label { display: block; margin-bottom: 5px; font-weight: 600; color: #555; font-size: 0.9rem; }
    .form-input {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 0.95rem;
    }
    .form-input:focus { border-color: var(--accent-color); outline: none; }
    .btn-save {
        background: var(--accent-color);
        color: white;
        padding: 10px 25px;
        border-radius: 6px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: 0.2s;
    }
    .btn-save:hover { opacity: 0.9; transform: translateY(-1px); }
</style>
<script>
    function testTelegram() {
        showConfirm('Konfirmasi', 'Kirim pesan tes ke Telegram?', () => {
            const btn = event.target;
            const originalText = btn.innerText;
            btn.disabled = true;
            btn.innerText = 'Mengirim...';

            fetch("{{ route('settings.test-telegram') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                showAlert('Informasi', data.message, data.success ? 'success' : 'error');
            })
            .catch(error => {
                showAlert('Kesalahan', 'Terjadi kesalahan saat mencoba mengirim pesan.', 'error');
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerText = originalText;
            });
        });
    }
</script>
@endsection

@section('content')
<div style="max-width: 900px; margin: 0 auto;">
    
    <!-- Header Card -->
    <div class="profile-card" style="display: flex; align-items: center; gap: 25px; background: linear-gradient(135deg, #3498db 0%, #2980b9 100%); color: white;">
        <div style="width: 80px; height: 80px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; border: 3px solid rgba(255,255,255,0.5);">
            <i class="fas fa-user-shield"></i>
        </div>
        <div>
            <h1 style="margin: 0; font-size: 1.5rem;">{{ Auth::user()->name }}</h1>
            <div style="opacity: 0.9; font-size: 0.95rem;">{{ Auth::user()->email }}</div>
            <div style="margin-top: 8px;">
                <span style="background: rgba(255,255,255,0.2); padding: 3px 12px; border-radius: 15px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">
                    Administrator
                </span>
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr; gap: 10px;">
        <!-- Akun Form -->
        <div class="profile-card">
            <div class="profile-section-title">
                <i class="fas fa-id-card" style="color: #3498db;"></i> Informasi Profil
            </div>
            <form method="post" action="{{ route('profile.update') }}">
                @csrf
                @method('patch')
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="name" class="form-input" value="{{ old('name', Auth::user()->name) }}" required>
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" class="form-input" value="{{ old('email', Auth::user()->email) }}" required>
                </div>
                <button type="submit" class="btn-save">Simpan Perubahan</button>
            </form>
        </div>

        <!-- Password Form -->
        <div class="profile-card">
            <div class="profile-section-title">
                <i class="fas fa-lock" style="color: #e67e22;"></i> Ganti Password
            </div>
            <form method="post" action="{{ route('password.update') }}">
                @csrf
                @method('put')
                <div class="form-group">
                    <label>Password Saat Ini</label>
                    <input type="password" name="current_password" class="form-input" autocomplete="current-password">
                </div>
                <div class="form-group">
                    <label>Password Baru</label>
                    <input type="password" name="password" class="form-input" autocomplete="new-password">
                </div>
                <div class="form-group">
                    <label>Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" class="form-input" autocomplete="new-password">
                </div>
                <button type="submit" class="btn-save" style="background: #e67e22;">Update Password</button>
            </form>
        </div>

        <!-- Telegram Config -->
        <div class="profile-card">
            <div class="profile-section-title">
                <i class="fab fa-telegram" style="color: #0088cc;"></i> Integrasi Notifikasi Telegram
            </div>
            <p style="font-size: 0.85rem; color: #7f8c8d; margin-bottom: 20px;">
                Konfigurasi bot Telegram untuk menerima peringatan status network secara otomatis.
            </p>
            <form method="post" action="{{ route('settings.update') }}">
                @csrf
                <div class="form-group">
                    <label>Bot Token</label>
                    <input type="text" name="telegram_bot_token" class="form-input" value="{{ $settings['telegram_bot_token'] ?? '' }}" placeholder="123456:ABC-DEF...">
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label>Admin Chat ID</label>
                        <input type="text" name="telegram_admin_chat_id" class="form-input" value="{{ $settings['telegram_admin_chat_id'] ?? '' }}" placeholder="987654321">
                    </div>
                    <div class="form-group">
                        <label>NOC Chat ID</label>
                        <input type="text" name="telegram_noc_chat_id" class="form-input" value="{{ $settings['telegram_noc_chat_id'] ?? '' }}" placeholder="-123456789">
                    </div>
                </div>
                <div style="display: flex; gap: 10px; margin-top: 10px;">
                    <button type="submit" class="btn-save" style="background: #0088cc;">Simpan Konfigurasi Bot</button>
                    <button type="button" onclick="testTelegram()" class="btn-save" style="background: #27ae60;">Tes Kirim Pesan</button>
                </div>
            </form>
        </div>

        <!-- Hapus Akun -->
        <div class="profile-card" style="border: 1px solid #fadbd8;">
            <div class="profile-section-title" style="color: #c0392b;">
                <i class="fas fa-trash-alt"></i> Hapus Akun
            </div>
            <p style="font-size: 0.85rem; color: #7f8c8d;">Setelah akun Anda dihapus, semua sumber daya dan datanya akan dihapus secara permanen.</p>
            <button type="button" class="btn-save" style="background: #c0392b; margin-top: 10px;">Hapus Akun Saya</button>
        </div>
    </div>
</div>
@endsection

