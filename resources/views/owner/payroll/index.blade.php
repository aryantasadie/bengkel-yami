@extends('layouts.app')
@section('title', 'Data Penggajian (Payroll)')

@section('content')
<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h3 style="margin:0;">Data Penggajian (Payroll)</h3>
            <p style="margin: 0.5rem 0 0 0; color: #6b7280; font-size: 0.9rem;">Kelola slip gaji, komisi, dan persetujuan pencairan dana.</p>
        </div>
        <button onclick="openGenerateModal()" class="btn btn-primary" style="display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-magic"></i> Generate Payroll
        </button>
    </div>
    
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success" style="margin-bottom: 1.5rem;">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error" style="margin-bottom: 1.5rem; background: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 0.5rem; border: 1px solid #fca5a5;">{{ session('error') }}</div>
        @endif

        <!-- Filter Bar -->
        <form method="GET" style="background: #f9fafb; padding: 1.25rem; border-radius: 0.75rem; border: 1px solid #e5e7eb; margin-bottom: 2rem; display: flex; gap: 1rem; align-items: flex-end; flex-wrap: wrap;">
            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                <label style="font-weight: 600; font-size: 0.85rem; color: #374151;">Bulan</label>
                <select name="bulan" class="form-input" style="min-width: 150px;">
                    <option value="">Semua Bulan</option>
                    @for($i=1; $i<=12; $i++)
                        <option value="{{ $i }}" {{ request('bulan') == $i ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
            </div>
            
            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                <label style="font-weight: 600; font-size: 0.85rem; color: #374151;">Tahun</label>
                <select name="tahun" class="form-input" style="min-width: 150px;">
                    <option value="">Semua Tahun</option>
                    @foreach($periodes->pluck('tahun')->unique() as $thn)
                        <option value="{{ $thn }}" {{ request('tahun') == $thn ? 'selected' : '' }}>{{ $thn }}</option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                <label style="font-weight: 600; font-size: 0.85rem; color: #374151;">Status</label>
                <select name="status" class="form-input" style="min-width: 150px;">
                    <option value="">Semua Status</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft (Belum Dicek)</option>
                    <option value="final" {{ request('status') == 'final' ? 'selected' : '' }}>Final (Menunggu Pembayaran)</option>
                    <option value="dibayar" {{ request('status') == 'dibayar' ? 'selected' : '' }}>Dibayar (Lunas)</option>
                </select>
            </div>
            
            <button type="submit" class="btn btn-outline" style="height: 42px;">Filter Data</button>
            <a href="{{ route('owner.payroll.index') }}" class="btn" style="height: 42px; display:flex; align-items:center; background: #e5e7eb; color: #374151;">Reset</a>
        </form>

        <!-- Payroll Table -->
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Karyawan</th>
                        <th>Periode</th>
                        <th>Pendapatan</th>
                        <th>Potongan</th>
                        <th>Take Home Pay (Gaji Bersih)</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payrolls as $pr)
                    <tr>
                        <td style="font-weight: 600;">
                            {{ $pr->karyawan->nama ?? 'Tidak Diketahui' }}
                            <div style="font-size: 0.8rem; color: #6b7280; font-weight: normal;">{{ $pr->karyawan->jabatan ?? '-' }}</div>
                        </td>
                        <td>
                            <div style="font-weight: 600;">{{ \Carbon\Carbon::create()->month($pr->periode_bulan)->translatedFormat('F') }} {{ $pr->periode_tahun }}</div>
                        </td>
                        <td style="color: #0d9488; font-weight: 500;">
                            Rp {{ number_format($pr->gaji_pokok_snapshot + $pr->tunjangan_snapshot + $pr->total_komisi + $pr->total_lembur, 0, ',', '.') }}
                        </td>
                        <td style="color: #e11d48; font-weight: 500;">
                            Rp {{ number_format($pr->potongan_absen, 0, ',', '.') }}
                        </td>
                        <td style="font-size: 1.1rem; font-weight: 700; color: #1f2937;">
                            Rp {{ number_format($pr->gaji_bersih, 0, ',', '.') }}
                        </td>
                        <td>
                            @if($pr->status == 'draft')
                                <span style="background: #f3f4f6; color: #4b5563; padding: 0.3rem 0.6rem; border-radius: 999px; font-size: 0.8rem; font-weight: 600;">DRAFT</span>
                            @elseif($pr->status == 'final')
                                <span style="background: #fef3c7; color: #d97706; padding: 0.3rem 0.6rem; border-radius: 999px; font-size: 0.8rem; font-weight: 600;">FINAL</span>
                            @elseif($pr->status == 'dibayar')
                                <span style="background: #dcfce3; color: #166534; padding: 0.3rem 0.6rem; border-radius: 999px; font-size: 0.8rem; font-weight: 600;">DIBAYAR PADA {{ \Carbon\Carbon::parse($pr->tanggal_bayar)->format('d/m/Y') }}</span>
                            @endif
                        </td>
                        <td class="text-center table-actions" style="justify-content: center;">
                            <a href="{{ route('owner.payroll.show', $pr->id) }}" class="btn btn-outline" style="padding: 0.3rem 0.8rem; font-size: 0.85rem;">Lihat Slip</a>
                            @if($pr->status !== 'dibayar')
                            <form action="{{ route('owner.payroll.destroy', $pr->id) }}" method="POST" style="display: inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline" style="padding: 0.3rem 0.5rem; color: #ef4444; border-color: #ef4444; display: flex; align-items: center; justify-content: center;" onclick="return confirm('Apakah Anda yakin ingin menghapus payroll ini?')">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center" style="padding: 3rem; color: #6b7280;">
                            <i class="fas fa-file-invoice-dollar" style="font-size: 3rem; color: #d1d5db; margin-bottom: 1rem; display: block;"></i>
                            Belum ada data payroll. Silakan klik tombol "Generate Payroll" di atas.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">{{ $payrolls->links() }}</div>
    </div>
</div>

<script>
function openGenerateModal() {
    let currentMonth = new Date().getMonth() + 1;
    let currentYear = new Date().getFullYear();
    
    let monthOptions = '';
    const monthNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
    for(let i=1; i<=12; i++) {
        monthOptions += `<option value="${i}" ${i === currentMonth ? 'selected' : ''}>${monthNames[i-1]}</option>`;
    }
    
    let yearOptions = '';
    for(let i=currentYear-1; i<=currentYear+1; i++) {
        yearOptions += `<option value="${i}" ${i === currentYear ? 'selected' : ''}>${i}</option>`;
    }

    let karyawanCheckboxes = `
        <div style="margin-bottom: 0.5rem; display: flex; align-items: center; justify-content: space-between;">
            <label style="font-weight: 600;">Pilih Karyawan</label>
            <label style="font-size: 0.85rem; cursor: pointer; color: #0d9488; display: flex; align-items: center; gap: 0.3rem;">
                <input type="checkbox" id="selectAllKaryawan" checked onclick="toggleAllKaryawan(this)"> Pilih Semua
            </label>
        </div>
        <div style="max-height: 150px; overflow-y: auto; border: 1px solid #cbd5e1; border-radius: 0.25rem; padding: 0.5rem;">
            @foreach($karyawans as $k)
                <label style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.3rem; font-size: 0.9rem; cursor: pointer;">
                    <input type="checkbox" name="karyawan_ids[]" value="{{ $k->id }}" class="karyawan-checkbox" checked>
                    {{ $k->nama }} ({{ $k->jabatan }})
                </label>
            @endforeach
        </div>
    `;

    Swal.fire({
        title: 'Generate Payroll Otomatis',
        html: `
            <p style="text-align:left; font-size:0.9rem; color:#6b7280; margin-bottom:1.5rem;">Sistem akan mengkalkulasi Gaji Pokok, Tunjangan, Komisi Servis, dan Potongan Absen untuk karyawan terpilih.</p>
            <form id="generateForm" action="{{ route('owner.payroll.generate') }}" method="POST" style="text-align: left;">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div style="display: flex; gap: 1rem; margin-bottom: 1rem;">
                    <div style="flex: 1;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Bulan</label>
                        <select name="bulan" class="form-input" style="width: 100%; box-sizing: border-box;" required>
                            ${monthOptions}
                        </select>
                    </div>
                    <div style="flex: 1;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Tahun</label>
                        <select name="tahun" class="form-input" style="width: 100%; box-sizing: border-box;" required>
                            ${yearOptions}
                        </select>
                    </div>
                </div>
                ${karyawanCheckboxes}
            </form>
        `,
        showCancelButton: true,
        confirmButtonText: 'Kalkulasi Sekarang',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#0d9488',
        preConfirm: () => {
            let checked = document.querySelectorAll('.karyawan-checkbox:checked');
            if(checked.length === 0) {
                Swal.showValidationMessage('Pilih minimal 1 karyawan');
                return false;
            }
            document.getElementById('generateForm').submit();
        }
    });
}

function toggleAllKaryawan(source) {
    checkboxes = document.querySelectorAll('.karyawan-checkbox');
    for(var i=0, n=checkboxes.length;i<n;i++) {
        checkboxes[i].checked = source.checked;
    }
}
</script>
@endsection
