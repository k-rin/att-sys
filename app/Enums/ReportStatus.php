<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class ReportStatus extends Enum
{
    const Pending   = 0;
    const Permitted = 1;
    const Rejected  = 2;

    public static function getDescription($value): string
    {
        return match($value) {
            self::Pending   => '申請中',
            self::Permitted => '許可',
            self::Rejected  => '却下',
        };
    }

    public static function getData(): array
    {
        return [
            self::Pending   => self::getDescription(self::Pending),
            self::Permitted => self::getDescription(self::Permitted),
            self::Rejected  => self::getDescription(self::Rejected),
        ];
    }
}