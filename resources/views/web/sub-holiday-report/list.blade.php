<!-- View stored in resources/views/web/sub-holiday-report/list.blade.php -->

@extends('web.layouts.layout')
@section('content')
    @php
        $isManagerRoute = Request::is('departments/*');
        $action = $isManagerRoute
                ? isset($user)
                    ? "/departments/users/{$user->id}/sub-holiday-reports"
                    : "/departments/sub-holiday-reports"
                : "/sub-holiday-reports";
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
                    'page' => 'sub-holiday-report',
                    'id' => $user->id,
                ])
            @else
            <div class="row">
                    <div class="col">
                        <h3 class="mt-3">{{ Auth::user()->department->name }} 補假申請列表</h3>
                    </div>
                </div>
            @endif
        @else
            <div class="row">
                <div class="col">
                    <h3 class="mt-3">{{ $user->name }}（{{ $user->alias }}）補假申請列表</h3>
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
                    <th>補假日期</th>
                    <th>假日上班日期</th>
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
                        <td>{{ $report->date }}</td>
                        <td>{{ $report->subAttendanceReport->date }}</td>
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
                    <h5>加班詳細</h5>
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
                                <td>補假日期</td>
                                <td><input type="date" class="form-control" name="date" id="date"></td>
                            </tr>
                            <tr>
                                <td>假日上班</td>
                                <td><input type="text" class="form-control" name="report" id="report"></td>
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
            const id = e.relatedTarget.getAttribute('data-bs-report-id');
            const isManagerRoute = '{{ $isManagerRoute }}';
            const url = isManagerRoute
                ? '/departments/sub-holiday-reports/' + id
                : '/sub-holiday-reports/' + id;
            $('#id').val(id);
            $('#date').attr('disabled', true);
            $('#report').attr('disabled', true);
            $('#approve').hide();
            $('#apply').hide();
            for (let i = 1; i <= 4; i++) {
                $('#layer' + i + '_status option[selected]').removeAttr('selected');
                $('#layer' + i + '_status').attr('disabled', true);
                $('.layer' + i + 'status').hide();
            }
            axios
            .get(url)
            .then((response) => {
                $('#date').val(response.data.date);
                $('#report').val('日期：' + response.data.sub_attendance_report.date + '　理由：' + response.data.sub_attendance_report.reason);
                $("#compensation option[value='" + response.data.compensation  + "']").attr('selected', true);
                $('#note').val(response.data.note);
                Object.keys(response.data.layer_status).forEach((key) => {
                    $("#layer" + key + "_status option[value='" + response.data.layer_status[key] + "']").attr('selected', true);
                    $('.layer' + key + 'status').show();
                });
                if (! response.data.closed) {
                    if (isManagerRoute) {
                        $('#note').attr('disabled', false);
                        $('#layer1_status').attr('disabled', false);
                        $('#approve').show();
                    } else {
                        if (response.data.editable) {
                            $('#date').attr('disabled', false);
                            $('#reason').attr('disabled', false);
                            $('#compensation').attr('disabled', false);
                            $('#apply').show();
                        }
                    }
                }
            })
            .catch((error) => {
                console.log(error);
            });
        });
        $('#apply').on('click', (e) => {
            const id = $('#id').val();
            const date = $('#date').val();
            const reason = $('#reason').val();
            const compensation = $('#compensation').val();
            const url = '/sub-attendance-reports/' + id;
            if (date == '') {
                alert('請輸入日期。');
                return false;
            }
            if (reason == '') {
                alert('請輸入理由。');
                return false;
            }
            axios
            .put(url, {
                'date': date,
            })
            .then(() => {
                location.reload();
            })
            .catch((error) => {
                alert(error.response.data);
            });
        });
        $('#approve').on('click', () => {
            const id = $('#id').val();
            const status = $('#layer1_status').val();
            const url = '/departments/sub-holiday-reports/' + id;
            axios
            .put(url, {
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
