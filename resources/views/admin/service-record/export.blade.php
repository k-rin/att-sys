<!-- View stored in resources/views/admin/service-record/export.blade.php -->
@php
    $year = request()->input('year');
    $month = request()->input('month');
    $birthday = \Carbon\Carbon::parse($user->birthday);
    $isBirthday = $birthday->month == $month;
@endphp

<h5>
    {{ $user->name }}（{{ $user->alias }}） {{ $year }} 年 {{ $month }} 月@if ($isBirthday)（誕生月）@endifの勤怠記録
</h5>
<h5>
    欠勤：{{ $summary->absence }}日；
    遅刻：{{ $summary->come_late }}分；
    早退：{{ $summary->leave_early }}分；
    残業：{{ $summary->over_time }}分（{{ $summary->over_time_unit }}単位）
    病假：<span @class(['text-danger' => $summary->sick_leave > 30])>{{ $summary->sick_leave }}</span>；
    安胎休養假：<span @class(['text-danger' => $summary->prenatal_care_leave > 30])>{{ $summary->prenatal_care_leave }}</span>
</h5>
<table>
    <thead>
        <tr>
            <th>日付</th>
            <th>曜日</th>
            <th>始業時間</th>
            <th>終業時間</th>
            <th>異常</th>
            <th>休暇</th>
        </tr>
    </thead>
    <tbody>
        @foreach($records as $record)
            <tr>
                <td>{{ $record->date }}</td>
                <td>{{ $record->week }}</td>
                @if (! empty($record->record_report) && $record->record_report->permitted)
                    <td>{{ Str::substr($record->record_report->start_at, 0, 5) }}</td>
                    <td>{{ Str::substr($record->record_report->end_at, 0, 5) }}</td>
                @else
                    <td>{{ Str::substr($record->start_at, 0, 5) }}</td>
                    <td>{{ Str::substr($record->end_at, 0, 5) }}</td>
                @endif
                <td>
                    {{ $record->abnormal }}
                </td>
                <td>
                    @if ($record->leave_report)
                        {{ \App\Enums\LeaveType::getDescription($record->leave_report->type) }}
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
