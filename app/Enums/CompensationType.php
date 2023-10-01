<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class CompensationType extends Enum
{
    const Cash    = 1;
    const Holiday = 2;

    public static function getDescription($value): string
    {
        return match($value) {
            self::Cash    => '加班費',
            self::Holiday => '補休',
        };
    }

    public static function getData(): array
    {
        return [
            self::Cash    => self::getDescription(self::Cash),
            self::Holiday => self::getDescription(self::Holiday),
        ];
    }
}