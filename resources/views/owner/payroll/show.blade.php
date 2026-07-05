@extends('layouts.app')
@section('title', 'Slip Gaji Karyawan')

@section('content')
<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px dashed #e5e7eb;">
        <div>
            <h3 style="margin:0; color: #111827;">Slip Gaji Digital</h3>
            <p style="margin: 0.2rem 0 0 0; color: #6b7280; font-size: 0.9rem;">Bengkel Yami</p>
        </div>
        <div style="text-align: right;">
            <div style="font-size: 0.85rem; color: #6b7280; font-weight: 600; text-transform: uppercase;">Status</div>
            @if($payroll->status == 'draft')
                <span style="background: #f3f4f6; color: #4b5563; padding: 0.4rem 0.8rem; border-radius: 999px; font-size: 0.9rem; font-weight: 700;">DRAFT</span>
            @elseif($payroll->status == 'final')
                <span style="background: #fef3c7; color: #d97706; padding: 0.4rem 0.8rem; border-radius: 999px; font-size: 0.9rem; font-weight: 700;">FINAL</span>
            @elseif($payroll->status == 'dibayar')
                <span style="background: #dcfce3; color: #166534; padding: 0.4rem 0.8rem; border-radius: 999px; font-size: 0.9rem; font-weight: 700;">LUNAS</span>
            @endif
        </div>
    </div>
    
    <div class="card-body" style="padding: 2rem;">
        @if(session('success'))
            <div class="alert alert-success" style="margin-bottom: 1.5rem;">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error" style="margin-bottom: 1.5rem;">{{ session('error') }}</div>
        @endif

        <!-- Header Info -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
            <div>
                <div style="font-size: 0.85rem; color: #6b7280; margin-bottom: 0.2rem;">Data Karyawan</div>
                <div style="font-weight: 700; font-size: 1.1rem; color: #111827;">{{ $payroll->karyawan->nama }}</div>
                <div style="color: #4b5563; font-size: 0.9rem;">Jabatan: {{ $payroll->karyawan->jabatan }}</div>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 0.85rem; color: #6b7280; margin-bottom: 0.2rem;">Periode Penggajian</div>
                <div style="font-weight: 700; font-size: 1.1rem; color: #111827;">{{ \Carbon\Carbon::create()->month($payroll->periode_bulan)->translatedFormat('F') }} {{ $payroll->periode_tahun }}</div>
                @if($payroll->status == 'dibayar')
                    <div style="color: #166534; font-size: 0.9rem; font-weight: 600;">Dibayar pada: {{ \Carbon\Carbon::parse($payroll->tanggal_bayar)->format('d M Y') }}</div>
                @endif
            </div>
        </div>

        <!-- Rincian -->
        <div style="border: 1px solid #e5e7eb; border-radius: 0.5rem; overflow: hidden; margin-bottom: 2rem;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead style="background: #f9fafb;">
                    <tr>
                        <th style="padding: 1rem; text-align: left; font-size: 0.9rem; color: #4b5563;">Keterangan</th>
                        <th style="padding: 1rem; text-align: right; font-size: 0.9rem; color: #4b5563; width: 40%;">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Pendapatan -->
                    <tr>
                        <td colspan="2" style="padding: 1rem; background: #f0fdf4; font-weight: 700; color: #166534; border-bottom: 1px solid #e5e7eb;">PENERIMAAN</td>
                    </tr>
                    <tr>
                        <td style="padding: 0.75rem 1rem 0.75rem 2rem; border-bottom: 1px solid #f3f4f6; color: #374151;">Gaji Pokok</td>
                        <td style="padding: 0.75rem 1rem; text-align: right; border-bottom: 1px solid #f3f4f6; color: #374151;">Rp {{ number_format($payroll->gaji_pokok_snapshot, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 0.75rem 1rem 0.75rem 2rem; border-bottom: 1px solid #f3f4f6; color: #374151;">Tunjangan Harian (Aktif Kerja)</td>
                        <td style="padding: 0.75rem 1rem; text-align: right; border-bottom: 1px solid #f3f4f6; color: #374151;">Rp {{ number_format($payroll->tunjangan_snapshot, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 0.75rem 1rem 0.75rem 2rem; border-bottom: 1px solid #f3f4f6; color: #374151;">Komisi Servis</td>
                        <td style="padding: 0.75rem 1rem; text-align: right; border-bottom: 1px solid #f3f4f6; color: #374151;">Rp {{ number_format($payroll->total_komisi, 0, ',', '.') }}</td>
                    </tr>
                    @if($payroll->total_lembur > 0)
                    <tr>
                        <td style="padding: 0.75rem 1rem 0.75rem 2rem; border-bottom: 1px solid #f3f4f6; color: #374151;">Lembur</td>
                        <td style="padding: 0.75rem 1rem; text-align: right; border-bottom: 1px solid #f3f4f6; color: #374151;">Rp {{ number_format($payroll->total_lembur, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                    <tr style="background: #f9fafb;">
                        <td style="padding: 0.75rem 1rem; font-weight: 600; text-align: right; color: #374151;">Total Penerimaan</td>
                        <td style="padding: 0.75rem 1rem; text-align: right; font-weight: 600; color: #0d9488;">Rp {{ number_format($payroll->gaji_pokok_snapshot + $payroll->tunjangan_snapshot + $payroll->total_komisi + $payroll->total_lembur, 0, ',', '.') }}</td>
                    </tr>

                    <!-- Potongan -->
                    <tr>
                        <td colspan="2" style="padding: 1rem; background: #fff1f2; font-weight: 700; color: #be123c; border-bottom: 1px solid #e5e7eb; border-top: 1px solid #e5e7eb;">POTONGAN</td>
                    </tr>
                    <tr>
                        <td style="padding: 0.75rem 1rem 0.75rem 2rem; border-bottom: 1px solid #f3f4f6; color: #374151;">Potongan Absen (Alpha)</td>
                        <td style="padding: 0.75rem 1rem; text-align: right; border-bottom: 1px solid #f3f4f6; color: #374151;">Rp {{ number_format($payroll->potongan_absen, 0, ',', '.') }}</td>
                    </tr>
                    <tr style="background: #f9fafb;">
                        <td style="padding: 0.75rem 1rem; font-weight: 600; text-align: right; color: #374151;">Total Potongan</td>
                        <td style="padding: 0.75rem 1rem; text-align: right; font-weight: 600; color: #e11d48;">Rp {{ number_format($payroll->potongan_absen, 0, ',', '.') }}</td>
                    </tr>

                    <!-- TAKE HOME PAY -->
                    <tr>
                        <td style="padding: 1.5rem 1rem; font-weight: 800; font-size: 1.2rem; color: #111827; border-top: 2px solid #e5e7eb;">TAKE HOME PAY</td>
                        <td style="padding: 1.5rem 1rem; text-align: right; font-weight: 800; font-size: 1.5rem; color: #0d9488; border-top: 2px solid #e5e7eb;">Rp {{ number_format($payroll->gaji_bersih, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        @if($payroll->total_komisi > 0 && isset($rincianKomisi))
        <div style="border: 1px solid #e5e7eb; border-radius: 0.5rem; overflow: hidden; margin-bottom: 2rem;">
            <div style="background: #f9fafb; padding: 1rem; border-bottom: 1px solid #e5e7eb; font-weight: 700; color: #111827;">Rincian Komisi Servis</div>
            <table style="width: 100%; border-collapse: collapse;">
                <thead style="background: #ffffff; border-bottom: 2px solid #f3f4f6;">
                    <tr>
                        <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.85rem; color: #6b7280;">Tanggal & Nota</th>
                        <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.85rem; color: #6b7280;">Nama Jasa Servis</th>
                        <th style="padding: 0.75rem 1rem; text-align: right; font-size: 0.85rem; color: #6b7280;">Nominal Komisi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rincianKomisi as $komisi)
                    <tr>
                        <td style="padding: 0.75rem 1rem; border-bottom: 1px solid #f3f4f6; font-size: 0.9rem; color: #374151;">
                            <div style="font-weight: 600;">{{ \Carbon\Carbon::parse($komisi->tanggal)->format('d M Y') }}</div>
                            <div style="font-size: 0.8rem; color: #6b7280;">{{ $komisi->pesananJasa->pesanan->no_pesanan ?? '-' }}</div>
                        </td>
                        <td style="padding: 0.75rem 1rem; border-bottom: 1px solid #f3f4f6; font-size: 0.9rem; color: #374151;">
                            {{ $komisi->pesananJasa->jasa->nama_jasa ?? 'Servis' }}
                        </td>
                        <td style="padding: 0.75rem 1rem; border-bottom: 1px solid #f3f4f6; text-align: right; font-size: 0.9rem; color: #0d9488; font-weight: 600;">
                            Rp {{ number_format($komisi->nominal_komisi, 0, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <!-- Action Buttons -->
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; gap: 1rem;">
                <a href="{{ route('owner.payroll.index') }}" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Kembali</a>
                @if($payroll->status !== 'dibayar')
                <form action="{{ route('owner.payroll.destroy', $payroll->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus slip gaji ini secara permanen?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-outline" style="color: #ef4444; border-color: #ef4444;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 0.3rem;"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg> Hapus
                    </button>
                </form>
                @endif
            </div>
            
            <div style="display: flex; gap: 1rem;">
                @if($payroll->status == 'draft')
                    <form action="{{ route('owner.payroll.recalculate', $payroll->id) }}" method="POST" onsubmit="return confirm('Sistem akan menarik ulang data absensi dan komisi terbaru. Lanjutkan?');">
                        @csrf @method('PUT')
                        <button type="submit" class="btn btn-outline" style="color: #0d9488; border-color: #0d9488;"><i class="fas fa-sync-alt"></i> Hitung Ulang (Refresh)</button>
                    </form>
                    <form action="{{ route('owner.payroll.approve', $payroll->id) }}" method="POST" onsubmit="return confirm('Kunci/Approve slip gaji ini? Angka tidak bisa diubah lagi setelah ini.');">
                        @csrf @method('PUT')
                        <button type="submit" class="btn btn-warning" style="color: white;"><i class="fas fa-lock"></i> Kunci & Approve Slip</button>
                    </form>
                @elseif($payroll->status == 'final')
                    <form action="{{ route('owner.payroll.unapprove', $payroll->id) }}" method="POST" onsubmit="return confirm('Kembalikan ke status Draft?');">
                        @csrf @method('PUT')
                        <button type="submit" class="btn btn-outline" style="color: #4b5563;"><i class="fas fa-undo"></i> Batal Approve</button>
                    </form>
                    <form action="{{ route('owner.payroll.pay', $payroll->id) }}" method="POST" onsubmit="return confirm('Cairkan gaji ini? Sistem akan otomatis mencatat pengeluaran sebesar Rp {{ number_format($payroll->gaji_bersih, 0, ',', '.') }} di Arus Kas.');">
                        @csrf @method('PUT')
                        <button type="submit" class="btn btn-success"><i class="fas fa-money-bill-wave"></i> Bayar Sekarang</button>
                    </form>
                @elseif($payroll->status == 'dibayar')
                    <form action="{{ route('owner.payroll.unpay', $payroll->id) }}" method="POST" onsubmit="return confirm('Batal bayar gaji ini? Catatan pengeluaran di Arus Kas akan otomatis dihapus.');">
                        @csrf @method('PUT')
                        <button type="submit" class="btn btn-outline" style="color: #e11d48; border-color: #fca5a5;"><i class="fas fa-times-circle"></i> Batal Bayar</button>
                    </form>
                    <button class="btn" style="background: #dcfce3; color: #166534; cursor: default;" disabled><i class="fas fa-check-circle"></i> Selesai Dibayarkan</button>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
