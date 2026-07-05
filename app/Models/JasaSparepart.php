<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JasaSparepart extends Model
{
    use HasFactory;

    protected $table = 'jasa_sparepart';

    protected $fillable = [
        'jasa_id',
        'sparepart_id',
        'qty_default',
    ];

    protected $casts = [
        'qty_default' => 'integer',
    ];

    // ==================== Relationships ====================

    public function jasa()
    {
        return $this->belongsTo(Jasa::class);
    }

    public function sparepart()
    {
        return $this->belongsTo(Sparepart::class);
    }
}
