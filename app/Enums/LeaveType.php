<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class LeaveType extends Enum
{
    const PaidLeave          = 1;
    const SickLeave          = 2;
    const PersonalLeave      = 3;
    const MenstrualLeave     = 4;
    const BereavementLeave   = 5;
    const MedicalLeave       = 6;
    const MaternityLeave     = 7;
    const PrenatalCareLeave  = 8;
    const PaternityLeave     = 9;
    const PrenatalVisitLeave = 10;
    const FamilyCareLeave    = 11;
    const OfficialLeave      = 12;

    public static function getDescription($value): string
    {
        return match($value) {
            self::PaidLeave          => '特別休假',
            self::SickLeave          => '病假',
            self::PersonalLeave      => '事假',
            self::MenstrualLeave     => '生理假',
            self::BereavementLeave   => '喪假',
            self::MedicalLeave       => '公傷病假',
            self::MaternityLeave     => '產假',
            self::PrenatalCareLeave  => '安胎休養假',
            self::PaternityLeave     => '陪產假',
            self::PrenatalVisitLeave => '產檢假',
            self::FamilyCareLeave    => '家庭照顧假',
            self::OfficialLeave      => '公假',
        };
    }

    public static function getDayLimit($value): int
    {
        return match($value) {
            self::PaidLeave          => 0,
            self::SickLeave          => 30,
            self::PersonalLeave      => 14,
            self::MenstrualLeave     => 12,
            self::BereavementLeave   => 0,
            self::MedicalLeave       => 0,
            self::MaternityLeave     => 0,
            self::PrenatalCareLeave  => 30,
            self::PaternityLeave     => 7,
            self::PrenatalVisitLeave => 7,
            self::FamilyCareLeave    => 7,
            self::OfficialLeave      => 0,
        };
    }

    public static function getSexLimit($value): int
    {
        return match($value) {
            self::PaidLeave          => 0,
            self::SickLeave          => 0,
            self::PersonalLeave      => 0,
            self::MenstrualLeave     => UserSex::Female,
            self::BereavementLeave   => 0,
            self::MedicalLeave       => 0,
            self::MaternityLeave     => UserSex::Female,
            self::PrenatalCareLeave  => UserSex::Female,
            self::PaternityLeave     => UserSex::Male,
            self::PrenatalVisitLeave => UserSex::Female,
            self::FamilyCareLeave    => 0,
            self::OfficialLeave      => 0,
        };
    }

    public static function getData(): array
    {
        return [
            self::PaidLeave          => self::getDescription(self::PaidLeave),
            self::SickLeave          => self::getDescription(self::SickLeave),
            self::PersonalLeave      => self::getDescription(self::PersonalLeave),
            self::MenstrualLeave     => self::getDescription(self::MenstrualLeave),
            self::BereavementLeave   => self::getDescription(self::BereavementLeave),
            self::MedicalLeave       => self::getDescription(self::MedicalLeave),
            self::MaternityLeave     => self::getDescription(self::MaternityLeave),
            self::PrenatalCareLeave  => self::getDescription(self::PrenatalCareLeave),
            self::PaternityLeave     => self::getDescription(self::PaternityLeave),
            self::PrenatalVisitLeave => self::getDescription(self::PrenatalVisitLeave),
            self::FamilyCareLeave    => self::getDescription(self::FamilyCareLeave),
            self::OfficialLeave      => self::getDescription(self::OfficialLeave),
        ];
    }
}