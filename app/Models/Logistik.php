<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Logistik extends Model
{
    use HasFactory;

    protected $table = 'logistik';

    protected $fillable = [
        'nama',
        'satuan',
        'harga_beli',
        'stok',
        'stok_minimum',
    ];

    protected $casts = [
        'harga_beli' => 'decimal:2',
        'stok' => 'integer',
        'stok_minimum' => 'integer',
    ];

    // ==================== Methods ====================

    /**
     * Dapatkan harga beli berdasarkan batch FIFO tertua.
     */
    public function getHargaBeliAttribute($value)
    {
        $oldestBatch = \Illuminate\Support\Facades\DB::table('logistik_batches')
            ->where('logistik_id', $this->id)
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
