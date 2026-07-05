@extends('layouts.app')
@section('title', 'Data Pengeluaran')
@section('content')
<div class="card">
    <div class="card-header">
        <h3>Daftar Pengeluaran</h3>
        <a href="{{ route('admin.pengeluaran.create') }}" class="btn btn-primary">+ Tambah Pengeluaran</a>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        {{-- Filter --}}
        <form method="GET" class="filter-bar">
            <div class="form-group" style="margin-bottom: 0;">
                <select name="kategori" class="form-select">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoris as $kat)
                        <option value="{{ $kat }}" {{ request('kategori') == $kat ? 'selected' : '' }}>
                            {{ ucwords(str_replace('_', ' ', $kat)) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <input type="date" name="tanggal_mulai" class="form-input" value="{{ request('tanggal_mulai') }}" placeholder="Dari tanggal">
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <input type="date" name="tanggal_akhir" class="form-input" value="{{ request('tanggal_akhir') }}" placeholder="Sampai tanggal">
            </div>
            <button type="submit" class="btn btn-outline">Filter</button>
            @if(request()->hasAny(['kategori', 'tanggal_mulai', 'tanggal_akhir']))
                <a href="{{ route('admin.pengeluaran.index') }}" class="btn btn-outline">Reset</a>
            @endif
        </form>

        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Kategori</th>
                        <th>Deskripsi</th>
                        <th>Nominal</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pengeluarans as $p)
                    <tr>
                        <td>{{ $p->tanggal ? $p->tanggal->format('d M Y') : '-' }}</td>
                        <td><span class="badge">{{ ucwords(str_replace('_', ' ', $p->kategori)) }}</span></td>
                        <td>{{ Str::limit($p->deskripsi ?? '-', 50) }}</td>
                        <td class="font-medium">Rp {{ number_format($p->nominal, 0, ',', '.') }}</td>
                        <td class="text-center table-actions" style="justify-content: center;">
                            <a href="{{ route('admin.pengeluaran.edit', $p->id) }}" class="text-warning">Edit</a> |
                            <a href="#" class="text-danger" onclick="event.preventDefault(); if(confirm('Yakin ingin menghapus data ini?')) document.getElementById('delete-form-{{ $p->id }}').submit();">Hapus</a>
                            <form id="delete-form-{{ $p->id }}" action="{{ route('admin.pengeluaran.destroy', $p->id) }}" method="POST" style="display: none;">
                                @csrf @method('DELETE')
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center">Tidak ada data pengeluaran.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $pengeluarans->links() }}</div>
    </div>
</div>
@endsection
