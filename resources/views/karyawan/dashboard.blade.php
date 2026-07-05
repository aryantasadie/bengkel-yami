@extends('layouts.karyawan')

@section('title', 'Dashboard Karyawan')

@section('content')
<div class="stats-grid-2">
    <div class="stat-card" style="flex-direction: column; align-items: center; text-align: center; gap: 0.5rem; padding: 1rem;">
        <div class="stat-card-icon blue">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
            </svg>
        </div>
        <div>
            <div class="stat-card-value" style="font-size: 1.5rem;">{{ $tugasHariIni ?? 0 }}</div>
            <div class="stat-card-label" style="font-size: 0.75rem;">Tugas Hari Ini</div>
        </div>
    </div>
    <div class="stat-card" style="flex-direction: column; align-items: center; text-align: center; gap: 0.5rem; padding: 1rem;">
        <div class="stat-card-icon green">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
        </div>
        <div>
            <div class="stat-card-value" style="font-size: 1.5rem;">{{ $tugasSelesai ?? 0 }}</div>
            <div class="stat-card-label" style="font-size: 0.75rem;">Selesai</div>
        </div>
    </div>
</div>

<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-header" style="padding: 1rem;">
        <h3 style="font-size: 1rem;">Absensi Hari Ini ({{ \Carbon\Carbon::now()->format('d M Y') }})</h3>
    </div>
    <div class="card-body" style="padding: 1rem; display: flex; gap: 1rem; align-items: center;">
        <div style="flex: 1; text-align: center;">
            <div style="font-size: 0.75rem; color: var(--neutral-500); margin-bottom: 0.25rem;">Jam Masuk</div>
            <div style="font-size: 1.25rem; font-weight: 600; color: {{ $absensiHariIni && $absensiHariIni->jam_masuk ? 'var(--neutral-900)' : 'var(--neutral-400)' }}">
                {{ $absensiHariIni->jam_masuk ?? '--:--' }}
            </div>
            @if(!$absensiHariIni || !$absensiHariIni->jam_masuk)
            <form action="{{ route('karyawan.absensi.clockIn') }}" method="POST" style="margin-top: 0.5rem;">
                @csrf
                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.5rem; font-size: 0.875rem; background: var(--primary); color: white; border-radius: var(--radius);">Clock In</button>
            </form>
            @endif
        </div>
        <div style="width: 1px; background: var(--neutral-200); height: 60px;"></div>
        <div style="flex: 1; text-align: center;">
            <div style="font-size: 0.75rem; color: var(--neutral-500); margin-bottom: 0.25rem;">Jam Keluar</div>
            <div style="font-size: 1.25rem; font-weight: 600; color: {{ $absensiHariIni && $absensiHariIni->jam_keluar ? 'var(--neutral-900)' : 'var(--neutral-400)' }}">
                {{ $absensiHariIni->jam_keluar ?? '--:--' }}
            </div>
            @if($absensiHariIni && $absensiHariIni->jam_masuk && !$absensiHariIni->jam_keluar)
            <form action="{{ route('karyawan.absensi.clockOut') }}" method="POST" style="margin-top: 0.5rem;">
                @csrf
                <button type="submit" class="btn btn-outline" style="width: 100%; padding: 0.5rem; font-size: 0.875rem; border: 1px solid var(--danger); color: var(--danger); border-radius: var(--radius); background: transparent;">Clock Out</button>
            </form>
            @endif
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header" style="padding: 1rem; display: flex; justify-content: space-between; align-items: center;">
        <h3 style="font-size: 1rem;">Tugas Aktif</h3>
        <a href="{{ route('karyawan.tugas.index') }}" style="font-size: 0.875rem; color: var(--primary); text-decoration: none;">Semua</a>
    </div>
    <div class="card-body" style="padding: 0;">
        @forelse ($tugasList ?? [] as $tugas)
        <div style="padding: 1rem; border-bottom: 1px solid var(--neutral-200);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
                <div style="font-weight: 600; font-size: 0.875rem;">{{ $tugas->pesananJasa->pesanan->no_pesanan }}</div>
                <span style="background: var(--warning-light); color: var(--warning); padding: 2px 6px; border-radius: 4px; font-size: 10px; font-weight: 600;">{{ ucfirst($tugas->status) }}</span>
            </div>
            <div style="font-size: 0.875rem; color: var(--neutral-700); margin-bottom: 0.25rem;">
                {{ $tugas->pesananJasa->jasa->nama_jasa }}
            </div>
            <div style="font-size: 0.75rem; color: var(--neutral-500); margin-bottom: 0.75rem;">
                Customer: {{ $tugas->pesananJasa->pesanan->customer->nama ?? '-' }}
            </div>
            
            @if($tugas->status === 'ditugaskan')
            <form action="{{ route('karyawan.tugas.updateStatus', $tugas->id) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="status" value="proses">
                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.5rem; background: var(--primary); color: white; border-radius: var(--radius); font-size: 0.875rem; font-weight: 500;">
                    <i class="fas fa-play"></i> Mulai Kerjakan
                </button>
            </form>
            @elseif($tugas->status === 'proses')
            <form action="{{ route('karyawan.tugas.updateStatus', $tugas->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin tugas ini sudah selesai sepenuhnya?');">
                @csrf
                @method('PUT')
                <input type="hidden" name="status" value="selesai">
                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.5rem; background: var(--success); color: white; border-radius: var(--radius); font-size: 0.875rem; font-weight: 500;">
                    <i class="fas fa-check-double"></i> Tandai Selesai
                </button>
            </form>
            @endif
        </div>
        @empty
        <div style="padding: 2rem; text-align: center; color: var(--neutral-500); font-size: 0.875rem;">
            Tidak ada tugas aktif saat ini.
        </div>
        @endforelse
    </div>
</div>
@endsection
