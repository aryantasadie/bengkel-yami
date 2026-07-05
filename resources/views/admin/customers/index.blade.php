@extends('layouts.app')
@section('title', 'Data Customer')
@section('content')
<div class="card">
    <div class="card-header">
        <h3>Daftar Customer</h3>
        <a href="{{ route('admin.customers.create') }}" class="btn btn-primary">+ Tambah Customer</a>
    </div>
    <div class="card-body">
        <form method="GET" class="mb-4" style="margin-bottom: 1.5rem; display: flex; gap: 1rem;">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / no telp..." class="form-input" style="max-width: 300px;">
            <button type="submit" class="btn btn-outline">Cari</button>
        </form>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>No Telp</th>
                        <th>Alamat</th>
                        <th>Diskon Default</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $c)
                    <tr>
                        <td class="font-medium">{{ $c->nama }}</td>
                        <td>{{ $c->no_telp ?? '-' }}</td>
                        <td>{{ Str::limit($c->alamat ?? '-', 30) }}</td>
                        <td>{{ $c->diskon_default }}%</td>
                        <td class="text-center table-actions" style="justify-content: center;">
                            <a href="{{ route('admin.customers.show', $c->id) }}" class="text-primary">Detail</a> | 
                            <a href="{{ route('admin.customers.edit', $c->id) }}" class="text-warning">Edit</a> | 
                            <a href="#" class="text-danger" data-delete="true" data-delete-form="delete-form-{{ $c->id }}">Hapus</a>
                            <form id="delete-form-{{ $c->id }}" action="{{ route('admin.customers.destroy', $c->id) }}" method="POST" style="display: none;">
                                @csrf @method('DELETE')
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center">Tidak ada data customer.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top: 1.5rem;">{{ $customers->links() }}</div>
    </div>
</div>
@endsection