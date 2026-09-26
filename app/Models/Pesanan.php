<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\StatusPesanan;

class Pesanan extends Model
{
    use HasFactory;

    protected $table = 'pesanan';

    protected $fillable = [
        'no_pesanan',
        'customer_id',
        'tanggal_masuk',
        'tanggal_selesai',
        'deskripsi_pekerjaan',
        'status',
        'catatan',
        'diskon_persen',
        'diskon_sparepart_persen',
        'dp'
    ];

    protected $casts = [
        'tanggal_masuk' => 'datetime',
        'tanggal_selesai' => 'datetime',
    ];

    // ==================== Relationships ====================

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function pesananJasa()
    {
        return $this->hasMany(PesananJasa::class);
    }

    public function transaksi()
    {
        return $this->hasOne(Transaksi::class);
    }

    public function pesananSparepart()
    {
        return $this->hasMany(PesananSparepart::class);
    }

    // ==================== Static Methods ====================

    /**
     * Generate nomor pesanan otomatis.
     * Format: PSN-YYYYMMDD-XXXX
     */
    public static function generateNoPesanan(): string
    {
        $date = now()->format('Ymd');
        $prefix = 'PSN-' . $date . '-';

        $lastPesanan = static::where('no_pesanan', 'like', $prefix . '%')
            ->orderBy('no_pesanan', 'desc')
            ->first();

        if ($lastPesanan) {
            $lastNumber = (int) substr($lastPesanan->no_pesanan, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}
