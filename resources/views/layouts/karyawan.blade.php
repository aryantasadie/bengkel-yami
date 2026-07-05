<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Karyawan - Bengkel Yami')</title>
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="karyawan-wrapper">
    
    <div class="karyawan-topbar">
        <div class="karyawan-topbar-brand">
            Bengkel Yami
        </div>
        <div class="karyawan-topbar-user" style="display: flex; align-items: center; gap: 1rem;">
            Halo, {{ auth()->user()->nama ?? 'Karyawan' }}
            <form method="POST" action="{{ route('logout') }}" style="margin: 0; display: flex;">
                @csrf
                <button type="submit" style="background: transparent; border: none; color: var(--danger); cursor: pointer; padding: 0; display: flex; align-items: center;" title="Keluar">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                </button>
            </form>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success" style="margin: 1rem;">
            {{ session('success') }}
            <button type="button" class="alert-dismiss">&times;</button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-error" style="margin: 1rem; background-color: var(--danger-light); color: var(--danger); border: 1px solid var(--danger); padding: var(--space-3); border-radius: var(--radius-md);">
            {{ session('error') }}
            <button type="button" class="alert-dismiss">&times;</button>
        </div>
    @endif

    <div class="karyawan-content">
        @yield('content')
    </div>
    
    <nav class="bottom-nav">
        <a href="{{ route('karyawan.dashboard') }}" class="bottom-nav-item {{ request()->routeIs('karyawan.dashboard') ? 'active' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            Home
        </a>
        <a href="{{ route('karyawan.absensi.index') }}" class="bottom-nav-item {{ request()->routeIs('karyawan.absensi.*') ? 'active' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Absensi
        </a>
        <a href="{{ route('karyawan.tugas.index') }}" class="bottom-nav-item {{ request()->routeIs('karyawan.tugas.*') ? 'active' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
            </svg>
            Tugas
        </a>
        <a href="{{ route('karyawan.profil.index') }}" class="bottom-nav-item {{ request()->routeIs('karyawan.profil.*') ? 'active' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            Profil
        </a>
    </nav>
    
</body>
</html>
