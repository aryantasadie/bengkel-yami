<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jasa extends Model
{
    use HasFactory;

    protected $table = 'jasa';

    protected $fillable = [
        'nama_jasa',
        'kategori_jasa_id',
        'harga',
        'deskripsi',
        'is_active',
    ];

    public function kategori()
    {
        return $this->belongsTo(KategoriJasa::class, 'kategori_jasa_id');
    }

    protected $casts = [
        'harga' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // ==================== Relationships ====================

    /**
     * Spareparts yang biasanya digunakan untuk jasa ini.
     */
    public function spareparts()
    {
        return $this->belongsToMany(Sparepart::class, 'jasa_sparepart')
                    ->withPivot('qty_default')
                    ->withTimestamps();
    }

    /**
     * Semua pesanan jasa yang menggunakan jasa ini.
     */
    public function pesananJasa()
    {
        return $this->hasMany(PesananJasa::class);
    }
}
