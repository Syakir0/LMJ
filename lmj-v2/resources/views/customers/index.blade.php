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

    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
            <thead>
                <tr style="background: #f8f9fa; text-align: left; border-bottom: 2px solid #eee;">
                    <th style="padding: 12px;">Nama</th>
                    <th style="padding: 12px;">Username</th>
                    <th style="padding: 12px;">Paket</th>
                    <th style="padding: 12px;">Tgl Tagihan</th>
                    <th style="padding: 12px;">Jatuh Tempo</th>
                    <th style="padding: 12px;">Status</th>
                    <th style="padding: 12px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($customers as $customer)
                @php
                    $isOverdue = $customer->due_date && \Carbon\Carbon::parse($customer->due_date)->isPast() && $customer->payment_status == 'unpaid';
                    $isIsolated = $customer->payment_status == 'isolated';
                    $rowBg = $isIsolated ? '#ffebee' : ($isOverdue ? '#fff9c4' : 'transparent');
                @endphp
                <tr style="border-bottom: 1px solid #eee; background-color: {{ $rowBg }};">
                    <td style="padding: 12px; font-weight: 500;">{{ $customer->name }}</td>
                    <td style="padding: 12px;"><code style="background: #f1f1f1; padding: 2px 4px; border-radius: 3px;">{{ $customer->username }}</code></td>
                    <td style="padding: 12px;">{{ $customer->package->name ?? '-' }}</td>
                    <td style="padding: 12px;">Tgl {{ $customer->billing_date }}</td>
                    <td style="padding: 12px;">
                        @if($customer->due_date)
                            <span style="color: {{ $isOverdue ? '#c62828' : '#2c3e50' }}; font-weight: {{ $isOverdue ? 'bold' : 'normal' }};">
                                {{ \Carbon\Carbon::parse($customer->due_date)->format('d M Y') }}
                            </span>
                        @else
                            -
                        @endif
                    </td>
                    <td style="padding: 12px;">
                        @php
                            $isOverdue = $customer->due_date && \Carbon\Carbon::parse($customer->due_date)->isPast() && $customer->payment_status !== 'paid';
                            $deviceOnline = $customer->device ? $customer->device->is_online : false;
                            
                            $statusColor = '#2e7d32'; // Green (Active)
                            $statusText = 'ACTIVE';
                            
                            if ($isOverdue) {
                                $statusColor = '#c62828'; // Red (Expired)
                                $statusText = 'EXPIRED';
                            } elseif (!$deviceOnline) {
                                $statusColor = '#7f8c8d'; // Gray (Offline)
                                $statusText = 'OFFLINE';
                            }
                        @endphp

                        <span style="background: {{ $customer->payment_status == 'paid' ? '#e8f5e9' : ($customer->payment_status == 'unpaid' ? '#fff3e0' : '#ffebee') }}; 
                                     color: {{ $customer->payment_status == 'paid' ? '#2e7d32' : ($customer->payment_status == 'unpaid' ? '#e65100' : '#c62828') }}; 
                                     padding: 4px 8px; border-radius: 12px; font-size: 0.8rem; font-weight: 600;">
                            {{ strtoupper($customer->payment_status) }}
                        </span>
                        <br>
                        <span style="background: {{ $statusColor }}22; 
                                     color: {{ $statusColor }}; 
                                     padding: 2px 6px; border-radius: 12px; font-size: 0.7rem; font-weight: 600; margin-top: 4px; display: inline-block; border: 1px solid {{ $statusColor }}44;">
                            INET: {{ $statusText }}
                        </span>
                    </td>
                    <td style="padding: 12px; text-align: center;">
                        <a href="{{ route('customers.edit', $customer) }}" style="color: var(--accent-color); margin-right: 10px; text-decoration: none;">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('customers.destroy', $customer) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="background: none; border: none; color: #e74c3c; cursor: pointer;" onclick="confirmDelete(event)">
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
