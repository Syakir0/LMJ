@extends('layouts.app')

@section('title', 'Detail Perangkat')
@section('breadcrumb', 'Devices / ' . $device->name)

@section('content')
<div style="display: flex; gap: 20px; flex-direction: column;">
    <!-- Back Button -->
    <div>
        <a href="{{ route('devices.index') }}" style="text-decoration: none; color: var(--accent-color); font-weight: bold;">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar
        </a>
    </div>

    <!-- Header Info -->
    <div class="card" style="display: flex; justify-content: space-between; align-items: center; background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%); color: white; border: none;">
        <div>
            <h1 style="margin: 0; font-size: 1.5rem;">{{ $device->name }}</h1>
            <div style="font-size: 0.9rem; opacity: 0.8;">{{ $device->ip_address }} | {{ strtoupper($device->type) }}</div>
        </div>
        <div style="text-align: right;">
            <div style="background: {{ $device->is_online ? '#2ecc71' : '#e74c3c' }}; padding: 5px 15px; border-radius: 20px; font-weight: bold; font-size: 0.8rem;">
                {{ $device->is_online ? 'ONLINE' : 'OFFLINE' }}
            </div>
            <div style="font-size: 0.7rem; margin-top: 5px; opacity: 0.7;">Terakhir terlihat: {{ $device->last_seen ?? 'Belum pernah' }}</div>
        </div>
    </div>

    <div class="grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <!-- Real-Time Monitoring -->
        <div class="card">
            <h2 style="margin-top:0; font-size: 1.1rem; border-bottom: 1px solid #eee; padding-bottom: 10px;">
                <i class="fas fa-pulse"></i> Real-Time Monitoring
            </h2>
            <div style="margin-top: 15px;">
                @if($device->type === 'mikrotik')
                    <div style="display: flex; flex-direction: column; gap: 15px;">
                        <div style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                            <div style="color: #7f8c8d; font-size: 0.8rem; margin-bottom: 5px;">CPU LOAD</div>
                            <div style="font-size: 1.5rem; font-weight: bold; color: #2c3e50;">{{ $stats['cpu-load'] ?? 0 }}%</div>
                            <div style="width: 100%; background: #eee; height: 8px; border-radius: 10px; margin-top: 10px;">
                                <div style="width: {{ $stats['cpu-load'] ?? 0 }}%; background: var(--accent-color); height: 100%; border-radius: 10px;"></div>
                            </div>
                        </div>
                        <div style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                            <div style="color: #7f8c8d; font-size: 0.8rem; margin-bottom: 5px;">FREE MEMORY</div>
                            <div style="font-size: 1.5rem; font-weight: bold; color: #2c3e50;">
                                {{ isset($stats['free-memory']) ? round($stats['free-memory'] / 1024 / 1024, 1) . ' MB' : '0 MB' }}
                            </div>
                        </div>
                        <div style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                            <div style="color: #7f8c8d; font-size: 0.8rem; margin-bottom: 5px;">UPTIME</div>
                            <div style="font-size: 1.2rem; font-weight: bold; color: #2c3e50;">{{ $stats['uptime'] ?? 'Unknown' }}</div>
                        </div>
                    </div>
                @else
                    <div style="text-align: center; padding: 30px; color: #7f8c8d;">
                        <i class="fas fa-satellite-dish" style="font-size: 3rem; margin-bottom: 15px; opacity: 0.3;"></i>
                        <p>Monitoring via ICMP Ping Aktif</p>
                        <div style="font-size: 0.9rem;">Status: <strong>{{ $stats['status'] }}</strong></div>
                        <div style="font-size: 0.8rem;">Update: {{ $stats['last_ping'] }}</div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Alerts/Logs -->
        <div class="card">
            <h2 style="margin-top:0; font-size: 1.1rem; border-bottom: 1px solid #eee; padding-bottom: 10px;">
                <i class="fas fa-history"></i> Riwayat Insiden
            </h2>
            <div style="margin-top: 15px;">
                @forelse($device->alerts()->latest()->take(10)->get() as $alert)
                    <div style="padding: 10px; border-bottom: 1px solid #f9f9f9; font-size: 0.85rem;">
                        <span style="color: {{ $alert->level == 'critical' ? '#e74c3c' : '#f1c40f' }}; font-weight: bold;">
                            [{{ strtoupper($alert->level) }}]
                        </span>
                        {{ $alert->title }}
                        <div style="color: #bdc3c7; font-size: 0.75rem;">{{ $alert->created_at->diffForHumans() }}</div>
                    </div>
                @empty
                    <div style="padding: 20px; text-align: center; color: #bdc3c7; font-size: 0.9rem;">
                        Belum ada riwayat insiden untuk perangkat ini.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
