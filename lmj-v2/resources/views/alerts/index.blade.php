@extends('layouts.app')

@section('title', 'Alerts & Log')
@section('breadcrumb', 'System / Alerts')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="margin: 0; font-size: 1.2rem;"><i class="fas fa-bell"></i> Log Notifikasi & Alerts</h2>
    </div>

    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8f9fa; text-align: left; border-bottom: 2px solid #eee;">
                    <th style="padding: 12px; width: 100px;">Level</th>
                    <th style="padding: 12px;">Pesan</th>
                    <th style="padding: 12px; width: 200px;">Waktu</th>
                </tr>
            </thead>
            <tbody>
                @forelse($alerts as $alert)
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 12px;">
                        <span style="background: {{ $alert->level == 'critical' ? '#ffebee' : ($alert->level == 'warning' ? '#fff3e0' : '#e3f2fd') }}; 
                                     color: {{ $alert->level == 'critical' ? '#c62828' : ($alert->level == 'warning' ? '#ef6c00' : '#1976d2') }}; 
                                     padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">
                            {{ $alert->level }}
                        </span>
                    </td>
                    <td style="padding: 12px; font-size: 0.95rem;">
                        <strong>{{ $alert->title }}</strong><br>
                        <span style="color: #555;">{{ $alert->message }}</span>
                    </td>
                    <td style="padding: 12px; color: #7f8c8d; font-size: 0.85rem;">
                        {{ $alert->created_at->format('d M Y H:i:s') }}<br>
                        <small>{{ $alert->created_at->diffForHumans() }}</small>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" style="padding: 20px; text-align: center; color: #bdc3c7;">Belum ada log notifikasi.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px;">
        {{ $alerts->links() }}
    </div>
</div>
@endsection
