<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class ApprovalLayer extends Enum
{
    const First  = 1;
    const Second = 2;
    const Third  = 3;
    const Fourth = 4;

    public static function getDescription($value): string
    {
        return match($value) {
            self::First  => '一次承認',
            self::Second => '二次承認',
            self::Third  => '三次承認',
            self::Fourth => '四次承認',
        };
    }
}