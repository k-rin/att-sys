<?php

namespace App\Imports;

use App\Models\ServiceRecord;
use App\Models\User;
use Carbon\Carbon;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithStartRow;

class ServiceRecordImport implements OnEachRow, SkipsOnFailure, WithStartRow
{
    use SkipsFailures;

    /**
     * @param Row $row
     */
    public function onRow(Row $row)
    {
        if ($user = User::where('alias', $row[2])->first()) {
            $time = Carbon::parse("{$row[6]} {$row[7]}");
            if ($record = ServiceRecord::where('user_id', $user->id)->where('date', $time->toDateString())->first()) {
                $startAt = Carbon::parse("{$record->date} {$record->start_at}");
                $endAt   = Carbon::parse("{$record->date} {$record->end_at}");
                if ($time < $startAt) {
                    $startAt = $time;
                } elseif ($time > $endAt) {
                    $endAt = $time;
                }
                $record->update([
                    'start_at' => $startAt,
                    'end_at'   => $endAt,
                ]);
            } else {
                ServiceRecord::create([
                    'user_id'  => $user->id,
                    'date'     => $time->toDateString(),
                    'start_at' => $time->toTimeString(),
                    'end_at'   => $time->toTimeString(),
                ]);
            }
        }

        return;
    }

    /**
     * @return int
     */
    public function startRow(): int
    {
        return 4;
    }
}