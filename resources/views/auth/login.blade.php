@extends('layouts.auth')

@section('content')
<div class="auth-card">
    <div class="auth-brand">
        <div class="auth-brand-icon">
            BY
        </div>
        <h1>Bengkel Yami</h1>
        <p>Sistem Informasi Manajemen</p>
    </div>

    <form action="{{ route('login') }}" method="POST">
        @csrf
        <div class="form-group">
            <label class="form-label" for="username">Username</label>
            <input type="text" id="username" name="username" class="form-input @error('username') is-invalid @enderror" value="{{ old('username') }}" required autofocus>
            @error('username')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="password">Password</label>
            <div style="position: relative;">
                <input type="password" id="password" name="password" oninput="toggleLoginIconVisibility(this.value)" class="form-input @error('password') is-invalid @enderror" required style="width: 100%; padding-right: 2.5rem;">
                <button type="button" id="btn-login-eye" onclick="toggleLoginPassword()" style="display: none; position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #6b7280; align-items: center; justify-content: center; padding: 0;">
                    <svg id="login-eye-icon" style="width: 1.25rem; height: 1.25rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </button>
            </div>
            @error('password')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group" style="margin-top: 2rem;">
            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.75rem; background: var(--primary); color: white; border-radius: var(--radius-md); font-weight: 600; font-size: 1rem;">
                Masuk ke Sistem
            </button>
        </div>
    </form>

    <div style="text-align: center; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--neutral-200);">
        <p class="text-sm text-muted">Hubungi administrator untuk mendapatkan akun.</p>
    </div>
</div>

<script>
    function toggleLoginIconVisibility(value) {
        const btn = document.getElementById('btn-login-eye');
        if (value.length > 0) {
            btn.style.display = 'flex';
        } else {
            btn.style.display = 'none';
        }
    }

    function toggleLoginPassword() {
        const input = document.getElementById('password');
        const icon = document.getElementById('login-eye-icon');
        
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
