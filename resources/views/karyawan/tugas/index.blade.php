@extends('layouts.karyawan')
@section('title', 'Tugas Saya')

@section('content')
<div style="margin-bottom: 2rem;">
    <h2 style="margin: 0; color: #111827; display: flex; align-items: center; gap: 0.5rem;">
        <i class="fas fa-clipboard-list" style="color: #4f46e5;"></i> Tugas Servis Saya
    </h2>
    <p style="margin: 0.2rem 0 0 0; color: #6b7280;">Kelola pekerjaan Anda hari ini. Selamat bekerja, {{ $karyawan->nama }}!</p>
</div>

@if(session('success'))
    <div class="alert alert-success" style="margin-bottom: 1.5rem;">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-error" style="margin-bottom: 1.5rem;">{{ session('error') }}</div>
@endif

<!-- KANBAN BOARD -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; align-items: start;">

    <!-- KOLOM 1: TUGAS BARU (DITUGASKAN) -->
    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 0.75rem; overflow: hidden;">
        <div style="background: #e2e8f0; padding: 1rem; border-bottom: 2px solid #cbd5e1;">
            <h4 style="margin: 0; font-size: 1rem; color: #334155; display: flex; justify-content: space-between; align-items: center;">
                <span><i class="fas fa-inbox" style="color: #64748b;"></i> Tugas Baru</span>
                <span style="background: #cbd5e1; color: #334155; padding: 0.2rem 0.6rem; border-radius: 999px; font-size: 0.8rem;">{{ $tugasBaru->count() }}</span>
            </h4>
        </div>
        <div style="padding: 1rem; display: flex; flex-direction: column; gap: 1rem;">
            @forelse($tugasBaru as $tugas)
                <div class="card" style="margin: 0; border-left: 4px solid #3b82f6; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                    <div class="card-body" style="padding: 1rem;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                            <span style="font-size: 0.8rem; font-weight: 600; color: #3b82f6;">{{ $tugas->pesananJasa->pesanan->no_pesanan }}</span>
                            <span style="font-size: 0.75rem; color: #94a3b8;">{{ \Carbon\Carbon::parse($tugas->created_at)->format('H:i') }}</span>
                        </div>
                        <h5 style="margin: 0 0 0.5rem 0; font-size: 1.1rem; color: #0f172a;">{{ $tugas->pesananJasa->jasa->nama_jasa }}</h5>
                        <div style="font-size: 0.85rem; color: #475569; margin-bottom: 0.2rem;"><i class="fas fa-motorcycle" style="width: 16px;"></i> {{ $tugas->pesananJasa->pesanan->kendaraan }}</div>
                        <div style="font-size: 0.85rem; color: #475569; margin-bottom: 1rem;"><i class="fas fa-user" style="width: 16px;"></i> {{ $tugas->pesananJasa->pesanan->customer->nama ?? 'Umum' }}</div>
                        
                        <form action="{{ route('karyawan.tugas.updateStatus', $tugas->id) }}" method="POST">
                            @csrf @method('PUT')
                            <input type="hidden" name="status" value="proses">
                            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.6rem; font-weight: 600;">
                                <i class="fas fa-play"></i> Mulai Kerjakan
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div style="text-align: center; color: #94a3b8; padding: 2rem 0; font-size: 0.9rem;">
                    <i class="fas fa-mug-hot" style="font-size: 2rem; margin-bottom: 0.5rem; color: #cbd5e1;"></i><br>
                    Belum ada tugas baru.
                </div>
            @endforelse
        </div>
    </div>

    <!-- KOLOM 2: SEDANG DIPROSES -->
    <div style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 0.75rem; overflow: hidden;">
        <div style="background: #fef3c7; padding: 1rem; border-bottom: 2px solid #fcd34d;">
            <h4 style="margin: 0; font-size: 1rem; color: #92400e; display: flex; justify-content: space-between; align-items: center;">
                <span><i class="fas fa-tools" style="color: #d97706;"></i> Sedang Diproses</span>
                <span style="background: #fcd34d; color: #92400e; padding: 0.2rem 0.6rem; border-radius: 999px; font-size: 0.8rem;">{{ $tugasProses->count() }}</span>
            </h4>
        </div>
        <div style="padding: 1rem; display: flex; flex-direction: column; gap: 1rem;">
            @forelse($tugasProses as $tugas)
                <div class="card" style="margin: 0; border-left: 4px solid #f59e0b; box-shadow: 0 4px 6px -1px rgba(245, 158, 11, 0.2);">
                    <div class="card-body" style="padding: 1rem;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                            <span style="font-size: 0.8rem; font-weight: 600; color: #d97706;">{{ $tugas->pesananJasa->pesanan->no_pesanan }}</span>
                            <span style="font-size: 0.75rem; color: #d97706; animation: pulse-text 2s infinite;">Live <i class="fas fa-circle" style="font-size: 0.4rem; vertical-align: middle;"></i></span>
                        </div>
                        <h5 style="margin: 0 0 0.5rem 0; font-size: 1.1rem; color: #0f172a;">{{ $tugas->pesananJasa->jasa->nama_jasa }}</h5>
                        <div style="font-size: 0.85rem; color: #475569; margin-bottom: 0.2rem;"><i class="fas fa-motorcycle" style="width: 16px;"></i> {{ $tugas->pesananJasa->pesanan->kendaraan }}</div>
                        <div style="font-size: 0.85rem; color: #475569; margin-bottom: 1rem;"><i class="fas fa-user" style="width: 16px;"></i> {{ $tugas->pesananJasa->pesanan->customer->nama ?? 'Umum' }}</div>
                        
                        <form action="{{ route('karyawan.tugas.updateStatus', $tugas->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin tugas ini sudah selesai sepenuhnya?');">
                            @csrf @method('PUT')
                            <input type="hidden" name="status" value="selesai">
                            <button type="submit" class="btn btn-success" style="width: 100%; padding: 0.6rem; font-weight: 600;">
                                <i class="fas fa-check-double"></i> Tandai Selesai
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div style="text-align: center; color: #d97706; padding: 2rem 0; font-size: 0.9rem; opacity: 0.7;">
                    <i class="fas fa-wrench" style="font-size: 2rem; margin-bottom: 0.5rem;"></i><br>
                    Tidak ada motor yang sedang dikerjakan.
                </div>
            @endforelse
        </div>
    </div>

    <!-- KOLOM 3: SELESAI BULAN INI -->
    <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 0.75rem; overflow: hidden;">
        <div style="background: #dcfce3; padding: 1rem; border-bottom: 2px solid #86efac;">
            <h4 style="margin: 0; font-size: 1rem; color: #166534; display: flex; justify-content: space-between; align-items: center;">
                <span><i class="fas fa-award" style="color: #22c55e;"></i> Selesai (Bulan Ini)</span>
                <span style="background: #86efac; color: #166534; padding: 0.2rem 0.6rem; border-radius: 999px; font-size: 0.8rem;">{{ $tugasSelesai->count() }}</span>
            </h4>
        </div>
        <div style="padding: 1rem; display: flex; flex-direction: column; gap: 1rem; max-height: 600px; overflow-y: auto;">
            @forelse($tugasSelesai as $tugas)
                <div class="card" style="margin: 0; border-left: 4px solid #10b981; opacity: 0.85;">
                    <div class="card-body" style="padding: 0.8rem 1rem;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.3rem;">
                            <span style="font-size: 0.75rem; font-weight: 600; color: #059669;">{{ $tugas->pesananJasa->pesanan->no_pesanan }}</span>
                            <span style="font-size: 0.7rem; color: #64748b;">{{ \Carbon\Carbon::parse($tugas->updated_at)->format('d M, H:i') }}</span>
                        </div>
                        <h5 style="margin: 0; font-size: 0.95rem; color: #0f172a;">{{ $tugas->pesananJasa->jasa->nama_jasa }}</h5>
                    </div>
                </div>
            @empty
                <div style="text-align: center; color: #15803d; padding: 2rem 0; font-size: 0.9rem; opacity: 0.7;">
                    <i class="fas fa-box-open" style="font-size: 2rem; margin-bottom: 0.5rem;"></i><br>
                    Belum ada riwayat selesai bulan ini.<br>Ayo semangat!
                </div>
            @endforelse
        </div>
    </div>

</div>

<style>
@keyframes pulse-text {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}
</style>
@endsection
