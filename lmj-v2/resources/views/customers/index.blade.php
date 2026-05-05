@extends('layouts.app')

@section('title', 'Daftar Pelanggan')
@section('breadcrumb', 'Pelanggan / List')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="margin: 0; font-size: 1.2rem;"><i class="fas fa-users"></i> Manajemen Pelanggan PPPoE</h2>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('reports.customers') }}" class="btn" style="background: #27ae60; color: white; text-decoration: none;">
                <i class="fas fa-file-csv"></i> Backup Report
            </a>
            <a href="{{ route('customers.create') }}" class="btn btn-primary" style="text-decoration: none;">
                <i class="fas fa-plus"></i> Tambah Pelanggan
            </a>
        </div>
    </div>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
            <thead>
                <tr style="background: #f8f9fa; text-align: left; border-bottom: 2px solid #eee;">
                    <th style="padding: 12px;">Nama</th>
                    <th style="padding: 12px;">Username</th>
                    <th style="padding: 12px;">Paket</th>
                    <th style="padding: 12px;">Status</th>
                    <th style="padding: 12px;">Dibuat</th>
                    <th style="padding: 12px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($customers as $customer)
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 12px; font-weight: 500;">{{ $customer->name }}</td>
                    <td style="padding: 12px;"><code style="background: #f1f1f1; padding: 2px 4px; border-radius: 3px;">{{ $customer->username }}</code></td>
                    <td style="padding: 12px;">{{ $customer->package->name }}</td>
                    <td style="padding: 12px;">
                        <span style="background: {{ $customer->status == 'active' ? '#e8f5e9' : '#ffebee' }}; 
                                     color: {{ $customer->status == 'active' ? '#2e7d32' : '#c62828' }}; 
                                     padding: 4px 8px; border-radius: 12px; font-size: 0.8rem; font-weight: 600;">
                            {{ strtoupper($customer->status) }}
                        </span>
                    </td>
                    <td style="padding: 12px; color: #7f8c8d; font-size: 0.9rem;">{{ $customer->created_at->format('d M Y') }}</td>
                    <td style="padding: 12px; text-align: center;">
                        <a href="{{ route('customers.edit', $customer) }}" style="color: var(--accent-color); margin-right: 10px; text-decoration: none;">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('customers.destroy', $customer) }}" method="POST" style="display: inline;" onsubmit="return confirm('Hapus pelanggan ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="background: none; border: none; color: #e74c3c; cursor: pointer;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
