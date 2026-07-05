<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi';

    protected $fillable = [
        'no_nota',
        'pesanan_id',
        'tanggal',
        'total_harga',
        'diskon_persen',
        'diskon_nominal',
        'dp',
        'sisa_bayar',
        'total_bayar',
        'status_bayar',
        'metode_bayar',
    ];

    protected $casts = [
        'tanggal' => 'datetime',
        'total_harga' => 'decimal:2',
        'diskon_persen' => 'decimal:2',
        'diskon_nominal' => 'decimal:2',
        'dp' => 'decimal:2',
        'sisa_bayar' => 'decimal:2',
        'total_bayar' => 'decimal:2',
    ];

    // ==================== Relationships ====================

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class);
    }

    // ==================== Static Methods ====================

    /**
     * Generate nomor nota otomatis.
     * Format: INV-YYYYMMDD-XXXX
     */
    public static function generateNoNota(): string
    {
        $date = now()->format('Ymd');
        $prefix = 'INV-' . $date . '-';

        $lastNota = static::where('no_nota', 'like', $prefix . '%')
            ->orderBy('no_nota', 'desc')
            ->first();

        if ($lastNota) {
            $lastNumber = (int) substr($lastNota->no_nota, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}
