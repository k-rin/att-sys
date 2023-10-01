<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class UserSex extends Enum
{
    const Female = 1;
    const Male   = 2;

    public static function getDescription($value): string
    {
        return match($value) {
            self::Female => '女性',
            self::Male   => '男性',
        };
    }

    public static function getData(): array
    {
        return [
            self::Female => self::getDescription(self::Female),
            self::Male   => self::getDescription(self::Male),
        ];
    }
}