@extends('layouts.app')
@section('title', 'Laporan Arus Kas (Cashflow)')

@section('content')
<style>
    /* Premium Dashboard Styles */
    .cashflow-summary {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    .summary-card {
        background: linear-gradient(135deg, rgba(255,255,255,0.1), rgba(255,255,255,0));
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.18);
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.05);
        display: flex;
        flex-direction: column;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }
    .summary-card::before {
        content: "";
        position: absolute;
        top: -50%;
        right: -50%;
        width: 150px;
        height: 150px;
        background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }
    .summary-card.income {
        background: linear-gradient(135deg, #0d9488, #0f766e);
        color: white;
    }
    .summary-card.expense {
        background: linear-gradient(135deg, #e11d48, #be123c);
        color: white;
    }
    .summary-card.balance {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        color: white;
    }
    .summary-title {
        font-size: 0.9rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        opacity: 0.9;
        margin-bottom: 0.5rem;
    }
    .summary-value {
        font-size: 2rem;
        font-weight: 700;
        margin: 0;
    }

    /* Filter Bar */
    .filter-wrapper {
        background: white;
        border-radius: 0.75rem;
        padding: 1rem 1.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        margin-bottom: 2rem;
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        align-items: flex-end;
    }
    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 0.4rem;
        flex: 1;
        min-width: 200px;
    }
    .filter-group label {
        font-size: 0.85rem;
        font-weight: 600;
        color: #4b5563;
    }
    .filter-group input, .filter-group select {
        padding: 0.6rem 1rem;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        font-size: 0.9rem;
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .filter-group input:focus, .filter-group select:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    .btn-filter {
        background: #1f2937;
        color: white;
        padding: 0.6rem 1.5rem;
        border: none;
        border-radius: 0.5rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
        height: 42px; /* match input height */
    }
    .btn-filter:hover {
        background: #111827;
    }

    /* Split Tables Grid */
    .cashflow-tables {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
    }
    @media (max-width: 1024px) {
        .cashflow-tables {
            grid-template-columns: 1fr;
        }
    }
    .cf-panel {
        background: white;
        border-radius: 1rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }
    .cf-panel-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #f3f4f6;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .cf-panel-header h3 {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 700;
    }
    .cf-panel.income .cf-panel-header h3 {
        color: #0d9488;
    }
    .cf-panel.expense .cf-panel-header h3 {
        color: #e11d48;
    }
    .cf-table-container {
        max-height: 600px;
        overflow-y: auto;
    }
    .cf-table {
        width: 100%;
        border-collapse: collapse;
    }
    .cf-table th {
        background: #f9fafb;
        padding: 0.75rem 1.5rem;
        text-align: left;
        font-size: 0.8rem;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    .cf-table td {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #f3f4f6;
        font-size: 0.9rem;
        color: #374151;
    }
    .cf-table tbody tr:hover {
        background-color: #f9fafb;
    }
    .cf-table tbody tr:last-child td {
        border-bottom: none;
    }
    
    .text-income { color: #0d9488; font-weight: 600; }
    .text-expense { color: #e11d48; font-weight: 600; }
    .badge {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        background: #f3f4f6;
        color: #4b5563;
    }
    .badge-op { background: #dbeafe; color: #1e40af; }
    .badge-gaji { background: #fef3c7; color: #b45309; }
    .badge-rs { background: #e0e7ff; color: #3730a3; }
    .badge-rl { background: #fae8ff; color: #86198f; }
</style>

<!-- Summary Cards -->
<div class="cashflow-summary">
    <div class="summary-card income">
        <div class="summary-title">Total Pemasukan</div>
        <div class="summary-value">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</div>
    </div>
    <div class="summary-card expense">
        <div class="summary-title">Total Pengeluaran</div>
        <div class="summary-value">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</div>
    </div>
    <div class="summary-card balance">
        <div class="summary-title">Saldo Bersih</div>
        <div class="summary-value">Rp {{ number_format($saldo, 0, ',', '.') }}</div>
    </div>
</div>

<!-- Filter Bar -->
<form method="GET" class="filter-wrapper">
    <div class="filter-group">
        <label>Mulai Tanggal</label>
        <input type="date" name="tanggal_mulai" value="{{ request('tanggal_mulai', \Carbon\Carbon::now()->startOfMonth()->toDateString()) }}">
    </div>
    <div class="filter-group">
        <label>Sampai Tanggal</label>
        <input type="date" name="tanggal_akhir" value="{{ request('tanggal_akhir', \Carbon\Carbon::now()->endOfMonth()->toDateString()) }}">
    </div>
    <div class="filter-group">
        <label>Kategori Pengeluaran</label>
        <select name="kategori">
            <option value="">Semua Kategori</option>
            <option value="operasional" {{ request('kategori') == 'operasional' ? 'selected' : '' }}>Operasional</option>
            <option value="restock_sparepart" {{ request('kategori') == 'restock_sparepart' ? 'selected' : '' }}>Restock Sparepart</option>
            <option value="restock_logistik" {{ request('kategori') == 'restock_logistik' ? 'selected' : '' }}>Restock Logistik</option>
            <option value="gaji" {{ request('kategori') == 'gaji' ? 'selected' : '' }}>Gaji Karyawan</option>
            <option value="lainnya" {{ request('kategori') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
        </select>
    </div>
    <button type="submit" class="btn-filter">Terapkan Filter</button>
</form>

<!-- Split Tables -->
<div class="cashflow-tables">
    
    <!-- Pemasukan -->
    <div class="cf-panel income">
        <div class="cf-panel-header">
            <h3>Pemasukan</h3>
            <span class="badge" style="background:#ccfbf1; color:#0f766e;">{{ $pemasukans->count() }} Transaksi</span>
        </div>
        <div class="cf-table-container">
            <table class="cf-table">
                <thead>
                    <tr>
                        <th>Tanggal & Nota</th>
                        <th>Keterangan</th>
                        <th style="text-align: right;">Nominal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pemasukans as $item)
                    <tr>
                        <td>
                            <div style="font-weight: 600;">{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</div>
                            <div style="font-size: 0.8rem; color: #6b7280;">{{ $item->no_nota }}</div>
                        </td>
                        <td>
                            Pesanan dari {{ $item->pesanan->customer->nama ?? 'Pelanggan' }}
                            @if($item->status_bayar == 'dp')
                                <span style="font-size: 0.7rem; background: #fef08a; color: #854d0e; padding: 2px 6px; border-radius: 4px; margin-left: 0.5rem; font-weight: 600;">DP</span>
                            @elseif($item->status_bayar == 'lunas')
                                <span style="font-size: 0.7rem; background: #bbf7d0; color: #166534; padding: 2px 6px; border-radius: 4px; margin-left: 0.5rem; font-weight: 600;">Lunas</span>
                            @endif
                        </td>
                        <td style="text-align: right;" class="text-income">
                            + Rp {{ number_format($item->total_bayar - $item->sisa_bayar, 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" style="text-align: center; color: #9ca3af; padding: 2rem;">Tidak ada pemasukan pada periode ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pengeluaran -->
    <div class="cf-panel expense">
        <div class="cf-panel-header">
            <h3>Pengeluaran</h3>
            <span class="badge" style="background:#ffe4e6; color:#be123c;">{{ $pengeluarans->count() }} Transaksi</span>
        </div>
        <div class="cf-table-container">
            <table class="cf-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Keterangan</th>
                        <th style="text-align: right;">Nominal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pengeluarans as $item)
                    @php
                        $badgeClass = 'badge';
                        if($item->kategori == 'operasional') $badgeClass .= ' badge-op';
                        elseif($item->kategori == 'gaji') $badgeClass .= ' badge-gaji';
                        elseif($item->kategori == 'restock_sparepart') $badgeClass .= ' badge-rs';
                        elseif($item->kategori == 'restock_logistik') $badgeClass .= ' badge-rl';
                    @endphp
                    <tr>
                        <td>
                            <div style="font-weight: 600;">{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</div>
                            <div style="margin-top: 0.2rem;"><span class="{{ $badgeClass }}">{{ ucwords(str_replace('_', ' ', $item->kategori)) }}</span></div>
                        </td>
                        <td>
                            {{ $item->deskripsi }}
                        </td>
                        <td style="text-align: right;" class="text-expense">
                            - Rp {{ number_format($item->nominal, 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" style="text-align: center; color: #9ca3af; padding: 2rem;">Tidak ada pengeluaran pada periode ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
