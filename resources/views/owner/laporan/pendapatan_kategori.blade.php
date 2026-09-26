@extends('layouts.app')
@section('title', 'Laporan Pendapatan Jasa per Kategori')
@section('content')
<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <h3>Laporan Pendapatan Jasa (Per Kategori)</h3>
        <form action="{{ route('owner.laporan.pendapatan_kategori') }}" method="GET" style="display: flex; gap: 0.5rem; align-items: center;">
            <select name="bulan" class="form-select" onchange="this.form.submit()">
                @for($i=1; $i<=12; $i++)
                    <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}" {{ $bulan == str_pad($i, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                        {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                    </option>
                @endfor
            </select>
            <select name="tahun" class="form-select" onchange="this.form.submit()">
                @for($i=date('Y'); $i>=2020; $i--)
                    <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>{{ $i }}</option>
                @endfor
            </select>
        </form>
    </div>
    <div class="card-body">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Kategori Jasa</th>
                        <th>Total Kotor (Sebelum Diskon)</th>
                        <th>Total Bersih (Setelah Diskon)</th>
                    </tr>
                </thead>
                <tbody>
                    @php 
                        $grandKotor = 0; 
                        $grandBersih = 0; 
                    @endphp
                    @forelse($pendapatanKategori as $pk)
                        @php 
                            $grandKotor += $pk->total_kotor; 
                            $grandBersih += $pk->total_bersih;
                        @endphp
                        <tr>
                            <td class="font-medium">{{ $pk->nama_kategori ?? 'Tanpa Kategori' }}</td>
                            <td>Rp {{ number_format($pk->total_kotor, 0, ',', '.') }}</td>
                            <td class="text-success font-medium">Rp {{ number_format($pk->total_bersih, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted">Tidak ada data pendapatan untuk bulan ini.</td>
                        </tr>
                    @endforelse
                </tbody>
                @if(count($pendapatanKategori) > 0)
                <tfoot>
                    <tr>
                        <td class="font-bold text-right">TOTAL</td>
                        <td class="font-bold">Rp {{ number_format($grandKotor, 0, ',', '.') }}</td>
                        <td class="font-bold text-success">Rp {{ number_format($grandBersih, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection
