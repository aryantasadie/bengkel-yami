@extends('layouts.karyawan')
@section('title', 'Profil Saya')
@section('content')

@if(session('success'))
    <div class="alert alert-success" style="margin-bottom: 1rem; padding: 1rem; border-radius: 0.5rem; background-color: #d1fae5; color: #065f46; border: 1px solid #a7f3d0;">
        {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger" style="margin-bottom: 1rem; padding: 1rem; border-radius: 0.5rem; background-color: #fee2e2; color: #991b1b; border: 1px solid #fecaca;">
        <ul style="margin: 0; padding-left: 1.5rem;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- Biodata Card -->
<div class="card" style="margin-bottom: 1.5rem; border: none; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); border-radius: 1rem; overflow: hidden;">
    <div style="background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%); height: 80px;"></div>
    <div class="card-body" style="padding: 1.5rem; text-align: center; margin-top: -40px;">
        <div style="width: 80px; height: 80px; background: white; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 2.5rem; color: #4f46e5; border: 4px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 1rem;">
            <i class="fas fa-user-tie"></i>
        </div>
        <h3 style="margin: 0; font-size: 1.25rem; color: #111827;">{{ $karyawan->nama }}</h3>
        <div style="color: #4f46e5; font-weight: 600; font-size: 0.9rem; margin-bottom: 1.5rem;">{{ $karyawan->jabatan }}</div>
        
        <div style="display: grid; grid-template-columns: 1fr; gap: 1rem; text-align: left;">
            <div style="background: #f9fafb; padding: 1rem; border-radius: 0.5rem; border: 1px solid #e5e7eb;">
                <div style="font-size: 0.75rem; color: #6b7280; font-weight: 600; text-transform: uppercase; margin-bottom: 0.2rem;">Tanggal Bergabung</div>
                <div style="font-size: 0.95rem; color: #111827; font-weight: 500;">
                    <i class="fas fa-calendar-check" style="color: #10b981; margin-right: 0.5rem;"></i>
                    {{ $karyawan->tanggal_masuk ? \Carbon\Carbon::parse($karyawan->tanggal_masuk)->format('d M Y') : '-' }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Ganti Password Card -->
<div class="card" style="margin-bottom: 1.5rem; border: none; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); border-radius: 1rem;">
    <div class="card-header" style="background: white; border-bottom: 1px solid #f3f4f6; padding: 1.25rem 1.5rem; border-top-left-radius: 1rem; border-top-right-radius: 1rem;">
        <h3 style="margin: 0; font-size: 1.1rem; color: #111827; display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-lock" style="color: #f59e0b;"></i> Keamanan Akun
        </h3>
    </div>
    <div class="card-body" style="padding: 1.5rem;">
        <form action="{{ route('karyawan.profil.updatePassword') }}" method="POST">
            @csrf @method('PUT')
            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label" style="font-size: 0.85rem; color: #374151;">Password Saat Ini</label>
                <div style="position: relative;">
                    <span style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #9ca3af;"><i class="fas fa-key"></i></span>
                    <input type="password" id="current_password" name="current_password" class="form-input" oninput="toggleIconVisibility('btn-eye-1', this.value)" style="padding-left: 2.5rem; padding-right: 2.5rem; width: 100%; box-sizing: border-box;" required>
                    <button type="button" id="btn-eye-1" onclick="togglePassword('current_password', 'eye-icon-1')" style="display: none; position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; align-items: center; justify-content: center; padding: 0;">
                        <svg id="eye-icon-1" style="width: 1.25rem; height: 1.25rem; color: #6b7280;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                </div>
            </div>
            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label" style="font-size: 0.85rem; color: #374151;">Password Baru</label>
                <div style="position: relative;">
                    <span style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #9ca3af;"><i class="fas fa-lock"></i></span>
                    <input type="password" id="password" name="password" class="form-input" oninput="toggleIconVisibility('btn-eye-2', this.value)" style="padding-left: 2.5rem; padding-right: 2.5rem; width: 100%; box-sizing: border-box;" required minlength="6">
                    <button type="button" id="btn-eye-2" onclick="togglePassword('password', 'eye-icon-2')" style="display: none; position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; align-items: center; justify-content: center; padding: 0;">
                        <svg id="eye-icon-2" style="width: 1.25rem; height: 1.25rem; color: #6b7280;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                </div>
            </div>
            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label class="form-label" style="font-size: 0.85rem; color: #374151;">Konfirmasi Password Baru</label>
                <div style="position: relative;">
                    <span style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #9ca3af;"><i class="fas fa-check-circle"></i></span>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" oninput="toggleIconVisibility('btn-eye-3', this.value)" style="padding-left: 2.5rem; padding-right: 2.5rem; width: 100%; box-sizing: border-box;" required minlength="6">
                    <button type="button" id="btn-eye-3" onclick="togglePassword('password_confirmation', 'eye-icon-3')" style="display: none; position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; align-items: center; justify-content: center; padding: 0;">
                        <svg id="eye-icon-3" style="width: 1.25rem; height: 1.25rem; color: #6b7280;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                </div>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.75rem; border-radius: 0.5rem; font-weight: 600; display: flex; justify-content: center; align-items: center; gap: 0.5rem;">
                <i class="fas fa-save"></i> Simpan Password
            </button>
        </form>
    </div>
</div>

<!-- Logout Card -->
<div style="margin-bottom: 3rem;">
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" style="width: 100%; padding: 1rem; background: white; border: 1px solid #ef4444; color: #ef4444; border-radius: 1rem; font-weight: 700; display: flex; justify-content: center; align-items: center; gap: 0.5rem; cursor: pointer; transition: all 0.2s; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
            <i class="fas fa-sign-out-alt"></i> Keluar Aplikasi (Logout)
        </button>
    </form>
</div>

<script>
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

