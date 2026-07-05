@extends('layouts.app')

@section('title', 'Tambah Pengguna Baru')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Tambah Pengguna Baru</h1>
        <a href="{{ route('owner.users.index') }}" class="text-gray-500 hover:text-gray-700">
            &larr; Kembali
        </a>
    </div>



    <div class="bg-white p-8 rounded-lg shadow-sm border border-gray-100">
        <form action="{{ route('owner.users.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div>
                <label for="nama" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                <input type="text" name="nama" id="nama" value="{{ old('nama') }}" required class="w-full rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2 border @error('nama') border-red-500 bg-red-50 @else border-gray-300 @enderror">
                @error('nama')
                    <p class="text-red-500 text-xs mt-1 italic">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username (Untuk Login)</label>
                <input type="text" name="username" id="username" value="{{ old('username') }}" required class="w-full rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2 border @error('username') border-red-500 bg-red-50 @else border-gray-300 @enderror">
                @error('username')
                    <p class="text-red-500 text-xs mt-1 italic">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Hak Akses (Role)</label>
                <select name="role" id="role" required class="w-full rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2 border @error('role') border-red-500 bg-red-50 @else border-gray-300 @enderror">
                    <option value="" disabled selected>-- Pilih Role --</option>
                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin (Kasir/Gudang)</option>
                    <option value="owner" {{ old('role') == 'owner' ? 'selected' : '' }}>Owner (Pemilik)</option>
                </select>
                @error('role')
                    <p class="text-red-500 text-xs mt-1 italic">{{ $message }}</p>
                @enderror
            </div>

            <hr class="border-gray-200 my-4">

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                <div class="relative">
                    <input type="password" name="password" id="password" oninput="toggleIconVisibility('btn-eye-1', this.value)" required class="w-full rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2 border @error('password') border-red-500 bg-red-50 @else border-gray-300 @enderror" style="padding-right: 2.5rem;">
                    <button type="button" id="btn-eye-1" onclick="togglePassword('password', 'eye-icon-1')" class="hidden" style="position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                        <svg id="eye-icon-1" class="h-5 w-5 text-gray-500 hover:text-gray-700 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                </div>
                @error('password')
                    <p class="text-red-500 text-xs mt-1 italic">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
                <div class="relative">
                    <input type="password" name="password_confirmation" id="password_confirmation" oninput="toggleIconVisibility('btn-eye-2', this.value)" required class="w-full rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2 border @error('password_confirmation') border-red-500 bg-red-50 @else border-gray-300 @enderror" style="padding-right: 2.5rem;">
                    <button type="button" id="btn-eye-2" onclick="togglePassword('password_confirmation', 'eye-icon-2')" class="hidden" style="position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                        <svg id="eye-icon-2" class="h-5 w-5 text-gray-500 hover:text-gray-700 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="pt-4 flex justify-end">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded shadow-sm transition w-full md:w-auto">
                    Simpan Pengguna
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleIconVisibility(btnId, value) {
        const btn = document.getElementById(btnId);
        if (value.length > 0) {
            btn.style.display = 'flex';
            btn.classList.remove('hidden');
        } else {
            btn.style.display = 'none';
        }
    }

    function togglePassword(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        
        if (input.type === "password") {
            input.type = "text";
            // Ubah icon ke mata dicoret (slash)
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />';
        } else {
            input.type = "password";
            // Ubah kembali ke icon mata normal
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />';
        }
    }
</script>
@endsection
