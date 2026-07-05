@extends('layouts.app')
@section('title', 'Data Karyawan')
@section('content')
<div class="card">
    <div class="card-header">
        <h3>Daftar Karyawan</h3>
        <a href="{{ route('admin.karyawan.create') }}" class="btn btn-primary">+ Tambah Karyawan</a>
    </div>
    <div class="card-body">
        <form method="GET" class="filter-bar">
            <div class="search-input-wrapper">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / jabatan..." class="form-input">
            </div>
            <select name="status" class="form-select" style="max-width: 160px;">
                <option value="">Semua Status</option>
                <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="nonaktif" {{ request('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
            </select>
            <button type="submit" class="btn btn-outline">Cari</button>
        </form>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                        <th>Nama</th>
                        <th>Jabatan</th>
                        <th>Tanggal Masuk</th>
                        <th>Gaji Pokok</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($karyawans as $k)
                    <tr>
                        <td>{{ $k->nama }}</td>
                        <td>{{ $k->jabatan }}</td>
                        <td>{{ $k->tanggal_masuk ? $k->tanggal_masuk->format('d M Y') : '-' }}</td>
                        <td>Rp {{ number_format($k->gaji_pokok, 0, ',', '.') }}</td>
                        <td>
                            @if($k->is_active)
                                <span class="badge badge-selesai">Aktif</span>
                            @else
                                <span class="badge badge-dibatalkan">Nonaktif</span>
                            @endif
                        </td>
                        <td class="text-center table-actions" style="justify-content: center;">
                            <a href="{{ route('admin.karyawan.show', $k->id) }}" class="text-primary">Detail</a> |
                            <a href="{{ route('admin.karyawan.edit', $k->id) }}" class="text-warning">Edit</a> |
                            <a href="#" class="text-danger" data-delete="true" data-delete-form="delete-form-{{ $k->id }}">Hapus</a>
                            <form id="delete-form-{{ $k->id }}" action="{{ route('admin.karyawan.destroy', $k->id) }}" method="POST" style="display: none;">
                                @csrf @method('DELETE')
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center">Tidak ada data karyawan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top: 1.5rem;">{{ $karyawans->links() }}</div>
    </div>
</div>
@endsection