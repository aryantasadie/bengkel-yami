<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    use HasFactory;

    protected $table = 'karyawan';

    protected $fillable = [
        'nama',
        'tanggal_lahir',
        'alamat',
        'tanggal_masuk',
        'jabatan',
        'gaji_pokok',
        'tunjangan',
        'is_active',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_masuk' => 'date',
        'gaji_pokok' => 'decimal:2',
        'tunjangan' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // ==================== Relationships ====================

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function absensi()
    {
        return $this->hasMany(Absensi::class);
    }

    public function jasaKaryawan()
    {
        return $this->hasMany(JasaKaryawan::class);
    }

    public function payroll()
    {
        return $this->hasMany(Payroll::class);
    }

    public function komisiKaryawan()
    {
        return $this->hasMany(KomisiKaryawan::class);
    }

    // ==================== Accessors ====================

    public function getGajiFormatAttribute(): string
    {
        return 'Rp ' . number_format($this->gaji_pokok, 0, ',', '.');
    }
}
