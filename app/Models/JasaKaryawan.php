<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JasaKaryawan extends Model
{
    use HasFactory;

    protected $table = 'jasa_karyawan';

    protected $fillable = [
        'pesanan_jasa_id',
        'karyawan_id',
        'status',
    ];

    // ==================== Relationships ====================

    public function pesananJasa()
    {
        return $this->belongsTo(PesananJasa::class);
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }
}
