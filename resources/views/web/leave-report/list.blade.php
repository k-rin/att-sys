<!-- View stored in resources/views/web/leave-report/list.blade.php -->

@extends('web.layouts.layout')
@section('content')
    @php
        $isManagerRoute = Request::is('departments/*');
        $action = $isManagerRoute
                ? isset($user)
                    ? "/departments/users/{$user->id}/leave-reports"
                    : '/departments/leave-reports'
                : '/leave-reports';
    @endphp
    <div class="container">
        @if ($isManagerRoute)
            @if (isset($user))
                <div class="row">
                    <div class="col">
                        <h3 class="mt-3">{{ $user->name }}（{{ $user->alias }}）詳細</h3>
                    </div>
                </div>
                @include('web.common.navbar', [
                    'page' => 'leave-report',
                    'id' => $user->id,
                ])
            @else
            <div class="row">
                    <div class="col">
                        <h3 class="mt-3">{{ Auth::user()->department->name }} 假單列表</h3>
                    </div>
                </div>
            @endif
        @else
            <div class="row">
                <div class="col">
                    <h3 class="mt-3">{{ $user->name }}（{{ $user->alias }}）假單列表</h3>
                </div>
            </div>
        @endif
        <form method="GET" action="{{ $action }}">
            @csrf
            <div class="row g-3 align-items-center mb-3">
                @if (empty($user))
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-text" id="name">姓名</span>
                            <input type="text" class="form-control" id="name" name="name" value="{{ request()->input('name') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-text" id="alias">英文名稱</span>
                            <input type="text" class="form-control" id="alias" name="alias" value="{{ request()->input('alias') }}">
                        </div>
                    </div>
                @endif
                <div class="col-md-2">
                    <div class="input-group">
                        <span class="input-group-text" id="year">年份</span>
                        <input type="text" class="form-control" id="year" name="year" value="{{ request()->input('year') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="input-group">
                        <span class="input-group-text" id="month">月份</span>
                        <select class="form-select" name="month" id="month">
                            @for ($i = 0; $i <= 12; $i ++)
                                <option value="{{ $i }}" @selected($i == request()->input('month'))>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-success">搜尋</button>
                </div>
            </div>
        </form>
        <table class="table table-striped table-hover table-bordered">
            <thead class="table-success">
                <tr>
                    <th>id</th>
                    @if (empty($user))
                        <th>姓名</th>
                        <th>英文名稱</th>
                    @endif
                    <th>開始</th>
                    <th>結束</th>
                    <th>種類</th>
                    <th>承認状態</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($reports as $report)
                    <tr>
                        <th>{{ $report->id }}</th>
                        @if (empty($user))
                            <td>{{ $report->user->name }}</td>
                            <td>{{ $report->user->alias }}</td>
                        @endif
                        <td>{{ Str::substr($report->start_at, 0, 16) }}</td>
                        <td>{{ Str::substr($report->end_at, 0, 16) }}</td>
                        <td>{{ \App\Enums\LeaveType::getDescription($report->type) }}</td>
                        <td>
                            @if ($report->permitted)
                                許可
                            @elseif ($report->rejected)
                                却下
                            @else
                                申請中
                            @endif
                        </td>
                        <td>
                            <button
                                type="button"
                                class="btn btn-success"
                                data-bs-toggle="modal"
                                data-bs-target="#formModal"
                                data-bs-report-id="{{ $report->id }}">
                                詳細
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="row">
            {{ $reports->appends(request()->input())->links() }}
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="formModal" tabindex="-1" role="dialog" aria-labelledby="formModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>假單詳細</h5>
                </div>
                <div class="modal-body" id="modal-body">
                    <input id="id" hidden>
                    <table class="table table-striped table-hover table-bordered">
                        <thead class="table-success">
                            <tr>
                                <th>欄位</th>
                                <th>內容</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>開始日期</td>
                                <td><input type="date" class="form-control" name="start_date" id="start_date"></td>
                            </tr>
                            <tr>
                                <td>開始時間</td>
                                <td>
                                    <select class="form-select" name="start_time" id="start_time">
                                        <option id="am_start_at" value=""></option>
                                        <option id="pm_start_at" value=""></option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>結束日期</td>
                                <td><input type="date" class="form-control" name="end_date" id="end_date"></td>
                            </tr>
                            <tr>
                                <td>結束時間</td>
                                <td>
                                    <select class="form-select" name="end_time" id="end_time">
                                        <option id="am_end_at" value=""></option>
                                        <option id="pm_end_at" value=""></option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>天數</td>
                                <td><input type="text" class="form-control" name="days" id="days" disabled></td>
                            </tr>
                            <tr>
                                <td>種類</td>
                                <td>
                                    <select class="form-select" name="type" id="type">
                                        @foreach (\App\Enums\LeaveType::getData() as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>理由</td>
                                <td><input type="text" class="form-control" name="reason" id="reason"></td>
                            </tr>
                            <tr>
                                <td>備考</td>
                                <td><input type="text" class="form-control" name="note" id="note"></td>
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
                                        <button type="submit" class="btn btn-dark" id="approve">承認</button>
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
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="apply">申請</button>
                    <button type="button" class="btn btn-secondary" id="close" data-bs-dismiss="modal">關閉</button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        $('#formModal').on('show.bs.modal', (e) => {
            const isManagerRoute = '{{ $isManagerRoute }}';
            const id = e.relatedTarget.getAttribute('data-bs-report-id');
            const url = isManagerRoute
                ? '/departments/leave-reports/' + id
                : '/leave-reports/' + id;
            $('#id').val(id);
            $('#start_date').attr('disabled', false);
            $('#start_time').attr('disabled', false);
            $('#end_date').attr('disabled', false);
            $('#end_time').attr('disabled', false);
            $('#type').attr('disabled', false);
            $('#reason').attr('disabled', false);
            $('#note').attr('disabled', true);
            $('#approve').hide();
            $('#apply').show();
            $('#start_time option[selected]').removeAttr('selected');
            $('#end_time option[selected]').removeAttr('selected');
            for (let i = 1; i <= 4; i++) {
                $('#layer' + i +'_status option[selected]').removeAttr('selected');
                $('#layer' + i +'_status').attr('disabled', true);
                $('.layer' + i + 'status').hide();
            }
            axios
            .get(url)
            .then((response) => {
                $('#start_date').val(response.data.start_at.slice(0, 10));
                $('#end_date').val(response.data.end_at.slice(0, 10));
                $('#days').val(response.data.days);
                $('#reason').val(response.data.reason);
                $('#note').val(response.data.note);
                $("#type option[value='" + response.data.type + "']").attr('selected', true);
                Object.keys(response.data.layer_status).forEach((key) => {
                    $("#layer" + key + "_status option[value='" + response.data.layer_status[key] + "']").attr('selected', true);
                    $('.layer' + key + 'status').show();
                });
                if (isManagerRoute || ! response.data.editable || response.data.closed) {
                    $('#start_date').attr('disabled', true);
                    $('#start_time').attr('disabled', true);
                    $('#end_date').attr('disabled', true);
                    $('#end_time').attr('disabled', true);
                    $('#type').attr('disabled', true);
                    $('#reason').attr('disabled', true);
                    $('#apply').hide();
                }
                if (isManagerRoute && ! response.data.closed) {
                    $('#note').attr('disabled', false);
                    $('#layer1_status').attr('disabled', false);
                    $('#approve').show();
                }
                setStartAt(response.data.start_at.slice(0, 10))
                .then(() => {
                    $("#start_time option[value='" + response.data.start_at.slice(11, 19) + "']").attr('selected', true);
                });
                setEndAt(response.data.end_at.slice(0, 10))
                .then(() => {
                    $("#end_time option[value='" + response.data.end_at.slice(11, 19) + "']").attr('selected', true);
                });
            })
            .catch((error) => {
                console.log(error);
            });
        });
        $('#start_date').change(() => {
            setStartAt($('#start_date').val());
        });
        $('#end_date').change(() => {
            setEndAt($('#end_date').val());
        });
        function setStartAt (date) {
            return axios
                .get('/attendance-times/' + date)
                .then((response) => {
                    const startTime = response.data.start_time;
                    const hour = parseInt(startTime.slice(0 , 2)) + 5;
                    $('#am_start_at').val(startTime);
                    $('#am_start_at').html(startTime.slice(0, 5));
                    $('#pm_start_at').val(hour + startTime.slice(2, 8));
                    $('#pm_start_at').html(hour + startTime.slice(2, 5));
                })
                .catch((error) => {
                    console.log(error);
                });
        }
        function setEndAt (date) {
            return axios
                .get('/attendance-times/' + date)
                .then((response) => {
                    const startTime = response.data.start_time;
                    const amEndAt = parseInt(startTime.slice(0 , 2)) + 4;
                    const pmEndAt = parseInt(startTime.slice(0 , 2)) + 9;
                    $('#am_end_at').val(amEndAt + startTime.slice(2, 8));
                    $('#am_end_at').html(amEndAt + startTime.slice(2, 5));
                    $('#pm_end_at').val(pmEndAt + startTime.slice(2, 8));
                    $('#pm_end_at').html(pmEndAt + startTime.slice(2, 5));
                })
                .catch((error) => {
                    console.log(error);
                });
        }
        $('#apply').on('click', (e) => {
            const id = $('#id').val();
            const url = '/leave-reports/' + id;
            const startDate = $('#start_date').val();
            const startTime = $('#start_time').val();
            const endDate = $('#end_date').val();
            const endTime = $('#end_time').val();
            const type = $('#type').val();
            const reason = $('#reason').val();
            if (startDate == '') {
                alert('請輸入開始的日期。');
                return false;
            }
            if (endDate == '') {
                alert('請輸入結束的日期。');
                return false;
            }
            const startAt = Date.parse(startDate + ' ' + startTime);
            const endAt = Date.parse(endDate + ' ' + endTime);
            if (startAt >= endAt) {
                alert('請輸入正確的日期。');
                return false;
            }
            if (type != 1 && reason == '') {
                alert('特別休假以外請輸入理由。');
                return false;
            }
            axios
            .put(url, {
                'start_at': startDate + ' ' + startTime,
                'end_at': endDate + ' ' + endTime,
                'type': type,
                'reason': reason
            })
            .then(() => {
                location.reload();
            })
            .catch((error) => {
                alert(error.response.data);
            });
        });
        $('#approve').on('click', (e) => {
            const id = $('#id').val();
            const note = $('#note').val();
            const status = $('#layer1_status').val();
            const url = '/departments/leave-reports/' + id;
            axios
            .put(url, {
                'note': note,
                'status': status
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
