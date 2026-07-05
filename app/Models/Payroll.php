<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use HasFactory;

    protected $table = 'payroll';

    protected $fillable = [
        'karyawan_id',
        'periode_bulan',
        'periode_tahun',
        'gaji_pokok_snapshot',
        'tunjangan_snapshot',
        'total_komisi',
        'total_lembur',
        'potongan_absen',
        'potongan_lain',
        'gaji_bersih',
        'status',
        'tanggal_bayar',
    ];

    protected $casts = [
        'periode_bulan' => 'integer',
        'periode_tahun' => 'integer',
        'gaji_pokok_snapshot' => 'decimal:2',
        'tunjangan_snapshot' => 'decimal:2',
        'total_komisi' => 'decimal:2',
        'total_lembur' => 'decimal:2',
        'potongan_absen' => 'decimal:2',
        'potongan_lain' => 'decimal:2',
        'gaji_bersih' => 'decimal:2',
        'tanggal_bayar' => 'date',
    ];

    // ==================== Relationships ====================

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }
}
