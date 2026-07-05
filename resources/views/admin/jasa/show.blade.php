@extends('layouts.app')
@section('title', 'Detail Jasa')
@section('content')
<div class="card" style="max-width: 800px;">
    <div class="card-header">
        <h3>Detail Jasa: {{ $jasa->nama_jasa }}</h3>
        <a href="{{ route('admin.jasa.index') }}" class="btn btn-outline">Kembali</a>
    </div>
    <div class="card-body">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
            <div>
                <div class="text-sm text-muted">Nama Jasa</div>
                <div class="font-medium text-lg">{{ $jasa->nama_jasa }}</div>
            </div>
            <div>
                <div class="text-sm text-muted">Harga</div>
                <div class="font-medium text-lg">Rp {{ number_format($jasa->harga, 0, ',', '.') }}</div>
            </div>
            <div>
                <div class="text-sm text-muted">Status</div>
                <div>
                    @if($jasa->is_active)
                        <span class="badge badge-selesai">Aktif</span>
                    @else
                        <span class="badge badge-dibatalkan">Nonaktif</span>
                    @endif
                </div>
            </div>
            <div>
                <div class="text-sm text-muted">Deskripsi</div>
                <div>{{ $jasa->deskripsi ?? '-' }}</div>
            </div>
        </div>

        @if($jasa->spareparts && $jasa->spareparts->count() > 0)
        <h4 style="margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid var(--neutral-200);">Bill of Materials (Sparepart)</h4>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nama Sparepart</th>
                        <th>Qty Default</th>
                        <th>Satuan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($jasa->spareparts as $sp)
                    <tr>
                        <td class="font-medium">{{ $sp->nama }}</td>
                        <td>{{ $sp->pivot->qty_default }}</td>
                        <td>{{ $sp->satuan }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <div style="display: flex; gap: 1rem; margin-top: 2rem;">
            <a href="{{ route('admin.jasa.edit', $jasa->id) }}" class="btn btn-primary">Edit Jasa</a>
        </div>
    </div>
</div>
@endsection
