<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'no_telp',
        'alamat',
        'diskon_default',
    ];

    protected $casts = [
        'diskon_default' => 'decimal:2',
    ];

    // ==================== Relationships ====================

    public function pesanan()
    {
        return $this->hasMany(Pesanan::class);
    }
}
