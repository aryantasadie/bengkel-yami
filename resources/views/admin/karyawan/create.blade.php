@extends('layouts.app')
@section('title', 'Tambah Karyawan')
@section('content')
<div class="card" style="max-width: 700px;">
    <div class="card-header">
        <h3>Form Tambah Karyawan</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.karyawan.store') }}" method="POST">
            @csrf
            <div class="form-row">
                <div class="form-group" style="flex: 1;">
                    <label class="form-label">Nama <span class="required">*</span></label>
                    <input type="text" name="nama" class="form-input" value="{{ old('nama') }}" required>
                    @error('nama') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" class="form-input" value="{{ old('tanggal_lahir') }}">
                    @error('tanggal_lahir') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Tanggal Masuk <span class="required">*</span></label>
                    <input type="date" name="tanggal_masuk" class="form-input" value="{{ old('tanggal_masuk') }}" required>
                    @error('tanggal_masuk') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Jabatan <span class="required">*</span></label>
                <input type="text" name="jabatan" class="form-input" value="{{ old('jabatan') }}" required>
                @error('jabatan') <span class="text-danger text-sm">{{ $message }}</span> @enderror
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Gaji Pokok <span class="required">*</span></label>
                    <input type="number" name="gaji_pokok" class="form-input" value="{{ old('gaji_pokok', 0) }}" min="0" required>
                    @error('gaji_pokok') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Tunjangan</label>
                    <input type="number" name="tunjangan" class="form-input" value="{{ old('tunjangan', 0) }}" min="0">
                    @error('tunjangan') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Alamat</label>
                <textarea name="alamat" class="form-textarea">{{ old('alamat') }}</textarea>
                @error('alamat') <span class="text-danger text-sm">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">Status</label>
                <select name="is_active" class="form-select">
                    <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>

            <hr style="margin: 2rem 0; border: none; border-top: 1px solid var(--neutral-200);">
            <h4 style="margin-bottom: 1rem;">Akun Sistem</h4>
            
            <div class="form-group" style="display: flex; gap: 0.5rem; align-items: center;">
                <input type="checkbox" name="buat_akun" id="buat_akun" value="1" {{ old('buat_akun') ? 'checked' : '' }} onchange="toggleAkunFields()">
                <label for="buat_akun" style="cursor: pointer; font-weight: 500;">Buat Akun Karyawan</label>
            </div>

            <div id="akun_fields" style="display: {{ old('buat_akun') ? 'block' : 'none' }}; margin-top: 1rem; padding: 1.5rem; background: var(--neutral-50); border-radius: var(--radius-md); border: 1px solid var(--neutral-200);">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-input" value="{{ old('username') }}">
                        @error('username') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <div style="position: relative;">
                            <input type="password" id="password" name="password" class="form-input" oninput="toggleIconVisibility('btn-eye-1', this.value)" style="width: 100%; padding-right: 2.5rem; box-sizing: border-box;">
                            <button type="button" id="btn-eye-1" onclick="togglePassword('password', 'eye-icon-1')" style="display: none; position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; align-items: center; justify-content: center; padding: 0;">
                                <svg id="eye-icon-1" style="width: 1.25rem; height: 1.25rem; color: #6b7280;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                        @error('password') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <a href="{{ route('admin.karyawan.index') }}" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Karyawan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function toggleAkunFields() {
        const checkbox = document.getElementById('buat_akun');
        const fields = document.getElementById('akun_fields');
        if (checkbox.checked) {
            fields.style.display = 'block';
        } else {
            fields.style.display = 'none';
        }
    }

    function toggleIconVisibility(btnId, value) {
        const btn = document.getElementById(btnId);
        if (value.length > 0) {
            btn.style.display = 'flex';
        } else {
            btn.style.display = 'none';
        }
    }

    function togglePassword(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        
        if (input.type === "password") {
            input.type = "text";
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />';
        } else {
            input.type = "password";
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />';
        }
    }
</script>
@endsection