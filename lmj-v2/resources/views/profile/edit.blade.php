@extends('layouts.app')

@section('title', 'Pengaturan Profil')
@section('breadcrumb', 'User / Profile')

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- User Info Header -->
            <div class="p-6 bg-white shadow sm:rounded-lg mb-6" style="display: flex; align-items: center; gap: 30px;">
                <div style="width: 100px; height: 100px; background: var(--accent-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 3rem;">
                    <i class="fas fa-user"></i>
                </div>
                <div>
                    <h1 style="margin: 0; font-size: 1.8rem; color: var(--primary-color);">{{ Auth::user()->name }}</h1>
                    <p style="margin: 5px 0 0; color: #7f8c8d; font-size: 1.1rem;">
                        <i class="fas fa-envelope"></i> {{ Auth::user()->email }}
                    </p>
                    <div style="margin-top: 10px;">
                        <span style="background: #e3f2fd; color: #1976d2; padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: bold;">
                            <i class="fas fa-shield-alt"></i> Administrator
                        </span>
                    </div>
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <h2 style="font-size: 1.2rem; margin-bottom: 20px; border-bottom: 2px solid var(--light-bg); padding-bottom: 10px;">
                        <i class="fas fa-id-card"></i> Informasi Akun
                    </h2>
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <h2 style="font-size: 1.2rem; margin-bottom: 20px; border-bottom: 2px solid var(--light-bg); padding-bottom: 10px;">
                        <i class="fas fa-key"></i> Ganti Password
                    </h2>
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <h2 style="font-size: 1.2rem; margin-bottom: 20px; border-bottom: 2px solid var(--light-bg); padding-bottom: 10px;">
                        <i class="fab fa-telegram"></i> Integrasi Telegram
                    </h2>
                    <form method="post" action="{{ route('settings.update') }}" class="space-y-6">
                        @csrf
                        <div>
                            <x-input-label for="telegram_bot_token" :value="__('Bot Token')" />
                            <x-text-input id="telegram_bot_token" name="telegram_bot_token" type="text" class="mt-1 block w-full" :value="$settings['telegram_bot_token'] ?? ''" />
                        </div>
                        <div>
                            <x-input-label for="telegram_admin_chat_id" :value="__('Admin Chat ID')" />
                            <x-text-input id="telegram_admin_chat_id" name="telegram_admin_chat_id" type="text" class="mt-1 block w-full" :value="$settings['telegram_admin_chat_id'] ?? ''" />
                        </div>
                        <div>
                            <x-input-label for="telegram_noc_chat_id" :value="__('NOC Chat ID')" />
                            <x-text-input id="telegram_noc_chat_id" name="telegram_noc_chat_id" type="text" class="mt-1 block w-full" :value="$settings['telegram_noc_chat_id'] ?? ''" />
                        </div>
                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Simpan Konfigurasi') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <h2 style="font-size: 1.2rem; margin-bottom: 20px; border-bottom: 2px solid var(--light-bg); padding-bottom: 10px;">
                        <i class="fas fa-user-slash"></i> Hapus Akun
                    </h2>
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
@endsection
