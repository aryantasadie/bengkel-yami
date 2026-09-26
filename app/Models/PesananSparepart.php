<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PesananSparepart extends Model
{
    use HasFactory;

    protected $table = 'pesanan_sparepart';

    protected $fillable = [
        'pesanan_id',
        'sparepart_id',
        'nama_snapshot',
        'qty',
        'harga_beli_snapshot',
        'harga_snapshot',
        'subtotal',
    ];

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class);
    }

    public function sparepart()
    {
        return $this->belongsTo(Sparepart::class);
    }
}
