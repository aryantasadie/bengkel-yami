<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KomisiKaryawan extends Model
{
    use HasFactory;

    protected $table = 'komisi_karyawan';

    protected $fillable = [
        'karyawan_id',
        'pesanan_jasa_id',
        'nominal_komisi',
        'tanggal',
    ];

    protected $casts = [
        'nominal_komisi' => 'decimal:2',
        'tanggal' => 'date',
    ];

    // ==================== Relationships ====================

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function pesananJasa()
    {
        return $this->belongsTo(PesananJasa::class);
    }
}
