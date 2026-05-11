@extends('layouts.app')

@section('title', 'Daftar Tagihan')
@section('breadcrumb', 'Billing / Invoices')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="margin: 0; font-size: 1.2rem;"><i class="fas fa-file-invoice-dollar"></i> Manajemen Tagihan Pelanggan</h2>
    </div>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
            <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
        </div>
    @endif

    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
            <thead>
                <tr style="background: #f8f9fa; text-align: left; border-bottom: 2px solid #eee;">
                    <th style="padding: 12px;">No. Invoice</th>
                    <th style="padding: 12px;">Pelanggan</th>
                    <th style="padding: 12px;">Bulan/Tahun</th>
                    <th style="padding: 12px;">Jumlah</th>
                    <th style="padding: 12px;">Jatuh Tempo</th>
                    <th style="padding: 12px;">Status</th>
                    <th style="padding: 12px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $invoice)
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 12px; font-weight: 600;">{{ $invoice->invoice_number }}</td>
                    <td style="padding: 12px;">{{ $invoice->customer->name ?? 'N/A' }}</td>
                    <td style="padding: 12px;">{{ date('F', mktime(0, 0, 0, $invoice->month, 10)) }} {{ $invoice->year }}</td>
                    <td style="padding: 12px;">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</td>
                    <td style="padding: 12px;">{{ $invoice->due_date->format('d M Y') }}</td>
                    <td style="padding: 12px;">
                        @php
                            $statusColors = [
                                'unpaid' => ['bg' => '#fff3e0', 'text' => '#e65100'],
                                'paid' => ['bg' => '#e8f5e9', 'text' => '#2e7d32'],
                                'overdue' => ['bg' => '#ffebee', 'text' => '#c62828'],
                                'cancelled' => ['bg' => '#f5f5f5', 'text' => '#757575'],
                            ];
                            $color = $statusColors[$invoice->status] ?? $statusColors['unpaid'];
                        @endphp
                        <span style="background: {{ $color['bg'] }}; color: {{ $color['text'] }}; padding: 4px 10px; border-radius: 12px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">
                            {{ $invoice->status }}
                        </span>
                    </td>
                    <td style="padding: 12px; text-align: center;">
                        @if($invoice->status !== 'paid')
                        <form action="{{ route('invoices.pay', $invoice) }}" method="POST" style="display: inline;" onsubmit="return confirm('Konfirmasi pembayaran tunai untuk invoice ini?')">
                            @csrf
                            <button type="submit" class="btn" style="background: #27ae60; color: white; padding: 5px 10px; font-size: 0.8rem; border-radius: 4px; border: none; cursor: pointer;">
                                <i class="fas fa-check"></i> Bayar
                            </button>
                        </form>
                        @else
                        <span style="color: #27ae60; font-size: 0.8rem;"><i class="fas fa-calendar-check"></i> {{ $invoice->paid_at->format('d/m/y H:i') }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="padding: 30px; text-align: center; color: #999;">Belum ada data tagihan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div style="margin-top: 20px;">
        {{ $invoices->links() }}
    </div>
</div>
@endsection
