<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PesananJasa extends Model
{
    use HasFactory;

    protected $table = 'pesanan_jasa';

    protected $fillable = [
        'pesanan_id',
        'jasa_id',
        'nama_snapshot',
        'harga_snapshot',
        'qty',
        'subtotal',
    ];

    protected $casts = [
        'harga_snapshot' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'qty' => 'integer',
    ];

    // ==================== Relationships ====================

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class);
    }

    public function jasa()
    {
        return $this->belongsTo(Jasa::class);
    }

    public function jasaKaryawan()
    {
        return $this->hasMany(JasaKaryawan::class);
    }

    public function komisiKaryawan()
    {
        return $this->hasMany(KomisiKaryawan::class);
    }
}
