<?php

namespace App\Enums;

class KategoriPengeluaran
{
    const OPERASIONAL = 'operasional';
    const RESTOCK_SPAREPART = 'restock_sparepart';
    const RESTOCK_LOGISTIK = 'restock_logistik';
    const GAJI = 'gaji';
    const LAINNYA = 'lainnya';

    public static function all(): array
    {
        return [self::OPERASIONAL, self::RESTOCK_SPAREPART, self::RESTOCK_LOGISTIK, self::GAJI, self::LAINNYA];
    }
}
