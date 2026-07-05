@extends('layouts.app')
@section('title', 'Data Jasa & Layanan')
@section('content')
<div class="card">
    <div class="card-header">
        <h3>Daftar Jasa</h3>
        <a href="{{ route('admin.jasa.create') }}" class="btn btn-primary">+ Tambah Jasa</a>
    </div>
    <div class="card-body">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nama Jasa</th>
                        <th>Harga</th>
                        <th>Deskripsi</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jasas ?? [] as $j)
                    <tr>
                        <td class="font-medium">{{ $j->nama_jasa }}</td>
                        <td>Rp {{ number_format($j->harga, 0, ',', '.') }}</td>
                        <td>{{ Str::limit($j->deskripsi ?? '-', 50) }}</td>
                        <td class="text-center table-actions justify-center">
                            <a href="{{ route('admin.jasa.edit', $j->id) }}" class="text-warning">Edit</a> | 
                            <form action="{{ route('admin.jasa.destroy', $j->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus jasa ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-danger bg-transparent border-none cursor-pointer font-medium hover:underline">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center">Tidak ada data jasa.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(isset($jasas)) <div class="mt-4">{{ $jasas->links() }}</div> @endif
    </div>
</div>
@endsection