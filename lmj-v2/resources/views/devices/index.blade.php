@extends('layouts.app')

@section('title', 'Perangkat Jaringan')
@section('breadcrumb', 'Devices / List')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="margin: 0; font-size: 1.2rem;"><i class="fas fa-server"></i> Infrastruktur Jaringan</h2>
        <div style="font-size: 0.85rem; color: #7f8c8d; background: #f1f1f1; padding: 10px; border-radius: 8px;">
            <i class="fas fa-info-circle"></i> Sistem memantau perangkat secara otomatis setiap 1 menit via ICMP Ping.
        </div>
    </div>

    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8f9fa; text-align: left; border-bottom: 2px solid #eee;">
                    <th style="padding: 12px;">Nama Perangkat</th>
                    <th style="padding: 12px;">IP Address</th>
                    <th style="padding: 12px;">Tipe</th>
                    <th style="padding: 12px; text-align: center;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($devices as $device)
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 12px; font-weight: 500;">{{ $device->name }}</td>
                    <td style="padding: 12px;"><code>{{ $device->ip_address }}</code></td>
                    <td style="padding: 12px;">{{ strtoupper($device->type) }}</td>
                    <td style="padding: 12px; text-align: center;">
                        <span style="background: {{ $device->is_online ? '#e8f5e9' : '#ffebee' }}; 
                                     color: {{ $device->is_online ? '#2e7d32' : '#c62828' }}; 
                                     padding: 4px 10px; border-radius: 12px; font-size: 0.8rem; font-weight: 600;">
                            <i class="fas fa-circle" style="font-size: 0.6rem; margin-right: 5px;"></i>
                            {{ $device->is_online ? 'ONLINE' : 'OFFLINE' }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="padding: 20px; text-align: center; color: #bdc3c7;">Belum ada perangkat terdaftar.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
