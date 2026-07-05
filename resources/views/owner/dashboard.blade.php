@extends('layouts.app')

@section('title', 'Dashboard Owner')

@section('content')
<div class="stats-grid-3">
    <div class="stat-card">
        <div class="stat-card-icon green">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
        </div>
        <div class="stat-card-info">
            <div class="stat-card-value">Rp {{ number_format($pendapatanBulanIni ?? 0, 0, ',', '.') }}</div>
            <div class="stat-card-label">Pendapatan Bulan Ini</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-card-icon red">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4" />
            </svg>
        </div>
        <div class="stat-card-info">
            <div class="stat-card-value">Rp {{ number_format($pengeluaranBulanIni ?? 0, 0, ',', '.') }}</div>
            <div class="stat-card-label">Pengeluaran Bulan Ini</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-card-icon blue">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
            </svg>
        </div>
        <div class="stat-card-info">
            <div class="stat-card-value">Rp {{ number_format($saldoBulanIni ?? 0, 0, ',', '.') }}</div>
            <div class="stat-card-label">Saldo Bulan Ini</div>
        </div>
    </div>
</div>

<div class="stats-grid-3">
    <a href="{{ route('owner.payroll.index') }}" class="stat-card" style="text-decoration: none; align-items: center; justify-content: center; text-align: center; flex-direction: column;">
        <div class="stat-card-icon blue" style="margin-bottom: 1rem;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
        </div>
        <h3 style="font-size: 1.125rem; font-weight: 600; color: var(--neutral-900);">Payroll Karyawan</h3>
        <p style="color: var(--neutral-500); font-size: 0.875rem; margin-top: 0.25rem;">Kelola gaji, komisi, dan potongan</p>
    </a>
    
    <a href="{{ route('owner.cashflow.index') }}" class="stat-card" style="text-decoration: none; align-items: center; justify-content: center; text-align: center; flex-direction: column;">
        <div class="stat-card-icon yellow" style="margin-bottom: 1rem;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
        </div>
        <h3 style="font-size: 1.125rem; font-weight: 600; color: var(--neutral-900);">Laporan Arus Kas</h3>
        <p style="color: var(--neutral-500); font-size: 0.875rem; margin-top: 0.25rem;">Pantau perputaran uang harian</p>
    </a>
    
    <a href="{{ route('owner.laba-rugi.index') }}" class="stat-card" style="text-decoration: none; align-items: center; justify-content: center; text-align: center; flex-direction: column;">
        <div class="stat-card-icon green" style="margin-bottom: 1rem;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
            </svg>
        </div>
        <h3 style="font-size: 1.125rem; font-weight: 600; color: var(--neutral-900);">Laba & Rugi</h3>
        <p style="color: var(--neutral-500); font-size: 0.875rem; margin-top: 0.25rem;">Analisis keuntungan bengkel</p>
    </a>
</div>
@endsection
