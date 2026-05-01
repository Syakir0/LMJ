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
            <h3>Perangkat Online</h3>
            <div class="value">{{ $onlineDevices }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon icon-sessions">
            <i class="fas fa-network-wired"></i>
        </div>
        <div class="stat-info">
            <h3>Sesi PPPoE Aktif</h3>
            <div class="value">{{ $activeSessions }}</div>
        </div>
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
            <button onclick="fetchPppoe()" class="btn" style="background: var(--light-bg); border: 1px solid #ddd; padding: 4px 10px; font-size: 0.75rem;">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
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
        <ul style="list-style: none; padding: 0;">
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
</div>
@endsection

@section('scripts')
<script>
    function fetchPppoe() {
        fetch('{{ route('pppoe.active') }}')
            .then(response => response.json())
            .then(data => {
                const tbody = document.getElementById('pppoe-table');
                if (!data || data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="3" style="padding: 20px; text-align: center; color: #bdc3c7;">Tidak ada sesi aktif.</td></tr>';
                    return;
                }
                
                let html = '';
                data.forEach(session => {
                    html += `<tr style="border-bottom: 1px solid #f9f9f9;">
                        <td style="padding: 8px;"><strong>${session.name}</strong></td>
                        <td style="padding: 8px;"><code>${session.address}</code></td>
                        <td style="padding: 8px;">${session.uptime}</td>
                    </tr>`;
                });
                tbody.innerHTML = html;
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('pppoe-table').innerHTML = '<tr><td colspan="3" style="padding: 20px; text-align: center; color: #e74c3c;">Gagal memuat data dari MikroTik.</td></tr>';
            });
    }

    // Fetch every 10 seconds
    fetchPppoe();
    setInterval(fetchPppoe, 10000);
</script>
@endsection
