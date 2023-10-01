<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class AccountStatus extends Enum
{
    const Unlocked = 0;
    const Locked   = 1;

    public static function getDescription($value): string
    {
        return match($value) {
            self::Unlocked => '稼働中',
            self::Locked   => '非稼働',
        };
    }

    public static function getData(): array
    {
        return [
            self::Unlocked => self::getDescription(self::Unlocked),
            self::Locked   => self::getDescription(self::Locked),
        ];
    }
}