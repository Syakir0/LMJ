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
            <tbody id="device-table">
                @forelse($devices as $device)
                <tr id="device-row-{{ $device->id }}" style="border-bottom: 1px solid #eee;">
                    <td style="padding: 12px; font-weight: 500;">
                        <a href="{{ route('devices.show', $device) }}" style="text-decoration: none; color: inherit;">
                            {{ $device->name }} <i class="fas fa-external-link-alt" style="font-size: 0.7rem; color: #bdc3c7; margin-left: 5px;"></i>
                        </a>
                    </td>
                    <td style="padding: 12px;"><code>{{ $device->ip_address }}</code></td>
                    <td style="padding: 12px;">{{ strtoupper($device->type) }}</td>
                    <td style="padding: 12px; text-align: center;" class="status-cell">
                        <span class="status-badge" style="background: {{ $device->is_online ? '#e8f5e9' : '#ffebee' }}; 
                                     color: {{ $device->is_online ? '#2e7d32' : '#c62828' }}; 
                                     padding: 4px 10px; border-radius: 12px; font-size: 0.8rem; font-weight: 600;">
                            <i class="fas fa-circle" style="font-size: 0.6rem; margin-right: 5px;"></i>
                            <span class="status-text">{{ $device->is_online ? 'ONLINE' : 'OFFLINE' }}</span>
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

@section('scripts')
<script>
    function refreshDeviceStatus() {
        fetch('{{ route('devices.list') }}')
            .then(response => response.json())
            .then(devices => {
                devices.forEach(device => {
                    const row = document.getElementById('device-row-' + device.id);
                    if (row) {
                        const badge = row.querySelector('.status-badge');
                        const text = row.querySelector('.status-text');
                        
                        const isOnline = device.is_online == 1;
                        badge.style.background = isOnline ? '#e8f5e9' : '#ffebee';
                        badge.style.color = isOnline ? '#2e7d32' : '#c62828';
                        text.innerText = isOnline ? 'ONLINE' : 'OFFLINE';
                    }
                });
            })
            .catch(err => console.error('Device refresh error:', err));
    }

    // Refresh every 10 seconds
    setInterval(refreshDeviceStatus, 10000);
</script>
@endsection
@endsection
