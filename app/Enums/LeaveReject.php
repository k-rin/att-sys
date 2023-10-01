<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class LeaveReject extends Enum
{
    const SexLimit       = '性別不符合該假別';
    const DayLimit       = '該假別超過可請天數';
    const HireLimit      = '到職天數不符合該假別';
    const MonthLimit     = '該月份資料已結算';
    const UniqueLimit    = '該日期已提出申請';
    const ConditionLimit = '該日期不符合規定';
}