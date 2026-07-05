@extends('layouts.app')
@section('title', 'Laba Rugi')

@section('content')
<div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h2 style="margin: 0; color: #111827; display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-chart-pie" style="color: #4f46e5;"></i> Laporan Laba Rugi
        </h2>
        <p style="margin: 0.2rem 0 0 0; color: #6b7280;">Pantau performa keuangan dan profitabilitas bengkel Anda.</p>
    </div>

    <!-- Filter Form -->
    <form action="{{ route('owner.laba-rugi.index') }}" method="GET" style="display: flex; gap: 0.5rem; background: white; padding: 0.5rem; border-radius: 0.5rem; border: 1px solid #e5e7eb; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
        <select name="bulan" class="form-control" style="padding: 0.4rem 2rem 0.4rem 0.8rem; border: none; background: #f9fafb; font-weight: 500;">
            @foreach($daftarBulan as $key => $namaBulan)
                <option value="{{ $key }}" {{ $bulan == $key ? 'selected' : '' }}>{{ $namaBulan }}</option>
            @endforeach
        </select>
        <input type="number" name="tahun" class="form-control" value="{{ $tahun }}" min="2020" max="2099" style="padding: 0.4rem 0.8rem; width: 100px; border: none; background: #f9fafb; font-weight: 500;">
        <button type="submit" class="btn btn-primary" style="padding: 0.4rem 1rem;">
            <i class="fas fa-filter"></i> Filter
        </button>
    </form>
</div>

<!-- Highlight Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <!-- Laba Kotor -->
    <div style="background: linear-gradient(135deg, #1e293b, #0f172a); border-radius: 1rem; padding: 1.5rem; color: white; box-shadow: 0 10px 15px -3px rgba(15, 23, 42, 0.4); position: relative; overflow: hidden;">
        <i class="fas fa-coins" style="position: absolute; right: -10px; bottom: -10px; font-size: 5rem; opacity: 0.1;"></i>
        <div style="font-size: 0.9rem; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0.5rem;">Laba Kotor</div>
        <div style="font-size: 2rem; font-weight: 700;">Rp {{ number_format($labaKotor, 0, ',', '.') }}</div>
        <div style="margin-top: 0.5rem; font-size: 0.85rem; color: #cbd5e1;"><i class="fas fa-arrow-up" style="color: #4ade80;"></i> Pendapatan - HPP</div>
    </div>

    <!-- Beban Operasional -->
    <div style="background: linear-gradient(135deg, #ef4444, #b91c1c); border-radius: 1rem; padding: 1.5rem; color: white; box-shadow: 0 10px 15px -3px rgba(220, 38, 38, 0.4); position: relative; overflow: hidden;">
        <i class="fas fa-receipt" style="position: absolute; right: -10px; bottom: -10px; font-size: 5rem; opacity: 0.1;"></i>
        <div style="font-size: 0.9rem; color: #fca5a5; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0.5rem;">Beban Operasional</div>
        <div style="font-size: 2rem; font-weight: 700;">Rp {{ number_format($bebanOperasional, 0, ',', '.') }}</div>
        <div style="margin-top: 0.5rem; font-size: 0.85rem; color: #fecaca;"><i class="fas fa-building"></i> Listrik, Gaji, Sewa, dll.</div>
    </div>

    <!-- Laba Bersih -->
    <div style="background: linear-gradient(135deg, #10b981, #047857); border-radius: 1rem; padding: 1.5rem; color: white; box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.4); position: relative; overflow: hidden;">
        <i class="fas fa-wallet" style="position: absolute; right: -10px; bottom: -10px; font-size: 5rem; opacity: 0.1;"></i>
        <div style="font-size: 0.9rem; color: #a7f3d0; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0.5rem;">Laba Bersih Usaha</div>
        <div style="font-size: 2rem; font-weight: 700;">Rp {{ number_format($labaBersih, 0, ',', '.') }}</div>
        <div style="margin-top: 0.5rem; font-size: 0.85rem; color: #d1fae5;"><i class="fas fa-check-circle" style="color: #6ee7b7;"></i> Total Keuntungan Bersih</div>
    </div>
</div>

<!-- Laporan Keuangan Format Standar -->
<div class="card" style="border: none; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);">
    <div class="card-header" style="background: white; border-bottom: 2px solid #f1f5f9; padding: 1.5rem;">
        <h3 style="margin: 0; color: #1e293b; text-align: center; font-size: 1.25rem;">Laporan Laba Rugi</h3>
        <p style="margin: 0.2rem 0 0 0; color: #64748b; text-align: center; font-size: 0.9rem;">Periode: {{ $daftarBulan[(int)$bulan] }} {{ $tahun }}</p>
    </div>
    <div class="card-body" style="padding: 2rem; background: #fafafa;">
        
        <table style="width: 100%; border-collapse: collapse; font-size: 1.05rem;">
            <!-- PENDAPATAN -->
            <tr>
                <td colspan="2" style="font-weight: 700; color: #334155; padding: 0.75rem 0; font-size: 1.1rem; border-bottom: 2px solid #e2e8f0;">PENDAPATAN USAHA</td>
            </tr>
            <tr>
                <td style="padding: 0.75rem 1.5rem; color: #475569;">Pendapatan Servis & Sparepart</td>
                <td style="text-align: right; font-weight: 500; color: #1e293b; padding: 0.75rem 0;">Rp {{ number_format($pendapatan, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td style="padding: 0.75rem 1.5rem; color: #475569;">Harga Pokok Penjualan (HPP Sparepart)</td>
                <td style="text-align: right; color: #ef4444; padding: 0.75rem 0;">(Rp {{ number_format($hpp, 0, ',', '.') }})</td>
            </tr>
            <tr style="background: #f1f5f9;">
                <td style="font-weight: 700; color: #0f172a; padding: 1rem 1.5rem;">TOTAL LABA KOTOR</td>
                <td style="text-align: right; font-weight: 700; color: #0f172a; padding: 1rem 1.5rem; border-top: 1px solid #cbd5e1;">Rp {{ number_format($labaKotor, 0, ',', '.') }}</td>
            </tr>

            <!-- BEBAN OPERASIONAL -->
            <tr>
                <td colspan="2" style="font-weight: 700; color: #334155; padding: 1.5rem 0 0.75rem 0; font-size: 1.1rem; border-bottom: 2px solid #e2e8f0;">BEBAN OPERASIONAL</td>
            </tr>
            <tr>
                <td style="padding: 0.75rem 1.5rem; color: #475569;">Beban Gaji & Operasional (Listrik, Air, Restock Logistik Habis Pakai)</td>
                <td style="text-align: right; color: #ef4444; padding: 0.75rem 0;">(Rp {{ number_format($bebanOperasional, 0, ',', '.') }})</td>
            </tr>
            <tr style="background: #f1f5f9;">
                <td style="font-weight: 700; color: #0f172a; padding: 1rem 1.5rem;">LABA OPERASIONAL</td>
                <td style="text-align: right; font-weight: 700; color: #0f172a; padding: 1rem 1.5rem; border-top: 1px solid #cbd5e1;">Rp {{ number_format($labaKotor - $bebanOperasional, 0, ',', '.') }}</td>
            </tr>

            <!-- PENDAPATAN/BEBAN LAINNYA -->
            <tr>
                <td colspan="2" style="font-weight: 700; color: #334155; padding: 1.5rem 0 0.75rem 0; font-size: 1.1rem; border-bottom: 2px solid #e2e8f0;">BEBAN LAIN-LAIN</td>
            </tr>
            <tr>
                <td style="padding: 0.75rem 1.5rem; color: #475569;">Pengeluaran Kategori "Lainnya" (Kerusakan, Sumbangan, dll)</td>
                <td style="text-align: right; color: #ef4444; padding: 0.75rem 0;">(Rp {{ number_format($bebanLainnya, 0, ',', '.') }})</td>
            </tr>
            
            <!-- LABA BERSIH -->
            <tr style="background: {{ $labaBersih >= 0 ? '#ecfdf5' : '#fef2f2' }}; border-bottom: 3px solid {{ $labaBersih >= 0 ? '#10b981' : '#ef4444' }};">
                <td style="font-weight: 800; color: {{ $labaBersih >= 0 ? '#047857' : '#b91c1c' }}; padding: 1.5rem; font-size: 1.2rem; text-transform: uppercase;">LABA BERSIH USAHA</td>
                <td style="text-align: right; font-weight: 800; color: {{ $labaBersih >= 0 ? '#047857' : '#b91c1c' }}; padding: 1.5rem; font-size: 1.3rem;">
                    Rp {{ number_format($labaBersih, 0, ',', '.') }}
                </td>
            </tr>
        </table>

        <div style="margin-top: 2rem; text-align: right;">
            <button class="btn btn-outline" style="color: #64748b; border-color: #cbd5e1; font-weight: 500;" onclick="window.print()">
                <i class="fas fa-print"></i> Cetak Laporan
            </button>
        </div>
    </div>
</div>

<style>
    @media print {
        body { background: white; }
        .sidebar, .navbar, .btn, form { display: none !important; }
        .main-content { margin: 0 !important; padding: 0 !important; }
        .card { box-shadow: none !important; border: 1px solid #000 !important; }
        * { color: black !important; }
        @page { margin: 1cm; }
    }
</style>
@endsection
