<?php

namespace App\Imports;

use App\Models\Calendar;
use Carbon\Carbon;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithStartRow;

class CalendarImport implements OnEachRow, SkipsOnFailure, WithStartRow
{
    use SkipsFailures;

    /**
     * @param Row $row
     */
    public function onRow(Row $row)
    {
        Calendar::create([
            'date'    => Carbon::createFromFormat('Ymd', $row[0])->toDateString(),
            'holiday' => empty($row[2]) ? false : true,
            'note'    => $row[3],
        ]);

        return;
    }

    /**
     * @return int
     */
    public function startRow(): int
    {
        return 2;
    }
}