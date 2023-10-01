<!-- View stored in resources/views/admin/service-record/list.blade.php -->

@extends('admin.layouts.layout')
@section('content')
    @php
        $year = request()->input('year');
        $month = request()->input('month');
        $birthday = \Carbon\Carbon::parse($user->birthday);
        $isBirthday = ($birthday->month == $month);
    @endphp
    <div class="container">
        <div class="row">
            <div class="col">
                <h3 class="mt-3">{{ $user->name }}({{ $user->alias }}) 詳細</h3>
            </div>
        </div>
        @include('admin.common.navbar', [
            'page' => 'service-record',
            'id' => $user->id,
        ])
        <form method="GET" action="/admin/users/{{ $user->id }}/service-records">
            @csrf
            <div class="row g-3 align-items-center mb-3">
                <div class="col-md-2">
                    <div class="input-group">
                        <span class="input-group-text">年</span>
                        <input type="text" class="form-control" id="year" name="year" value="{{ $year }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="input-group">
                        <span class="input-group-text">月</span>
                        <select class="form-select" name="month" id="month">
                            @for ($i = 1; $i <= 12; $i ++)
                                <option value="{{ $i }}" @selected($i == $month)>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-dark">検索</button>
                    @can('isNotReadonly')
                        <button type="button" class="btn btn-outline-dark" id="download">ダウンロード</button>
                    @endcan
                </div>
                <div class="col-md-5 d-md-flex justify-content-md-end">
                    @can('isNotReadonly')
                        <button type="button" @class([
                            'rounded-pill' => true,
                            'btn' => true,
                            'btn-danger' => ! $closed,
                            'btn-outline-danger' => $closed,
                        ]) id="close">
                            締め@if($closed)済み@endif
                        </button>
                    @else
                        <span @class([
                            'rounded-pill' => true,
                            'btn' => true,
                            'btn-danger' => ! $closed,
                            'btn-outline-danger' => $closed,
                        ])>
                            締め@if($closed)済み@endif
                        </span>
                    @endcan
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
                <thead class="table-dark">
                    <tr>
                        <th>日付</th>
                        <th>曜日</th>
                        <th>出勤時間</th>
                        <th>退勤時間</th>
                        <th>異常</th>
                        <th>訂正申請</th>
                        <th>残業申請</th>
                        <th>振替出勤</th>
                        <th>振替休暇</th>
                        <th>休暇届</th>
                        @if (Auth::user()->can('isNotReadonly') && ! $closed)
                            <th></th>
                        @endif
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
                                        class="btn btn-outline-dark"
                                        data-bs-toggle="modal"
                                        data-bs-target="#formModal"
                                        data-bs-date="{{ $record->date }}"
                                        data-bs-type="serviceRecordReport">
                                        @if ($record->service_record_report->permitted)
                                            許可
                                        @elseif ($record->service_record_report->rejected)
                                            却下
                                        @else
                                            申請中
                                        @endif
                                    </button>
                                @endif
                            </td>
                            <td>
                                @if (! empty($record->overtime_report))
                                    <button
                                        type="button"
                                        class="btn btn-outline-dark"
                                        data-bs-toggle="modal"
                                        data-bs-target="#formModal"
                                        data-bs-report-id="{{ $record->overtime_report->id }}"
                                        data-bs-date="{{ $record->date }}"
                                        data-bs-type="overtimeReport">
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
                                        class="btn btn-outline-dark"
                                        data-bs-toggle="modal"
                                        data-bs-target="#formModal"
                                        data-bs-type="subAttendanceReport"
                                        data-bs-date="{{ $record->date }}">
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
                                        class="btn btn-outline-dark"
                                        data-bs-toggle="modal"
                                        data-bs-target="#formModal"
                                        data-bs-type="subHolidayReport"
                                        data-bs-date="{{ $record->date }}">
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
                                        class="btn btn-dark"
                                        data-bs-toggle="modal"
                                        data-bs-target="#formModal"
                                        data-bs-report-id="{{ $record->leave_report->id }}"
                                        data-bs-type="leaveReport">
                                        {{ \App\Enums\LeaveType::getDescription($record->leave_report->type) }}
                                    </button>
                                @endif
                            </td>
                            @if (Auth::user()->can('isNotReadonly') && ! $closed)
                                <td>
                                    @if (! $record->holiday)
                                        <button
                                            type="button"
                                            class="btn btn-dark"
                                            data-bs-toggle="modal"
                                            data-bs-target="#formModal"
                                            data-bs-date="{{ $record->date }}"
                                            data-bs-type="updateServiceRecord">
                                            更新
                                        </button>
                                    @endif
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="formModal" tabindex="-1" role="dialog" aria-labelledby="formModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="title"></h5>
                </div>
                <div class="modal-body">
                    <input id="id" hidden>
                    <input id="type" hidden>
                    <table class="table table-striped table-hover table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>カラム</th>
                                <th>內容</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="date-column">
                                <td>日付</td>
                                <td id="date"></td>
                            </tr>
                            <tr class="sub-attendance-report">
                                <td>振替出勤</td>
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
                                <td>開始日時</td>
                                <td><input type="text" id="start_datetime" name="start_datetime" value="" disabled></td>
                            </tr>
                            <tr class="datetime-column">
                                <td>終了日時</td>
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
                                        <button type="button" class="btn btn-dark" id="layer1_approve">承認</button>
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
                                        <button type="button" class="btn btn-dark" id="layer2_approve">承認</button>
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
                                        <button type="button" class="btn btn-dark" id="layer3_approve">承認</button>
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
                                        <button type="button" class="btn btn-dark" id="layer4_approve">承認</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-dark" id="update">更新</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">閉じる</button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.0/FileSaver.min.js" integrity="sha512-csNcFYJniKjJxRWRV1R7fvnXrycHP6qDR21mgz1ZP55xY5d+aHLfo9/FcGDQLfn2IfngbAHd8LdfsagcCqgTcQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        const userId = '{{ $user->id }}';
        const layer = '{{ Auth::user()->approval_layer }}';
        $('#formModal').on('show.bs.modal', (e) => {
            const date = e.relatedTarget.getAttribute('data-bs-date');
            const type = e.relatedTarget.getAttribute('data-bs-type');
            $('#type').val(type);
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
            $('#update').hide();
            for (let i = 1; i <= 4; i++) {
                $('#layer' + i + '_status option[selected]').removeAttr('selected');
                $('#layer' + i + '_status').attr('disabled', true);
                $('#layer' + i + '_approve').hide();
                $('.layer' + i + 'status').hide();
            }
            if (type == 'leaveReport') {
                const id = e.relatedTarget.getAttribute('data-bs-report-id');
                const url = '/admin/leave-reports/' + id;
                $('#id').val(id);
                $('.datetime-column').show();
                $('#title').html('休暇届詳細');
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
                        if (layer == key && ! response.data.closed) {
                            $('#layer' + key +'_status').attr('disabled', false);
                            $('#layer' + key +'_approve').show();
                            $('#note').attr('disabled', false);
                        }
                    });
                    */
                })
                .catch((error) => {
                    console.log(error);
                });
            } else if (type == 'serviceRecordReport') {
                const url = '/admin/users/' + userId + '/service-record-reports/' + date;
                $('.date-column').show();
                $('.time-column').show();
                $('.note').hide();
                $('#title').html(date + '打刻時間訂正申請');
                $('#start_time_title').html('始業時間');
                $('#end_time_title').html('終業時間');
                $('#date').html(date);
                axios
                .get(url)
                .then((response) => {
                    $('#start_time').val(response.data.start_at);
                    $('#end_time').val(response.data.end_at);
                    $('#reason').val(response.data.reason);
                    Object.keys(response.data.layer_status).forEach((key) => {
                        $("#layer" + key + "_status option[value='" + response.data.layer_status[key] + "']").attr('selected', true);
                        $('.layer' + key + 'status').show();
                        if (layer == key && ! response.data.closed) {
                            $('#layer' + key +'_status').attr('disabled', false);
                            $('#layer' + key +'_approve').show();
                        }
                    });
                })
                .catch((error) => {
                    console.log(error);
                });
            } else if (type == 'updateServiceRecord') {
                const url = '/admin/users/' + userId + '/service-records/' + date;
                $('.date-column').show();
                $('.time-column').show();
                $('.note').hide();
                $('#title').html(date + '打刻時間更新');
                $('#start_time_title').html('始業時間');
                $('#end_time_title').html('終業時間');
                $('#date').html(date);
                $('#start_time').attr('disabled', false);
                $('#end_time').attr('disabled', false);
                $('.reason').hide();
                $('#update').show();
                axios
                .get(url)
                .then((response) => {
                    $('#start_time').val(response.data.start_at);
                    $('#end_time').val(response.data.end_at);
                })
                .catch((error) => {
                    console.log(error);
                });
            } else if (type == 'overtimeReport') {
                const id = e.relatedTarget.getAttribute('data-bs-report-id');
                const url = '/admin/overtime-reports/' + id;
                $('#id').val(id);
                $('.date-column').show();
                $('.time-column').show();
                $('#title').html(date + '残業申請');
                $('#start_time_title').html('開始時間');
                $('#end_time_title').html('終了時間');
                $('#date').html(date);
                axios
                .get(url)
                .then((response) => {
                    $('#start_time').val(response.data.start_at);
                    $('#end_time').val(response.data.end_at);
                    $('#reason').val(response.data.reason);
                    $('#note').val(response.data.note);
                    /* Only permitted report show here
                    Object.keys(response.data.layer_status).forEach((key) => {
                        $("#layer" + key + "_status option[value='" + response.data.layer_status[key] + "']").attr('selected', true);
                        $('.layer' + key + 'status').show();
                        if (layer == key && ! response.data.closed) {
                            $('#layer' + key +'_status').attr('disabled', false);
                            $('#layer' + key +'_approve').show();
                            $('#note').attr('disabled', false);
                        }
                    });
                    */
                })
                .catch((error) => {
                    console.log(error);
                });
            } else if (type == 'subAttendanceReport') {
                const url = '/admin/users/' + userId + '/sub-attendance-reports/' + date;
                $('.date-column').show();
                $('.compensation').show();
                $('#compensation option[selected]').removeAttr('selected');
                $('#title').html('振替出勤申請');
                $('#date').html(date);
                axios
                .get(url)
                .then((response) => {
                    $('#reason').val(response.data.reason);
                    $("#compensation option[value='" + response.data.compensation + "']").attr('selected', true);
                    $('#note').val(response.data.note);
                    /* Only permitted report show here
                    Object.keys(response.data.layer_status).forEach((key) => {
                        $("#layer" + key + "_status option[value='" + response.data.layer_status[key] + "']").attr('selected', true);
                        $('.layer' + key + 'status').show();
                        if (layer == key && ! response.data.closed) {
                            $('#layer' + key +'_status').attr('disabled', false);
                            $('#layer' + key +'_approve').show();
                            $('#note').attr('disabled', false);
                        }
                    });
                    */
                })
                .catch((error) => {
                    console.log(error);
                });
            } else if (type == 'subHolidayReport') {
                const url = '/admin/users/' + userId + '/sub-holiday-reports/' + date;
                $('.date-column').show();
                $('.sub-attendance-report').show();
                $('.reason').hide();
                $('.note').hide();
                $('#title').html('振替休暇申請');
                $('#date').html(date);
                axios
                .get(url)
                .then((response) => {
                    $('#sub_attendance_report').html(response.data.sub_attendance_report.date);
                    /* Only permitted report show here
                    Object.keys(response.data.layer_status).forEach((key) => {
                        $("#layer" + key + "_status option[value='" + response.data.layer_status[key] + "']").attr('selected', true);
                        $('.layer' + key + 'status').show();
                        if (layer == key && ! response.data.closed) {
                            $('#layer' + key +'_status').attr('disabled', false);
                            $('#layer' + key +'_approve').show();
                        }
                    });
                    */
                })
                .catch((error) => {
                    console.log(error);
                });
            }
        });
        $("[id$='approve']").on('click', (e) => {
            var type = $('#type').val();
            var data = {
                'layer': layer,
                'status': $('#layer' + layer + '_status').val()
            };
            if (type == 'leaveReport') {
                var id = $('#id').val();
                var url = '/admin/leave-reports/' + id;
                data.note = $('#note').val();
            } else if (type == 'serviceRecordReport') {
                var date = $('#date').html();
                var url = '/admin/users/' + userId + '/service-record-reports/' + date;
            } else if (type == 'overtimeReport') {
                var id = $('#id').val();
                var url = '/admin/overtime-reports/' + id;
                data.note = $('#note').val();
            } else if (type == 'subAttendanceReport') {
                var date = $('#date').html();
                var url = '/admin/users/' + userId + '/sub-attendance-reports/' + date;
                data.note = $('#note').val();
            } else if (type == 'subHolidayReport') {
                var date = $('#date').html();
                var url = '/admin/users/' + userId + '/sub-holiday-reports/' + date;
            }
            axios
            .put(url, data)
            .catch((error) => {
                console.log(error);
            })
            .finally(() => {
                location.reload();
            });
        });
        $('#update').on('click', (e) => {
            const date = $('#date').html();
            const startAt = $('#start_time').val();
            const endAt = $('#end_time').val();
            const url = '/admin/users/' + userId + '/service-records/' + date;
            axios
            .put(url, {
                'start_at': startAt,
                'end_at': endAt
            })
            .catch((error) => {
                console.log(error);
            })
            .finally(() => {
                location.reload();
            });
        });
        $('#download').on('click', () => {
            const year = $('#year').val();
            const month = $('#month').val();
            const url = '/admin/users/' + userId + '/service-records/export';
            axios
            .get(url, {
                responseType: 'blob',
                params: {
                    'year': year,
                    'month': month
                }
            })
            .then((response) => {
                const mineType = response.headers["content-type"];
                const name = getFileName(response.headers["content-disposition"]);
                const blob = new Blob([response.data], { type: mineType });
                saveAs(blob, name);
            })
            .catch((error) => {
                console.log(error);
            });
        });
        function getFileName(contentDisposition) {
            let fileName = contentDisposition.substring(contentDisposition.indexOf("''") + 2, contentDisposition.length);

            return decodeURI(fileName).replace(/\+/g, " ");
        }
        $('#close').on('click', () => {
            const year = $('#year').val();
            const month = $('#month').val();
            const locked = '{{ $closed }}';
            const url = '/admin/users/' + userId + '/close-attendances';
            axios
            .put(url, {
                'year': year,
                'month': month,
                'locked': (locked == 0) ? 1 : 0
            })
            .catch((error) => {
                console.log(error);
            })
            .finally(() => {
                location.reload();
            });
        });
    </script>
@endsection