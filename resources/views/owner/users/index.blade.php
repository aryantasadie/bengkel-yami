@extends('layouts.app')

@section('title', 'Kelola Pengguna (Admin & Owner)')

@section('content')
<div class="mb-4 flex flex-col md:flex-row justify-between items-center gap-4">
    <h1 class="text-2xl font-bold text-gray-800">Manajemen Pengguna</h1>
    <a href="{{ route('owner.users.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition">
        + Tambah Pengguna Baru
    </a>
</div>

@if (session('success'))
    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
        {{ session('error') }}
    </div>
@endif

<div class="bg-white p-6 rounded-lg shadow-sm overflow-x-auto">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-gray-50 border-b border-gray-200">
                <th class="p-3 text-sm font-semibold text-gray-600 uppercase">Nama Lengkap</th>
                <th class="p-3 text-sm font-semibold text-gray-600 uppercase">Username</th>
                <th class="p-3 text-sm font-semibold text-gray-600 uppercase">Role</th>
                <th class="p-3 text-sm font-semibold text-gray-600 uppercase">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($users as $user)
                <tr class="hover:bg-gray-50 transition">
                    <td class="p-3 text-gray-800 font-medium">{{ $user->nama }}</td>
                    <td class="p-3 text-gray-600">{{ $user->username }}</td>
                    <td class="p-3">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $user->role === 'owner' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td class="p-3">
                        <a href="{{ route('owner.users.edit', $user->id) }}" class="text-indigo-600 hover:text-indigo-800 font-medium text-sm border border-indigo-200 bg-indigo-50 px-3 py-1 rounded">
                            Edit
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="p-4 text-center text-gray-500">Belum ada pengguna admin/owner.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
