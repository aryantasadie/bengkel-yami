<?php

namespace App\Enums;

class UserRole
{
    const ADMIN = 'admin';
    const OWNER = 'owner';
    const KARYAWAN = 'karyawan';

    public static function all(): array
    {
        return [self::ADMIN, self::OWNER, self::KARYAWAN];
    }
}
