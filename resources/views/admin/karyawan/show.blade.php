@extends('layouts.app')
@section('title', 'Monitoring Karyawan')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <div>
        <h2 style="margin: 0; color: #111827; display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-desktop" style="color: #4f46e5;"></i> Monitoring Karyawan
        </h2>
        <p style="margin: 0.2rem 0 0 0; color: #6b7280;">Pantau aktivitas dan kinerja <strong>{{ $karyawan->nama }}</strong> secara langsung.</p>
    </div>
    <a href="{{ route('admin.karyawan.index') }}" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Kembali</a>
</div>

<!-- TOP CARDS -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <!-- Profile Card -->
    <div class="card" style="margin-bottom: 0;">
        <div class="card-body" style="display: flex; gap: 1.5rem; align-items: center;">
            <div style="width: 80px; height: 80px; background: #e0e7ff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; color: #4f46e5; flex-shrink: 0;">
                <i class="fas fa-user-tie"></i>
            </div>
            <div>
                <h3 style="margin: 0; font-size: 1.25rem; color: #111827;">{{ $karyawan->nama }}</h3>
                <div style="color: #4f46e5; font-weight: 600; margin-bottom: 0.5rem;">{{ $karyawan->jabatan }}</div>
            </div>
        </div>
    </div>

    <!-- Live Status Card -->
    <div class="card" style="margin-bottom: 0;">
        <div class="card-body">
            <div style="font-size: 0.85rem; color: #6b7280; margin-bottom: 0.5rem; font-weight: 600; text-transform: uppercase;">Status Hari Ini ({{ \Carbon\Carbon::today()->format('d M Y') }})</div>
            @if($absensiHariIni)
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="width: 12px; height: 12px; background: #10b981; border-radius: 50%; box-shadow: 0 0 0 4px #d1fae5; animation: pulse 2s infinite;"></div>
                    <div>
                        <div style="font-size: 1.25rem; font-weight: 700; color: #10b981;">HADIR</div>
                        <div style="font-size: 0.85rem; color: #6b7280;">Clock In: {{ \Carbon\Carbon::parse($absensiHariIni->jam_masuk)->format('H:i') }}</div>
                    </div>
                </div>
            @else
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="width: 12px; height: 12px; background: #ef4444; border-radius: 50%; box-shadow: 0 0 0 4px #fee2e2;"></div>
                    <div>
                        <div style="font-size: 1.25rem; font-weight: 700; color: #ef4444;">BELUM ABSEN</div>
                        <div style="font-size: 0.85rem; color: #6b7280;">Karyawan belum melakukan Clock In</div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
@keyframes pulse {
    0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4); }
    70% { box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
    100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
}
</style>

<!-- BIODATA KARYAWAN -->
<div class="card" style="margin-bottom: 2rem;">
    <div class="card-body">
        <h4 style="margin: 0 0 1rem 0; color: #111827; font-size: 1.1rem; border-bottom: 1px solid #e5e7eb; padding-bottom: 0.5rem;">Informasi Lengkap Karyawan</h4>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
            <div>
                <div style="font-size: 0.8rem; color: #6b7280; font-weight: 600;">Tanggal Lahir</div>
                <div style="font-size: 0.95rem; color: #111827;">{{ $karyawan->tanggal_lahir ? \Carbon\Carbon::parse($karyawan->tanggal_lahir)->format('d M Y') : '-' }}</div>
            </div>
            <div>
                <div style="font-size: 0.8rem; color: #6b7280; font-weight: 600;">Tanggal Masuk / Bergabung</div>
                <div style="font-size: 0.95rem; color: #111827;">{{ $karyawan->tanggal_masuk ? \Carbon\Carbon::parse($karyawan->tanggal_masuk)->format('d M Y') : '-' }}</div>
            </div>
            <div>
                <div style="font-size: 0.8rem; color: #6b7280; font-weight: 600;">Gaji Pokok</div>
                <div style="font-size: 0.95rem; color: #111827; font-weight: 600;">Rp {{ number_format($karyawan->gaji_pokok, 0, ',', '.') }}</div>
            </div>
            <div>
                <div style="font-size: 0.8rem; color: #6b7280; font-weight: 600;">Tunjangan (Harian)</div>
                <div style="font-size: 0.95rem; color: #111827;">Rp {{ number_format($karyawan->tunjangan, 0, ',', '.') }}</div>
            </div>
            <div style="grid-column: 1 / -1;">
                <div style="font-size: 0.8rem; color: #6b7280; font-weight: 600;">Alamat Lengkap</div>
                <div style="font-size: 0.95rem; color: #111827;">{{ $karyawan->alamat ?? '-' }}</div>
            </div>
        </div>
        <div style="margin-top: 1.5rem; display: flex; justify-content: flex-end;">
            <a href="{{ route('admin.karyawan.edit', $karyawan->id) }}" class="btn btn-outline" style="font-size: 0.85rem;"><i class="fas fa-edit"></i> Edit Data</a>
        </div>
    </div>
</div>

<!-- LIVE TRACKING -->
<h3 style="color: #111827; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;"><i class="fas fa-broadcast-tower" style="color: #ef4444;"></i> Live Tracking Pekerjaan</h3>
<div class="card" style="margin-bottom: 2rem;">
    <div class="card-body">
        @if($tugasAktif->isEmpty())
            <div style="text-align: center; color: #6b7280; padding: 2rem 0;">
                <i class="fas fa-coffee" style="font-size: 2rem; color: #d1d5db; margin-bottom: 1rem;"></i>
                <p style="margin: 0;">Karyawan sedang tidak mengerjakan servis apapun (Standby).</p>
            </div>
        @else
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
                @foreach($tugasAktif as $tugas)
                <div style="border: 1px solid #e5e7eb; border-radius: 0.5rem; padding: 1rem; border-left: 4px solid #f59e0b; background: #fffbeb;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span style="font-size: 0.8rem; font-weight: 600; color: #d97706; text-transform: uppercase;">{{ $tugas->status }}</span>
                        <span style="font-size: 0.8rem; color: #6b7280;">{{ $tugas->pesananJasa->pesanan->no_pesanan }}</span>
                    </div>
                    <div style="font-weight: 700; color: #111827; margin-bottom: 0.2rem;">{{ $tugas->pesananJasa->jasa->nama_jasa }}</div>
                    <div style="font-size: 0.85rem; color: #4b5563;"><i class="fas fa-motorcycle"></i> {{ $tugas->pesananJasa->pesanan->kendaraan }}</div>
                    <div style="font-size: 0.85rem; color: #4b5563;"><i class="fas fa-user"></i> Pelanggan: {{ $tugas->pesananJasa->pesanan->customer->nama ?? 'Umum' }}</div>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<hr style="border: 0; border-top: 1px dashed #d1d5db; margin: 3rem 0;">

<!-- FILTER PERIODE -->
<div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 1rem;">
    <h3 style="color: #111827; margin: 0;"><i class="fas fa-history" style="color: #3b82f6;"></i> Riwayat Periode</h3>
    <form action="{{ route('admin.karyawan.show', $karyawan->id) }}" method="GET" style="display: flex; gap: 0.5rem; align-items: flex-end;">
        <div>
            <label style="font-size: 0.8rem; color: #6b7280; font-weight: 600;">Bulan</label>
            <select name="bulan" class="form-control" style="padding: 0.4rem; height: auto;">
                @for($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}</option>
                @endfor
            </select>
        </div>
        <div>
            <label style="font-size: 0.8rem; color: #6b7280; font-weight: 600;">Tahun</label>
            <select name="tahun" class="form-control" style="padding: 0.4rem; height: auto;">
                @for($i = date('Y'); $i >= 2020; $i--)
                    <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>{{ $i }}</option>
                @endfor
            </select>
        </div>
        <button type="submit" class="btn btn-primary" style="padding: 0.5rem 1rem;"><i class="fas fa-filter"></i> Filter</button>
    </form>
</div>

<!-- TABLES: ABSENSI & PEKERJAAN -->
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; align-items: start;">
    
    <!-- Riwayat Absensi -->
    <div class="card">
        <div class="card-header" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
            <h4 style="margin: 0; font-size: 1rem; color: #1e293b;">Riwayat Absensi</h4>
        </div>
        <div class="card-body" style="padding: 0;">
            @if($absensis->isEmpty())
                <div style="padding: 2rem; text-align: center; color: #94a3b8;">Belum ada catatan absensi.</div>
            @else
                <table style="width: 100%; border-collapse: collapse;">
                    <thead style="background: #f1f5f9;">
                        <tr>
                            <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.85rem; color: #475569;">Tanggal</th>
                            <th style="padding: 0.75rem 1rem; text-align: center; font-size: 0.85rem; color: #475569;">Masuk</th>
                            <th style="padding: 0.75rem 1rem; text-align: center; font-size: 0.85rem; color: #475569;">Keluar</th>
                            <th style="padding: 0.75rem 1rem; text-align: center; font-size: 0.85rem; color: #475569;">Status</th>
                            <th style="padding: 0.75rem 1rem; text-align: center; font-size: 0.85rem; color: #475569;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($absensis as $absen)
                        <tr>
                            <td style="padding: 0.75rem 1rem; border-bottom: 1px solid #f1f5f9; color: #334155;">{{ \Carbon\Carbon::parse($absen->tanggal)->format('d M Y') }}</td>
                            <td style="padding: 0.75rem 1rem; border-bottom: 1px solid #f1f5f9; text-align: center; color: #10b981; font-weight: 600;">{{ $absen->jam_masuk ? \Carbon\Carbon::parse($absen->jam_masuk)->format('H:i') : '-' }}</td>
                            <td style="padding: 0.75rem 1rem; border-bottom: 1px solid #f1f5f9; text-align: center; color: #f59e0b; font-weight: 600;">{{ $absen->jam_keluar ? \Carbon\Carbon::parse($absen->jam_keluar)->format('H:i') : '-' }}</td>
                            <td style="padding: 0.75rem 1rem; border-bottom: 1px solid #f1f5f9; text-align: center; font-weight: 600; color: {{ str_contains(strtolower($absen->status_view), 'alpha') ? '#ef4444' : '#10b981' }};">{{ $absen->status_view }}</td>
                            <td style="padding: 0.75rem 1rem; border-bottom: 1px solid #f1f5f9; text-align: center;">
                                @php
                                    $jamMasukFmt = $absen->jam_masuk ? \Carbon\Carbon::parse($absen->jam_masuk)->format('H:i') : '';
                                    $jamKeluarFmt = $absen->jam_keluar ? \Carbon\Carbon::parse($absen->jam_keluar)->format('H:i') : '';
                                @endphp
                                <button type="button" class="btn btn-outline" style="padding: 0.3rem 0.6rem; font-size: 0.75rem;" onclick="openEditAbsenModal('{{ $absen->tanggal }}', '{{ $jamMasukFmt }}', '{{ $jamKeluarFmt }}', '{{ str_contains(strtolower($absen->status_view), 'alpha') ? 'alpha' : strtolower($absen->status ?? 'hadir') }}')">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <!-- Riwayat Pekerjaan Selesai -->
    <div class="card">
        <div class="card-header" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
            <h4 style="margin: 0; font-size: 1rem; color: #1e293b;">Riwayat Servis Selesai</h4>
        </div>
        <div class="card-body" style="padding: 0;">
            @if($riwayatTugas->isEmpty())
                <div style="padding: 2rem; text-align: center; color: #94a3b8;">Belum ada servis selesai.</div>
            @else
                <table style="width: 100%; border-collapse: collapse;">
                    <thead style="background: #f1f5f9;">
                        <tr>
                            <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.85rem; color: #475569;">Selesai Pada</th>
                            <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.85rem; color: #475569;">Pekerjaan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($riwayatTugas as $tugas)
                        <tr>
                            <td style="padding: 0.75rem 1rem; border-bottom: 1px solid #f1f5f9; color: #334155; font-size: 0.9rem;">
                                {{ \Carbon\Carbon::parse($tugas->updated_at)->format('d M Y') }}
                            </td>
                            <td style="padding: 0.75rem 1rem; border-bottom: 1px solid #f1f5f9;">
                                <div style="font-weight: 600; color: #0f172a; font-size: 0.95rem;">{{ $tugas->pesananJasa->jasa->nama_jasa }}</div>
                                <div style="font-size: 0.8rem; color: #64748b;">Nota: {{ $tugas->pesananJasa->pesanan->no_pesanan }}</div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

</div>

<!-- Modal Edit Absensi -->
<div id="editAbsenModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 9999;">
    <div style="background: white; padding: 2rem; border-radius: 0.5rem; width: 100%; max-width: 400px; margin: auto;">
        <h3 style="margin-top: 0;">Edit Absensi Karyawan</h3>
        <form id="formEditAbsen" action="{{ route('admin.karyawan.absensi.update', $karyawan->id) }}" method="POST">
            @csrf
            <div class="form-group" style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Tanggal</label>
                <input type="text" id="editAbsenTanggal" name="tanggal" class="form-control" readonly style="background: #f1f5f9; width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 0.25rem;">
            </div>
            <div class="form-group" style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Status</label>
                <select name="status" id="editAbsenStatus" class="form-control" required style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 0.25rem;">
                    <option value="hadir">Hadir</option>
                    <option value="izin">Izin</option>
                    <option value="sakit">Sakit</option>
                    <option value="alpha">Alpha</option>
                </select>
            </div>
            <div class="form-group" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Jam Masuk</label>
                    <input type="time" name="jam_masuk" id="editAbsenMasuk" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 0.25rem;">
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Jam Keluar</label>
                    <input type="time" name="jam_keluar" id="editAbsenKeluar" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 0.25rem;">
                </div>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 1rem; margin-top: 1.5rem;">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('editAbsenModal').style.display='none'">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditAbsenModal(tanggal, masuk, keluar, status) {
    document.getElementById('editAbsenTanggal').value = tanggal;
    document.getElementById('editAbsenMasuk').value = masuk;
    document.getElementById('editAbsenKeluar').value = keluar;
    document.getElementById('editAbsenStatus').value = status;
    document.getElementById('editAbsenModal').style.display = 'flex';
    handleStatusChange(); // Initial check
}

document.getElementById('editAbsenStatus').addEventListener('change', handleStatusChange);

function handleStatusChange() {
    const status = document.getElementById('editAbsenStatus').value;
    const masukInput = document.getElementById('editAbsenMasuk');
    const keluarInput = document.getElementById('editAbsenKeluar');
    
    if (status === 'alpha' || status === 'sakit') {
        masukInput.value = '';
        keluarInput.value = '';
        masukInput.disabled = true;
        keluarInput.disabled = true;
    } else {
        // Hadir atau Izin bisa diisi jam
        masukInput.disabled = false;
        keluarInput.disabled = false;
    }
}
</script>

@endsection
