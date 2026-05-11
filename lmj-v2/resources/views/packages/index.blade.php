@extends('layouts.app')

@section('title', 'Paket Layanan')
@section('breadcrumb', 'Paket / List')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="margin: 0; font-size: 1.2rem;"><i class="fas fa-box"></i> Paket Layanan Internet</h2>
        <a href="{{ route('packages.create') }}" class="btn btn-primary" style="text-decoration: none;">
            <i class="fas fa-plus"></i> Tambah Paket
        </a>
    </div>

    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8f9fa; text-align: left; border-bottom: 2px solid #eee;">
                    <th style="padding: 12px;">Nama Paket</th>
                    <th style="padding: 12px;">Speed Limit</th>
                    <th style="padding: 12px;">Harga</th>
                    <th style="padding: 12px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($packages as $package)
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 12px; font-weight: 500;">{{ $package->name }}</td>
                    <td style="padding: 12px;"><span style="color: var(--accent-color); font-weight: bold;">{{ $package->speed_limit }} Mbps</span></td>
                    <td style="padding: 12px;">Rp {{ number_format($package->price, 0, ',', '.') }}</td>
                    <td style="padding: 12px; text-align: center;">
                        <a href="{{ route('packages.edit', $package) }}" style="color: var(--accent-color); margin-right: 10px; text-decoration: none;">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('packages.destroy', $package) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="background: none; border: none; color: #e74c3c; cursor: pointer;" onclick="confirmDelete(event)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="padding: 20px; text-align: center; color: #bdc3c7;">Belum ada paket layanan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
