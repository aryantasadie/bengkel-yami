<?php

namespace App\Enums;

class StatusPesanan
{
    const ANTRIAN = 'antrian';
    const PROSES = 'proses';
    const SELESAI = 'selesai';
    const DIBATALKAN = 'dibatalkan';

    public static function all(): array
    {
        return [self::ANTRIAN, self::PROSES, self::SELESAI, self::DIBATALKAN];
    }
}
