@extends('layouts.karyawan')
@section('title', 'Riwayat Kehadiran')

@section('content')
<div style="margin-bottom: 2rem;">
    <h2 style="margin: 0; color: #111827; display: flex; align-items: center; gap: 0.5rem;">
        <i class="fas fa-calendar-check" style="color: #10b981;"></i> Riwayat Kehadiran Saya
    </h2>
    <p style="margin: 0.2rem 0 0 0; color: #6b7280;">Pantau rekam jejak absensi Anda. (Clock In & Out dilakukan melalui halaman Dashboard).</p>
</div>

<!-- FILTER PERIODE -->
<div class="card" style="margin-bottom: 2rem;">
    <div class="card-body">
        <form action="{{ route('karyawan.absensi.index') }}" method="GET" style="display: flex; gap: 1rem; align-items: flex-end; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 200px;">
                <label style="font-size: 0.8rem; color: #6b7280; font-weight: 600;">Pilih Bulan & Tahun</label>
                <input type="month" name="bulan" class="form-control" value="{{ request('bulan') ?? \Carbon\Carbon::now()->format('Y-m') }}" required>
            </div>
            <button type="submit" class="btn btn-primary" style="padding: 0.6rem 1.5rem;"><i class="fas fa-search"></i> Tampilkan</button>
            <a href="{{ route('karyawan.absensi.index') }}" class="btn btn-outline" style="padding: 0.6rem 1.5rem;"><i class="fas fa-sync-alt"></i> Reset</a>
        </form>
    </div>
</div>

<!-- TABEL RIWAYAT ABSENSI -->
<div class="card">
    <div class="card-header" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
        <h3 style="margin: 0; font-size: 1.1rem; color: #1e293b;">Catatan Kehadiran</h3>
        <span style="background: #d1fae5; color: #065f46; padding: 0.2rem 0.8rem; border-radius: 999px; font-size: 0.85rem; font-weight: 600;">
            Total Hadir: {{ $absensis->total() }} Hari
        </span>
    </div>
    <div class="card-body" style="padding: 0; overflow-x: auto;">
        @if($absensis->isEmpty())
            <div style="padding: 3rem 1rem; text-align: center; color: #94a3b8;">
                <i class="fas fa-calendar-times" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                <p style="margin: 0;">Belum ada catatan kehadiran pada periode ini.</p>
            </div>
        @else
            <table style="width: 100%; border-collapse: collapse; min-width: 500px;">
                <thead style="background: #f1f5f9;">
                    <tr>
                        <th style="padding: 1rem; text-align: left; font-size: 0.85rem; color: #475569; font-weight: 600;">Tanggal</th>
                        <th style="padding: 1rem; text-align: center; font-size: 0.85rem; color: #475569; font-weight: 600;">Jam Masuk (Clock In)</th>
                        <th style="padding: 1rem; text-align: center; font-size: 0.85rem; color: #475569; font-weight: 600;">Jam Keluar (Clock Out)</th>
                        <th style="padding: 1rem; text-align: center; font-size: 0.85rem; color: #475569; font-weight: 600;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($absensis as $absen)
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 1rem; color: #334155; font-weight: 500;">
                            {{ \Carbon\Carbon::parse($absen->tanggal)->translatedFormat('l, d M Y') }}
                        </td>
                        <td style="padding: 1rem; text-align: center;">
                            @if($absen->jam_masuk)
                                <span style="background: #d1fae5; color: #059669; padding: 0.3rem 0.8rem; border-radius: 0.5rem; font-weight: 600;">
                                    <i class="fas fa-sign-in-alt"></i> {{ \Carbon\Carbon::parse($absen->jam_masuk)->format('H:i') }}
                                </span>
                            @else
                                <span style="color: #cbd5e1;">-</span>
                            @endif
                        </td>
                        <td style="padding: 1rem; text-align: center;">
                            @if($absen->jam_keluar)
                                <span style="background: #ffedd5; color: #ea580c; padding: 0.3rem 0.8rem; border-radius: 0.5rem; font-weight: 600;">
                                    <i class="fas fa-sign-out-alt"></i> {{ \Carbon\Carbon::parse($absen->jam_keluar)->format('H:i') }}
                                </span>
                            @else
                                <span style="color: #cbd5e1;">-</span>
                            @endif
                        </td>
                        <td style="padding: 1rem; text-align: center;">
                            <span style="color: #2563eb; font-weight: 600; text-transform: capitalize;">
                                <i class="fas fa-check-circle"></i> {{ $absen->status }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div style="padding: 1rem; border-top: 1px solid #e2e8f0;">
                {{ $absensis->links('pagination::bootstrap-4') }}
            </div>
        @endif
    </div>
</div>
@endsection
