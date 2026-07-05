<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sparepart extends Model
{
    use HasFactory;

    protected $table = 'sparepart';

    protected $fillable = [
        'nama',
        'satuan',
        'harga_beli',
        'harga_jual',
        'stok',
        'stok_minimum',
    ];

    protected $casts = [
        'harga_beli' => 'decimal:2',
        'harga_jual' => 'decimal:2',
        'stok' => 'integer',
        'stok_minimum' => 'integer',
    ];

    // ==================== Relationships ====================

    /**
     * Jasa yang menggunakan sparepart ini.
     */
    public function jasa()
    {
        return $this->belongsToMany(Jasa::class, 'jasa_sparepart')
                    ->withPivot('qty_default')
                    ->withTimestamps();
    }

    // ==================== Methods ====================

    /**
     * Dapatkan harga beli berdasarkan batch FIFO tertua (HPP yang akan digunakan berikutnya).
     */
    public function getHargaBeliAttribute($value)
    {
        $oldestBatch = \Illuminate\Support\Facades\DB::table('sparepart_batches')
            ->where('sparepart_id', $this->id)
            ->where('qty_sisa', '>', 0)
            ->orderBy('tanggal_masuk', 'asc')
            ->orderBy('id', 'asc')
            ->first();

        if ($oldestBatch) {
            return $oldestBatch->harga_beli;
        }

        return $value;
    }

    /**
     * Cek apakah stok rendah (di bawah atau sama dengan minimum).
     */
    public function isLowStock(): bool
    {
        return $this->stok <= $this->stok_minimum;
    }
}
