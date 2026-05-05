@extends('layouts.app')

@section('title', 'Dashboard')
@section('breadcrumb', 'Dashboard')

@section('styles')
<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    .stat-card {
        background: white;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        display: flex;
        align-items: center;
        transition: transform 0.3s;
    }
    .stat-card:hover {
        transform: translateY(-5px);
    }
    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-right: 20px;
    }
    .icon-customers { background: #e3f2fd; color: #1976d2; }
    .icon-online { background: #e8f5e9; color: #2e7d32; }
    .icon-sessions { background: #fff3e0; color: #ef6c00; }
    
    .stat-info h3 {
        margin: 0;
        font-size: 0.9rem;
        color: #7f8c8d;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .stat-info .value {
        font-size: 1.8rem;
        font-weight: bold;
        color: #2c3e50;
    }
</style>
@endsection

@section('content')
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon icon-customers">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-info">
            <h3>Total Pelanggan</h3>
            <div class="value">{{ $totalCustomers }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon icon-online">
            <i class="fas fa-microchip"></i>
        </div>
        <div class="stat-info">
            <h3>CPU Load</h3>
            <div id="stat-cpu" class="value">{{ $mikrotikStats['cpu-load'] ?? 0 }}%</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon icon-sessions">
            <i class="fas fa-memory"></i>
        </div>
        <div class="stat-info">
            <h3>RAM Free</h3>
            <div id="stat-ram" class="value">
                @if(isset($mikrotikStats['free-memory']))
                    {{ round($mikrotikStats['free-memory'] / 1024 / 1024, 1) }} MB
                @else
                    0 MB
                @endif
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: #fce4ec; color: #c2185b;">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-info">
            <h3>Uptime</h3>
            <div id="stat-uptime" class="value" style="font-size: 1.1rem;">{{ $mikrotikStats['uptime'] ?? '00:00:00' }}</div>
        </div>
    </div>
</div>

<!-- Interface Status -->
<div class="card" style="margin-bottom: 30px;">
    <h2 style="margin-top:0; font-size: 1.2rem; border-bottom: 1px solid #eee; padding-bottom: 10px;">
        <i class="fas fa-ethernet"></i> Status Port (Interfaces)
    </h2>
    <div id="interface-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px; margin-top: 15px;">
        @foreach($interfaces as $iface)
        <div class="interface-card" data-name="{{ $iface['name'] }}" style="padding: 12px; border-radius: 8px; border: 1px solid #eee; display: flex; align-items: center; gap: 12px; background: {{ $iface['running'] == 'true' ? '#f0fff4' : '#fff5f5' }}">
            <div class="indicator" style="width: 10px; height: 10px; border-radius: 50%; background: {{ $iface['running'] == 'true' ? '#2ecc71' : '#e74c3c' }};"></div>
            <div>
                <div style="font-weight: bold; font-size: 0.95rem;">{{ $iface['name'] }}</div>
                <div class="status-text" style="font-size: 0.8rem; color: #7f8c8d;">
                    {{ $iface['running'] == 'true' ? 'Connected' : 'Disconnected' }}
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<div style="margin-bottom: 30px;">
    <h2 style="font-size: 1.2rem; margin-bottom: 15px;"><i class="fas fa-bolt"></i> Akses Cepat</h2>
    <div style="display: flex; gap: 15px;">
        <a href="{{ route('customers.create') }}" class="btn btn-primary" style="text-decoration: none; display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-user-plus"></i> Pelanggan Baru
        </a>
        <a href="{{ route('packages.create') }}" class="btn" style="background: var(--secondary-color); color: white; text-decoration: none; display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-box-open"></i> Tambah Paket
        </a>
        <a href="{{ route('alerts.index') }}" class="btn" style="background: white; border: 1px solid #ddd; color: #555; text-decoration: none; display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-history"></i> Log Sistem
        </a>
    </div>
</div>

<div class="grid" style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 15px;">
            <h2 style="margin:0; font-size: 1.2rem;">
                <i class="fas fa-chart-line"></i> Monitoring Real-Time (PPPoE Active)
            </h2>
            <div style="display: flex; align-items: center; gap: 10px;">
                <small id="last-update" style="color: #bdc3c7; font-size: 0.7rem;"></small>
                <button onclick="fetchPppoe()" class="btn" style="background: var(--light-bg); border: 1px solid #ddd; padding: 4px 10px; font-size: 0.75rem;">
                    <i id="refresh-icon" class="fas fa-sync-alt"></i> Refresh
                </button>
            </div>
        </div>
        <div style="padding: 20px; overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                <thead style="background: #f8f9fa;">
                    <tr>
                        <th style="padding: 8px; text-align: left;">User</th>
                        <th style="padding: 8px; text-align: left;">IP Address</th>
                        <th style="padding: 8px; text-align: left;">Uptime</th>
                    </tr>
                </thead>
                <tbody id="pppoe-table">
                    <tr>
                        <td colspan="3" style="padding: 20px; text-align: center; color: #bdc3c7;">
                            <i class="fas fa-spinner fa-spin"></i> Menghubungkan ke MikroTik...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <h2 style="margin-top:0; font-size: 1.2rem; border-bottom: 1px solid #eee; padding-bottom: 10px;">
            <i class="fas fa-bell"></i> Alerts Terbaru
        </h2>
        <ul id="alert-list" style="list-style: none; padding: 0;">
            @forelse($latestAlerts as $alert)
            <li style="padding: 12px 0; border-bottom: 1px solid #f9f9f9; font-size: 0.85rem;">
                <span style="color: {{ $alert->level == 'critical' ? '#e74c3c' : ($alert->level == 'warning' ? '#f1c40f' : '#3498db') }}; font-weight: bold;">
                    [{{ strtoupper($alert->level) }}]
                </span> 
                {{ $alert->title }} <br>
                <small style="color: #bdc3c7;">{{ $alert->created_at->diffForHumans() }}</small>
            </li>
            @empty
            <li style="padding: 20px; text-align: center; color: #bdc3c7;">Tidak ada alert aktif.</li>
            @endforelse
        </ul>
        <a href="{{ route('alerts.index') }}" style="display: block; text-align: center; font-size: 0.8rem; color: var(--accent-color); text-decoration: none; margin-top: 10px;">
            Lihat Semua Log
        </a>
    </div>

    <div class="card">
        <h2 style="margin-top:0; font-size: 1.2rem; border-bottom: 1px solid #eee; padding-bottom: 10px;">
            <i class="fas fa-user-times"></i> Failed Logins (RADIUS)
        </h2>
        <ul style="list-style: none; padding: 0;">
            @forelse($failedLogins as $login)
            <li style="padding: 12px 0; border-bottom: 1px solid #f9f9f9; font-size: 0.85rem;">
                <strong style="color: #e74c3c;">{{ $login->username }}</strong> <br>
                <small style="color: #bdc3c7;">{{ $login->authdate }}</small>
            </li>
            @empty
            <li style="padding: 20px; text-align: center; color: #bdc3c7;">Tidak ada percobaan login gagal.</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function fetchDashboardData() {
        const refreshIcon = document.getElementById('refresh-icon');
        const lastUpdate = document.getElementById('last-update');
        
        if(refreshIcon) refreshIcon.classList.add('fa-spin');
        
        // 1. Fetch PPPoE Sessions
        fetch('{{ route('pppoe.active') }}')
            .then(response => response.json())
            .then(data => {
                const tbody = document.getElementById('pppoe-table');
                if (!tbody) return;

                if (!Array.isArray(data) || data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="3" style="padding: 20px; text-align: center; color: #bdc3c7;">Tidak ada sesi aktif.</td></tr>';
                    return;
                }
                
                let html = '';
                data.forEach(session => {
                    const user = session.user || session.name || 'Unknown';
                    const ip = session.address || '0.0.0.0';
                    const uptime = session.uptime || '00:00:00';
                    html += `<tr style="border-bottom: 1px solid #f9f9f9;">
                        <td style="padding: 8px;"><strong>${user}</strong></td>
                        <td style="padding: 8px;"><code>${ip}</code></td>
                        <td style="padding: 8px;">${uptime}</td>
                    </tr>`;
                });
                tbody.innerHTML = html;
            });

        // 2. Fetch MikroTik Stats & Interfaces
        fetch('{{ route('dashboard.stats') }}')
            .then(response => response.json())
            .then(data => {
                if(refreshIcon) refreshIcon.classList.remove('fa-spin');
                
                const now = new Date();
                if(lastUpdate) lastUpdate.innerText = 'Auto-update: ' + now.getHours().toString().padStart(2, '0') + ':' + 
                                      now.getMinutes().toString().padStart(2, '0') + ':' + 
                                      now.getSeconds().toString().padStart(2, '0');

                // Update Stats
                const stats = data.mikrotikStats;
                if(stats) {
                    document.getElementById('stat-cpu').innerText = (stats['cpu-load'] || 0) + '%';
                    const ramMB = stats['free-memory'] ? Math.round(stats['free-memory'] / 1024 / 1024 * 10) / 10 : 0;
                    document.getElementById('stat-ram').innerText = ramMB + ' MB';
                    document.getElementById('stat-uptime').innerText = stats['uptime'] || '00:00:00';
                }

                // Update Interfaces
                const ifaces = data.interfaces;
                if(ifaces) {
                    ifaces.forEach(iface => {
                        const card = document.querySelector(`.interface-card[data-name="${iface.name}"]`);
                        if(card) {
                            const isRunning = iface.running === 'true';
                            card.style.background = isRunning ? '#f0fff4' : '#fff5f5';
                            card.querySelector('.indicator').style.background = isRunning ? '#2ecc71' : '#e74c3c';
                            card.querySelector('.status-text').innerText = isRunning ? 'Connected' : 'Disconnected';
                        }
                    });
                }

                // Update Alerts List
                const alerts = data.latestAlerts;
                const alertList = document.getElementById('alert-list');
                if(alerts && alertList) {
                    if(alerts.length === 0) {
                        alertList.innerHTML = '<li style="padding: 20px; text-align: center; color: #bdc3c7;">Tidak ada alert aktif.</li>';
                    } else {
                        let alertHtml = '';
                        alerts.forEach(alert => {
                            const levelColor = alert.level === 'critical' ? '#e74c3c' : (alert.level === 'warning' ? '#f1c40f' : '#3498db');
                            
                            // Format relative time locally
                            const createdAt = new Date(alert.created_at);
                            const diffInSeconds = Math.floor((new Date() - createdAt) / 1000);
                            let timeStr = 'Just now';
                            if (diffInSeconds > 60) timeStr = Math.floor(diffInSeconds / 60) + 'm ago';
                            if (diffInSeconds > 3600) timeStr = Math.floor(diffInSeconds / 3600) + 'h ago';

                            alertHtml += `
                                <li style="padding: 12px 0; border-bottom: 1px solid #f9f9f9; font-size: 0.85rem;">
                                    <span style="color: ${levelColor}; font-weight: bold;">
                                        [${alert.level.toUpperCase()}]
                                    </span> 
                                    ${alert.title} <br>
                                    <small style="color: #bdc3c7;">${timeStr}</small>
                                </li>
                            `;
                        });
                        alertList.innerHTML = alertHtml;
                    }
                }
            })
            .catch(error => {
                console.error('Error fetching stats:', error);
                if(refreshIcon) refreshIcon.classList.remove('fa-spin');
            });
    }

    function checkWebNotifications() {
        fetch('{{ route('alerts.latest') }}?last_id=' + lastNotifId)
            .then(response => response.json())
            .then(alerts => {
                if (alerts.length > 0) {
                    const sound = document.getElementById('notif-sound');
                    if(sound) sound.play().catch(e => console.log('Autoplay blocked'));

                    alerts.forEach(alert => {
                        showToast(alert);
                        lastNotifId = Math.max(lastNotifId, alert.id);
                    });
                }
            });
    }

    // Initialize Automation
    document.addEventListener('DOMContentLoaded', function() {
        fetchDashboardData();
        // Update stats & sessions every 5 seconds
        setInterval(fetchDashboardData, 5000);
        // Check alerts every 5 seconds
        setInterval(checkWebNotifications, 5000);
    });
</script>
@endsection
