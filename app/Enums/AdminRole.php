<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class AdminRole extends Enum
{
    const Master   = 'Master';
    const Operator = 'Operator';
    const Readonly = 'Readonly';

    public static function getData(): array
    {
        return [
            self::Master,
            self::Operator,
            self::Readonly,
        ];
    }
}