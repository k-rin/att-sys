<!-- View stored in resources/views/web/service-record/list.blade.php -->

@extends('web.layouts.layout')
@section('content')
    @php
        $year = request()->input('year');
        $month = request()->input('month');
        $birthday = \Carbon\Carbon::parse($user->birthday);
        $isBirthday = ($birthday->month == $month);
        $isManagerRoute = Request::is('departments/*');
        $action = $isManagerRoute
                ? "/departments/users/{$user->id}/service-records"
                : "/service-records";
    @endphp
    <div class="container">
        @if ($isManagerRoute)
            <div class="row">
                <div class="col">
                    <h3 class="mt-3">{{ $user->name }}（{{ $user->alias }}）詳細</h3>
                </div>
            </div>
            @include('web.common.navbar', [
                'page' => 'service-record',
                'id' => $user->id,
            ])
        @else
            <div class="row">
                <div class="col">
                    <h3 class="mt-3">{{ $user->name }}（{{ $user->alias }}）出勤紀錄</h3>
                </div>
            </div>
        @endif
        <form method="GET" action="{{ $action }}">
            @csrf
            <div class="row g-3 align-items-center mb-3">
                <div class="col-md-2">
                    <div class="input-group">
                        <span class="input-group-text" id="year">年份</span>
                        <input type="text" class="form-control" id="year" name="year" value="{{ $year }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="input-group">
                        <span class="input-group-text" id="month">月份</span>
                        <select class="form-select" id="month" name="month">
                            @for ($i = 1; $i <= 12; $i ++)
                                <option value="{{ $i }}" @selected($i == $month)>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-success">検索</button>
                </div>
                <div class="col-md-5 d-md-flex justify-content-md-end">
                    <span @class([
                        'rounded-pill' => true,
                        'btn' => true,
                        'btn-danger' => ! $closed,
                        'btn-outline-danger' => $closed,
                    ])>
                        @if($closed)已@else尚未@endif結算
                    </span>
                </div>
            </div>
        </form>
        <div style="height: 40px;">
            @if ($isBirthday)
                <img class="d-inline-block h-75 mb-1" src="/images/cake.png">
            @endif
            <span class="fs-5">
                欠勤：{{ $summary->absence }}日；
                遅刻：{{ $summary->come_late }}分；
                早退：{{ $summary->leave_early }}分；
                残業：{{ $summary->over_time }}分（{{ $summary->over_time_unit }}単位）
                病假：<span @class(['text-danger' => $summary->sick_leave > 30])>{{ $summary->sick_leave }}</span>；
                安胎休養假：<span @class(['text-danger' => $summary->prenatal_care_leave > 30])>{{ $summary->prenatal_care_leave }}</span>
            </span>
        </div>
        <div>
            <table class="table table-hover table-bordered">
                <thead class="table-success">
                    <tr>
                        <th>日付</th>
                        <th>曜日</th>
                        <th>上班時間</th>
                        <th>下班時間</th>
                        <th>異常</th>
                        <th>修正申請</th>
                        <th>加班申請</th>
                        <th>假日上班申請</th>
                        <th>補休申請</th>
                        <th>休暇届</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($records as $record)
                        <tr @class([
                            'table-secondary' => $record->holiday,
                            'table-default' => ! $record->holiday,
                        ])>
                            <td>{{ $record->date }}</td>
                            <td>{{ $record->week }}</td>
                            @if (empty($record->service_record_report))
                                <td>{{ Str::substr($record->start_at, 0, 5) }}</td>
                                <td>{{ Str::substr($record->end_at, 0, 5) }}</td>
                            @else
                                <td>
                                    <span @class([
                                        'text-decoration-line-through' => $record->service_record_report->permitted,
                                        'text-body' => ! $record->service_record_report->permitted,
                                    ])>
                                        {{ Str::substr($record->start_at, 0, 5) }}
                                    </span>
                                    &nbsp;
                                    <span @class([
                                        'text-primary' => $record->service_record_report->permitted,
                                        'text-muted' => ! $record->service_record_report->permitted,
                                    ])>
                                        {{ Str::substr($record->revised_start_at, 0, 5) }}
                                    </span>
                                </td>
                                <td>
                                    <span @class([
                                        'text-decoration-line-through' => $record->service_record_report->permitted,
                                        'text-body' => ! $record->service_record_report->permitted,
                                    ])>
                                        {{ Str::substr($record->end_at, 0, 5) }}
                                    </span>
                                    &nbsp;
                                    <span @class([
                                        'text-primary' => $record->service_record_report->permitted,
                                        'text-muted' => ! $record->service_record_report->permitted,
                                    ])>
                                        {{ Str::substr($record->revised_end_at, 0, 5) }}
                                    </span>
                                </td>
                            @endif
                            <td @class([
                                'text-danger' => $record->abnormal,
                                'text-default' => ! $record->abnormal,
                            ])>
                                {{ $record->abnormal }}
                            </td>
                            <td>
                                @if (! empty($record->service_record_report))
                                    <button
                                        type="button"
                                        class="btn btn-outline-success"
                                        data-bs-toggle="modal"
                                        data-bs-target="#formModal"
                                        data-bs-type="serviceRecordReport"
                                        data-bs-date="{{ $record->date }}">
                                        @if ($record->service_record_report->permitted)
                                            許可
                                        @elseif ($record->service_record_report->rejected)
                                            却下
                                        @else
                                            申請中
                                        @endif
                                    </button>
                                @elseif (! $isManagerRoute && ! $record->holiday && ! $closed)
                                    <button
                                        type="button"
                                        class="btn btn-success"
                                        data-bs-toggle="modal"
                                        data-bs-target="#formModal"
                                        data-bs-type="serviceRecordReport"
                                        data-bs-date="{{ $record->date }}">
                                        申請
                                    </button>
                                @endif
                            </td>
                            <td>
                                @if (! empty($record->overtime_report))
                                    <button
                                        type="button"
                                        class="btn btn-outline-success"
                                        data-bs-toggle="modal"
                                        data-bs-target="#formModal"
                                        data-bs-type="overtimeReport"
                                        data-bs-report-id="{{ $record->overtime_report->id }}"
                                        data-bs-date="{{ $record->date }}">
                                        @if ($record->overtime_report->permitted)
                                            許可
                                        @elseif ($record->overtime_report->rejected)
                                            却下
                                        @else
                                            申請中
                                        @endif
                                    </button>
                                @endif
                            </td>
                            <td>
                                @if (! empty($record->sub_attendance_report))
                                    <button
                                        type="button"
                                        class="btn btn-outline-success"
                                        data-bs-toggle="modal"
                                        data-bs-target="#formModal"
                                        data-bs-type="subAttendanceReport"
                                        data-bs-report-id="{{ $record->sub_attendance_report->id }}">
                                        @if ($record->sub_attendance_report->permitted)
                                            許可
                                        @elseif ($record->sub_attendance_report->rejected)
                                            却下
                                        @else
                                            申請中
                                        @endif
                                    </button>
                                @endif
                            </td>
                            <td>
                                @if (! empty($record->sub_holiday_report))
                                    <button
                                        type="button"
                                        class="btn btn-outline-success"
                                        data-bs-toggle="modal"
                                        data-bs-target="#formModal"
                                        data-bs-type="subHolidayReport"
                                        data-bs-report-id="{{ $record->sub_holiday_report->id }}">
                                        @if ($record->sub_holiday_report->permitted)
                                            許可
                                        @elseif ($record->sub_holiday_report->rejected)
                                            却下
                                        @else
                                            申請中
                                        @endif
                                    </button>
                                @endif
                            </td>
                            <td>
                                @if (! empty($record->leave_report))
                                    <button
                                        type="button"
                                        class="btn btn-success"
                                        data-bs-toggle="modal"
                                        data-bs-target="#formModal"
                                        data-bs-type="leaveReport"
                                        data-bs-report-id="{{ $record->leave_report->id }}">
                                        {{ \App\Enums\LeaveType::getDescription($record->leave_report->type) }}
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <!-- Form Modal -->
    <div class="modal fade" id="formModal" tabindex="-1" role="dialog" aria-labelledby="formReportModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="title"></h5>
                </div>
                <div class="modal-body">
                    <input id="id" hidden>
                    <input id="type" hidden>
                    <table class="table table-striped table-hover table-bordered">
                        <thead class="table-success">
                            <tr>
                                <th>欄位</th>
                                <th>內容</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="date-column">
                                <td id="date_title"></td>
                                <td id="date"></td>
                            </tr>
                            <tr class="sub-attendance-report">
                                <td>假日上班日期</td>
                                <td id="sub_attendance_report"></td>
                            </tr>
                            <tr class="time-column">
                                <td id="start_time_title"></td>
                                <td><input type="time" id="start_time" name="start_time" value=""></td>
                            </tr>
                            <tr class="time-column">
                                <td id="end_time_title"></td>
                                <td><input type="time" id="end_time" name="end_time" value=""></td>
                            </tr>
                            <tr class="datetime-column">
                                <td>開始時間</td>
                                <td><input type="text" id="start_datetime" name="start_datetime" value="" disabled></td>
                            </tr>
                            <tr class="datetime-column">
                                <td>結束時間</td>
                                <td><input type="text" id="end_datetime" name="end_datetime" value="" disabled></td>
                            </tr>
                            <tr class="datetime-column">
                                <td>種類</td>
                                <td>
                                    <select class="form-select" name="type" id="type" disabled>
                                        @foreach (\App\Enums\LeaveType::getData() as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                            <tr class="reason">
                                <td>理由</td>
                                <td><input type="text" id="reason" name="reason" value=""></td>
                            </tr>
                            <tr class="compensation">
                                <td>補填</td>
                                <td>
                                    <select class="form-select" name="compensation" id="compensation" disabled>
                                        @foreach (\App\Enums\CompensationType::getData() as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                            <tr class="note">
                                <td>備考</td>
                                <td><input type="text" id="note" name="note" value=""></td>
                            </tr>
                            <tr class="layer1status">
                                <td>一次承認</td>
                                <td>
                                    <div class="input-group mb-3">
                                        <select class="form-select" name="layer1_status" id="layer1_status" style="width: auto;">
                                            @foreach (\App\Enums\ReportStatus::getData() as $key => $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                            @endforeach
                                        </select>
                                        <button type="button" class="btn btn-success" id="layer1_approve">承認</button>
                                    </div>
                                </td>
                            </tr>
                            <tr class="layer2status">
                                <td>二次承認</td>
                                <td>
                                    <div class="input-group mb-3">
                                        <select class="form-select" name="layer2_status" id="layer2_status" style="width: auto;">
                                            @foreach (\App\Enums\ReportStatus::getData() as $key => $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                            @endforeach
                                        </select>
                                        <button type="button" class="btn btn-success" id="layer2_approve">承認</button>
                                    </div>
                                </td>
                            </tr>
                            <tr class="layer3status">
                                <td>三次承認</td>
                                <td>
                                    <div class="input-group mb-3">
                                        <select class="form-select" name="layer3_status" id="layer3_status" style="width: auto;">
                                            @foreach (\App\Enums\ReportStatus::getData() as $key => $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                            @endforeach
                                        </select>
                                        <button type="button" class="btn btn-success" id="layer3_approve">承認</button>
                                    </div>
                                </td>
                            </tr>
                            <tr class="layer4status">
                                <td>四次承認</td>
                                <td>
                                    <div class="input-group mb-3">
                                        <select class="form-select" name="layer4_status" id="layer4_status" style="width: auto;">
                                            @foreach (\App\Enums\ReportStatus::getData() as $key => $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                            @endforeach
                                        </select>
                                        <button type="button" class="btn btn-success" id="layer4_approve">承認</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="apply">申請</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">閉じる</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Search Modal -->
    <div class="modal fade" id="searchModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>選擇假日上班申請</h5>
                </div>
                <div class="modal-body">
                    <input id="sub_holiday_date" value="" hidden>
                    <p class="fs-6 fw-lighter">僅顯示過去三個月內被許可的假日上班申請</p>
                    <div class="row g-3 align-items-center mb-3">
                        <div>
                            <table class="table table-striped table-hover table-bordered">
                                <thead class="table-success">
                                    <tr>
                                        <th>id</th>
                                        <th>日期</th>
                                        <th>理由</th>
                                        <th>備考</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="search-result">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">閉じる</button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        const userId = '{{ $user->id }}';
        const isManagerRoute = '{{ $isManagerRoute }}';
        $('#formModal').on('show.bs.modal', (e) => {
            const date = e.relatedTarget.getAttribute('data-bs-date');
            const type = e.relatedTarget.getAttribute('data-bs-type');
            $('#type').val(type);
            for (let i = 1; i <= 4; i++) {
                $('#layer' + i + '_status option[selected]').removeAttr('selected');
                $('#layer' + i + '_status').attr('disabled', true);
                $('#layer' + i + '_approve').hide();
                $('.layer' + i + 'status').hide();
            }
            $('#date_title').html('日期');
            $('#start_time').attr('disabled', true);
            $('#end_time').attr('disabled', true);
            $('#start_datetime').attr('disabled', true);
            $('#end_datetime').attr('disabled', true);
            $('#reason').attr('disabled', true);
            $('#compensation').attr('disabled', true);
            $('#note').attr('disabled', true);
            $('.date-column').hide();
            $('.time-column').hide();
            $('.datetime-column').hide();
            $('.sub-attendance-report').hide();
            $('.reason').show();
            $('.compensation').hide();
            $('.note').show();
            $('#apply').hide();
            if (type == 'serviceRecordReport') {
                const url = isManagerRoute
                    ? '/departments/users/' + userId + '/service-record-reports/' + date
                    : '/service-record-reports/' + date;
                $('.date-column').show();
                $('.time-column').show();
                $('.note').hide();
                $('#title').html(date + ' 打卡時間訂正申請');
                $('#date').html(date);
                $('#start_time_title').html('上班時間');
                $('#end_time_title').html('下班時間');
                axios
                .get(url)
                .then((response) => {
                    if (Object.keys(response.data).length) {
                        $('#start_time').val(response.data.start_at.slice(0, 5));
                        $('#end_time').val(response.data.end_at.slice(0, 5));
                        $('#reason').val(response.data.reason);
                        Object.keys(response.data.layer_status).forEach((key) => {
                            $("#layer" + key + "_status option[value='" + response.data.layer_status[key] + "']").attr('selected', true);
                            $('.layer' + key + 'status').show();
                        });
                        if (! response.data.closed) {
                            if (isManagerRoute) {
                                $('#layer1_status').attr('disabled', false);
                                $('#layer1_approve').show();
                            } else {
                                if (response.data.editable) {
                                    $('#start_time').attr('disabled', false);
                                    $('#end_time').attr('disabled', false);
                                    $('#reason').attr('disabled', false);
                                    $('#apply').show();
                                }
                            }
                        }
                    } else {
                        $('#start_time').val('');
                        $('#end_time').val('');
                        $('#reason').val('');
                        $('#start_time').attr('disabled', false);
                        $('#end_time').attr('disabled', false);
                        $('#reason').attr('disabled', false);
                        $("#layer1_status option[value='0']").attr('selected', true);
                        $("#layer2_status option[value='0']").attr('selected', true);
                        $('#apply').show();
                    }
                })
                .catch((error) => {
                    console.log(error);
                });
            } else if (type == 'leaveReport') {
                const id = e.relatedTarget.getAttribute('data-bs-report-id');
                const url = isManagerRoute
                    ? '/departments/leave-reports/' + id
                    : '/leave-reports/' + id;
                $('#id').val(id);
                $('.datetime-column').show();
                $('#title').html('假單詳細');
                $('#type option[selected]').removeAttr('selected');
                axios
                .get(url)
                .then((response) => {
                    $('#start_datetime').val(response.data.start_at.slice(0, 16));
                    $('#end_datetime').val(response.data.end_at.slice(0, 16));
                    $('#reason').val(response.data.reason);
                    $('#note').val(response.data.note);
                    $("#type option[value='" + response.data.type + "']").attr('selected', true);
                    /* Only permitted report show here
                    Object.keys(response.data.layer_status).forEach((key) => {
                        $("#layer" + key + "_status option[value='" + response.data.layer_status[key] + "']").attr('selected', true);
                        $('.layer' + key + 'status').show();
                    });
                    if (isManagerRoute && ! response.data.closed) {
                        $('#layer1_status').attr('disabled', false);
                        $('#note').attr('disabled', false);
                        $('#layer1_approve').show();
                    }
                    */
                })
                .catch((error) => {
                    console.log(error);
                });
            } else if (type == 'overtimeReport') {
                const id = e.relatedTarget.getAttribute('data-bs-report-id');
                const url = isManagerRoute
                    ? '/departments/overtime-reports/' + id
                    : '/overtime-reports/' + id;
                $('#id').val(id);
                $('.date-column').show();
                $('.time-column').show();
                $('#title').html('加班申請');
                $('#date').html(date);
                $('#start_time_title').html('開始時間');
                $('#end_time_title').html('結束時間');
                axios
                .get(url)
                .then((response) => {
                    $('#start_time').val(response.data.start_at.slice(0, 5));
                    $('#end_time').val(response.data.end_at.slice(0, 5));
                    $('#reason').val(response.data.reason);
                    $('#note').val(response.data.note);
                    /* Only permitted report show here
                    Object.keys(response.data.layer_status).forEach((key) => {
                        $("#layer" + key + "_status option[value='" + response.data.layer_status[key] + "']").attr('selected', true);
                        $('.layer' + key + 'status').show();
                    });
                    if (isManagerRoute && ! response.data.closed) {
                        $('#layer1_status').attr('disabled', false);
                        $('#note').attr('disabled', false);
                        $('#layer1_approve').show();
                    }
                    */
                })
                .catch((error) => {
                    console.log(error);
                });
            } else if (type == 'subAttendanceReport') {
                const id = e.relatedTarget.getAttribute('data-bs-report-id');
                const url = isManagerRoute
                    ? '/departments/sub-attendance-reports/' + id
                    : '/sub-attendance-reports/' + id;
                $('#id').val(id);
                $('.date-column').show();
                $('.compensation').show();
                $('#compensation option[selected]').removeAttr('selected');
                $('#title').html('假日上班申請');
                axios
                .get(url)
                .then((response) => {
                    $('#date').html(response.data.date);
                    $('#reason').val(response.data.reason);
                    $("#compensation option[value='" + response.data.compensation + "']").attr('selected', true);
                    $('#note').val(response.data.note);
                    /* Only permitted report show here
                    Object.keys(response.data.layer_status).forEach((key) => {
                        $("#layer" + key + "_status option[value='" + response.data.layer_status[key] + "']").attr('selected', true);
                        $('.layer' + key + 'status').show();
                    });
                    if (isManagerRoute && ! response.data.closed) {
                        $('#layer1_status').attr('disabled', false);
                        $('#note').attr('disabled', false);
                        $('#layer1_approve').show();
                    }
                    */
                })
                .catch((error) => {
                    console.log(error);
                });
            } else if (type == 'subHolidayReport') {
                const id = e.relatedTarget.getAttribute('data-bs-report-id');
                const url = isManagerRoute
                    ? '/departments/users/' + userId + '/sub-holiday-reports/' + id
                    : '/sub-holiday-reports/' + id;
                $('.date-column').show();
                $('.sub-attendance-report').show();
                $('.time-column').hide();
                $('.datetime-column').hide();
                $('.reason').hide();
                $('.note').hide();
                $('#title').html('補假申請');
                $('#date_title').html('補假日期');
                axios
                .get(url)
                .then((response) => {
                    $('#date').html(response.data.date);
                    $('#sub_attendance_report').html(response.data.sub_attendance_report.date);
                    /* Only permitted report show here
                    Object.keys(response.data.layer_status).forEach((key) => {
                        $("#layer" + key + "_status option[value='" + response.data.layer_status[key] + "']").attr('selected', true);
                        $('.layer' + key + 'status').show();
                    });
                    if (isManagerRoute && ! response.data.closed) {
                        $('#layer1_status').attr('disabled', false);
                        $('#layer1_approve').show();
                    }
                    */
                })
                .catch((error) => {
                    console.log(error);
                });
            }
        });
        $('#searchModal').on('show.bs.modal', (e) => {
            const date = e.relatedTarget.getAttribute('data-bs-date');
            const url = '/sub-attendance-reports/uncompensated';
            let result = '';
            $('#sub_holiday_date').val(date);
            axios
            .get(url)
            .then((response) => {
                response.data.forEach((element) => {
                    result += '<tr><th>' + element.id + '</th><td>' + element.date + '</td><td>' + element.reason + '</td><td>' + element.note + '</td><td><button type="button" class="btn btn-success" onClick="pickUp(\'' + element.id + '\')">選擇</button></td></tr>';
                });
            })
            .catch((error) => {
                console.log(error);
            })
            .finally(() => {
                $('#search-result').html(result);
            });
        });
        $('#apply').on('click', (e) => {
            const type = $('#type').val();
            const date = $('#date').html();
            const reason = $('#reason').val();
            var data = {};
            if (type == 'serviceRecordReport') {
                var url = '/service-record-reports/' + date;
                const startAt = $('#start_time').val();
                const endAt = $('#end_time').val();
                if (startAt == '') {
                    alert('請輸入上班時間。');
                    return false;
                }
                if (endAt == '') {
                    alert('請輸入下班時間。');
                    return false;
                }
                if (startAt >= endAt) {
                    alert('請輸入正確的時間。');
                    return false;
                }
                data.start_at = startAt;
                data.end_at = endAt;
            } else if (type == 'subAttendanceReport') {
                var url = '/sub-attendance-reports/' + date;
                const compensation = $('#compensation').val();
                data.compensation = compensation;
            }
            if (reason == '') {
                alert('請輸入理由。');
                return false;
            }
            data.reason = reason;
            axios
            .put(url, data)
            .catch((error) => {
                console.log(error);
            })
            .finally(() => {
                location.reload();
            });
        });
        $("[id$='approve']").on('click', (e) => {
            var type = $('#type').val();
            var data = {};
            if (type == 'leaveReport') {
                var id = $('#id').val();
                var url = '/departments/leave-reports/' + id;
                data.note = $('#note').val();
            } else if (type == 'serviceRecordReport') {
                var date = $('#date').html();
                var url = '/departments/users/' + userId + '/service-record-reports/' + date;
            } else if (type == 'overtimeReport') {
                var id = $('#id').val();
                var url = '/departments/overtime-reports/' + id;
                data.note = $('#note').val();
            } else if (type == 'subAttendanceReport') {
                var date = $('#date').html();
                var url = '/departments/users/' + userId + '/sub-attendance-reports/' + date;
                data.note = $('#note').val();
            } else if (type == 'subHolidayReport') {
                var date = $('#date').html();
                var url = '/departments/users/' + userId + '/sub-holiday-reports/' + date;
            }
            data.status = $('#layer1_status').val();
            axios
            .put(url, data)
            .catch((error) => {
                console.log(error);
            })
            .finally(() => {
                location.reload();
            });
        });
        function pickUp(id) {
            const date = $('#sub_holiday_date').val();
            const url = '/sub-holiday-reports/' + date;
            axios
            .put(url, {
                'sub_attendance_report_id': id
            })
            .catch((error) => {
                console.log(error);
            })
            .finally(() => {
                $('#searchModal').modal('hide');
                location.reload();
            });
        };
    </script>
@endsection