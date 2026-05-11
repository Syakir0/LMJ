@extends('layouts.app')

@section('title', 'Discovery Perangkat')
@section('breadcrumb', 'Devices / Discovery')

@section('content')
<div class="card">
    <h2 style="margin-top:0; font-size: 1.2rem; border-bottom: 1px solid #eee; padding-bottom: 10px;">
        <i class="fas fa-search-location"></i> Deteksi Perangkat Baru
    </h2>
    <p style="color: #7f8c8d; font-size: 0.9rem;">
        Sistem mendeteksi perangkat yang terhubung ke MikroTik namun belum terdaftar di Dashboard.
    </p>

    <div style="margin-top: 20px;">
        <h3 style="font-size: 1rem;"><i class="fas fa-network-wired"></i> MikroTik Neighbors (MNDP)</h3>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                <thead>
                    <tr style="background: #f8f9fa; text-align: left; border-bottom: 2px solid #eee;">
                        <th style="padding: 12px;">Identity / Brand</th>
                        <th style="padding: 12px;">IP Address</th>
                        <th style="padding: 12px;">MAC Address</th>
                        <th style="padding: 12px;">Interface</th>
                        <th style="padding: 12px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($newNeighbors as $n)
                    <tr style="border-bottom: 1px solid #eee;">
                        <form action="{{ route('discovery.add') }}" method="POST">
                            @csrf
                            <td style="padding: 12px;">
                                <input type="text" name="name" value="{{ $n['identity'] ?? 'Device' }}" style="padding: 5px; border: 1px solid #ddd; border-radius: 4px; font-weight: bold; width: 150px;">
                                <br><small style="color: #7f8c8d;">{{ $n['platform'] ?? ($n['board'] ?? '-') }}</small>
                            </td>
                            <td style="padding: 12px;"><code>{{ $n['address'] ?? '-' }}</code></td>
                            <td style="padding: 12px;">{{ $n['mac-address'] ?? '-' }}</td>
                            <td style="padding: 12px;">{{ $n['interface'] ?? '-' }}</td>
                            <td style="padding: 12px; text-align: center;">
                                <input type="hidden" name="ip_address" value="{{ $n['address'] ?? '' }}">
                                <input type="hidden" name="type" value="mikrotik">
                                <button type="submit" class="btn btn-primary" style="padding: 5px 15px; font-size: 0.75rem;">
                                    <i class="fas fa-plus"></i> Monitor
                                </button>
                            </td>
                        </form>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="padding: 20px; text-align: center; color: #999;">Tidak ada tetangga (neighbor) baru terdeteksi.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div style="margin-top: 40px;">
        <h3 style="font-size: 1rem;"><i class="fas fa-dhcp"></i> DHCP Leases (Wired/Wireless)</h3>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                <thead>
                    <tr style="background: #f8f9fa; text-align: left; border-bottom: 2px solid #eee;">
                        <th style="padding: 12px;">Host Name</th>
                        <th style="padding: 12px;">IP Address</th>
                        <th style="padding: 12px;">MAC Address</th>
                        <th style="padding: 12px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($newLeases as $l)
                    <tr style="border-bottom: 1px solid #eee;">
                        <form action="{{ route('discovery.add') }}" method="POST">
                            @csrf
                            <td style="padding: 12px;">
                                <input type="text" name="name" value="{{ $l['host-name'] ?? 'Generic Device' }}" style="padding: 5px; border: 1px solid #ddd; border-radius: 4px; font-weight: bold; width: 150px;">
                            </td>
                            <td style="padding: 12px;"><code>{{ $l['address'] ?? '-' }}</code></td>
                            <td style="padding: 12px;">{{ $l['mac-address'] ?? '-' }}</td>
                            <td style="padding: 12px; text-align: center;">
                                <input type="hidden" name="ip_address" value="{{ $l['address'] }}">
                                <input type="hidden" name="type" value="other">
                                <button type="submit" class="btn btn-primary" style="padding: 5px 15px; font-size: 0.75rem;">
                                    <i class="fas fa-plus"></i> Monitor
                                </button>
                            </td>
                        </form>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="padding: 20px; text-align: center; color: #999;">Tidak ada perangkat DHCP baru terdeteksi.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div style="margin-top: 40px;">
        <h3 style="font-size: 1rem;"><i class="fas fa-microchip"></i> ARP Table (Semua Alat Terkoneksi)</h3>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                <thead>
                    <tr style="background: #f8f9fa; text-align: left; border-bottom: 2px solid #eee;">
                        <th style="padding: 12px;">Identitas Perangkat</th>
                        <th style="padding: 12px;">IP Address</th>
                        <th style="padding: 12px;">MAC Address</th>
                        <th style="padding: 12px;">Interface</th>
                        <th style="padding: 12px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($newArp as $a)
                    <tr style="border-bottom: 1px solid #eee;">
                        <form action="{{ route('discovery.add') }}" method="POST">
                            @csrf
                            <td style="padding: 12px;">
                                <input type="text" name="name" value="Detected-{{ $a['address'] }}" style="padding: 5px; border: 1px solid #ddd; border-radius: 4px; font-weight: bold; width: 150px;">
                            </td>
                            <td style="padding: 12px;"><code>{{ $a['address'] ?? '-' }}</code></td>
                            <td style="padding: 12px;">{{ $a['mac-address'] ?? '-' }}</td>
                            <td style="padding: 12px;">{{ $a['interface'] ?? '-' }}</td>
                            <td style="padding: 12px; text-align: center;">
                                <input type="hidden" name="ip_address" value="{{ $a['address'] }}">
                                <input type="hidden" name="type" value="other">
                                <button type="submit" class="btn btn-primary" style="padding: 5px 15px; font-size: 0.75rem;">
                                    <i class="fas fa-plus"></i> Monitor
                                </button>
                            </td>
                        </form>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="padding: 20px; text-align: center; color: #999;">Tidak ada perangkat baru di tabel ARP.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
