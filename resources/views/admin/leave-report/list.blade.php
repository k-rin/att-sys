<!-- View stored in resources/views/admin/leave-report/list.blade.php -->

@extends('admin.layouts.layout')
@section('content')
    @php
        $action = isset($user)
                ? "/admin/users/{$user->id}/leave-reports"
                : "/admin/leave-reports";
        $userId = isset($user)
                ? $user->id
                : "";
    @endphp
    <div class="container">
        @if (isset($user))
            <div class="row">
                <div class="col">
                    <h3 class="mt-3">{{ $user->name }}({{ $user->alias }}) 詳細</h3>
                </div>
            </div>
            @include('admin.common.navbar', [
                'page' => 'leave-report',
                'id' => $user->id,
            ])
        @else
            <div class="row">
                <div class="col">
                    <h3 class="mt-3">休暇届一覧</h3>
                </div>
            </div>
        @endif
        <form method="GET" action="{{ $action }}">
            @csrf
            <div class="row g-3 align-items-center mb-3">
                @if (empty($user))
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-text">氏名</span>
                            <input type="text" class="form-control" id="name" name="name" value="{{ request()->input('name') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-text">英語名</span>
                            <input type="text" class="form-control" id="alias" name="alias" value="{{ request()->input('alias') }}">
                        </div>
                    </div>
                @endif
                <div class="col-md-2">
                    <div class="input-group">
                        <span class="input-group-text">年</span>
                        <input type="text" class="form-control" id="year" name="year" value="{{ request()->input('year') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="input-group">
                        <span class="input-group-text">月</span>
                        <select class="form-select" name="month" id="month">
                            @for ($i = 0; $i <= 12; $i ++)
                                <option value="{{ $i }}" @selected($i == request()->input('month'))>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-dark">検索</button>
                    @if (Auth::user()->can('isNotReadonly') && isset($user))
                        <button
                            type="button"
                            class="btn btn-outline-dark"
                            data-bs-toggle="modal"
                            data-bs-target="#formModal"
                            data-bs-type="register"
                            data-bs-user-id="{{ $user->id }}">
                            登録
                        </button>
                    @endif
                </div>
            </div>
        </form>
        <div>
            <table class="table table-striped table-hover table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>id</th>
                        <th>氏名</th>
                        <th>英語名</th>
                        <th>開始日時</th>
                        <th>終了日時</th>
                        <th>承認状態</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reports as $report)
                        <tr>
                            <th>{{ $report->id }}</th>
                            <td>{{ $report->user->name }}</td>
                            <td>{{ $report->user->alias }}</td>
                            <td>{{ Str::substr($report->start_at, 0, 16) }}</td>
                            <td>{{ Str::substr($report->end_at, 0, 16) }}</td>
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
                                    class="btn btn-dark"
                                    data-bs-toggle="modal"
                                    data-bs-target="#formModal"
                                    data-bs-type="detail"
                                    data-bs-report-id="{{ $report->id }}">
                                    詳細
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="row">
            {{ $reports->appends(request()->input())->links() }}
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="formModal" tabindex="-1" role="dialog" aria-labelledby="formModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="title"></h5>
                </div>
                <div class="modal-body" id="modal-body">
                    <input id="id" hidden>
                    <input id="layer" hidden>
                    <table class="table table-striped table-hover table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>欄位</th>
                                <th>內容</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="detail">
                                <td>開始日時</td>
                                <td><input type="text" class="form-control" name="start_at" id="start_at"></td>
                            </tr>
                            <tr class="detail">
                                <td>終了日時</td>
                                <td><input type="text" class="form-control" name="end_at" id="end_at"></td>
                            </tr>
                            <tr class="detail">
                                <td>日数</td>
                                <td><input type="text" class="form-control" name="days" id="days" disabled></td>
                            </tr>
                            <tr class="register">
                                <td>開始日期</td>
                                <td><input type="date" class="form-control" name="start_date" id="start_date"></td>
                            </tr>
                            <tr class="register">
                                <td>開始時間</td>
                                <td>
                                    <select class="form-select" name="start_time" id="start_time">
                                        <option id="am_start_at" value="" selected></option>
                                        <option id="pm_start_at" value=""></option>
                                    </select>
                                </td>
                            </tr>
                            <tr class="register">
                                <td>結束日期</td>
                                <td><input type="date" class="form-control" name="end_date" id="end_date"></td>
                            </tr>
                            <tr class="register">
                                <td>結束時間</td>
                                <td>
                                    <select class="form-select" name="end_time" id="end_time">
                                        <option id="am_end_at" value=""></option>
                                        <option id="pm_end_at" value="" selected></option>
                                    </select>
                                </td>
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
                    <button type="button" class="btn btn-dark" id="register">登録</button>
                    <button type="button" class="btn btn-secondary" id="close" data-bs-dismiss="modal">關閉</button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        const userId = '{{ $userId }}';
        $('#formModal').on('show.bs.modal', (e) => {
            const type = e.relatedTarget.getAttribute('data-bs-type');
            for (let i = 1; i <= 4; i++) {
                $('#layer' + i +'_status option[selected]').removeAttr('selected');
                $('#layer' + i +'_status').attr('disabled', true);
                $('#layer' + i +'_approve').hide();
                $('.layer' + i + 'status').hide();
            }
            $('#start_at').attr('disabled', true);
            $('#end_at').attr('disabled', true);
            $('#type').attr('disabled', true);
            $('#reason').attr('disabled', true);
            $('#note').attr('disabled', true);
            $('#type option[selected]').removeAttr('selected');
            if (type == "detail") {
                const id = e.relatedTarget.getAttribute('data-bs-report-id');
                const layer = '{{ Auth::user()->approval_layer }}';
                const url = '/admin/leave-reports/' + id;
                $('#title').html('休暇届詳細');
                $('.detail').show();
                $('.register').hide();
                $('#register').hide();
                $('#id').val(id);
                $('#layer').val(layer);
                $('#note').attr('disabled', true);
                axios
                .get(url)
                .then((response) => {
                    $('#start_at').val(response.data.start_at.slice(0, 16));
                    $('#end_at').val(response.data.end_at.slice(0, 16));
                    $('#days').val(response.data.days);
                    $('#reason').val(response.data.reason);
                    $('#note').val(response.data.note);
                    $("#type option[value='" + response.data.type + "']").attr('selected', true);
                    Object.keys(response.data.layer_status).forEach((key) => {
                        $("#layer" + key + "_status option[value='" + response.data.layer_status[key] + "']").attr('selected', true);
                        $('.layer' + key + 'status').show();
                        if (layer == key && ! response.data.closed) {
                            $('#layer' + key + '_approve').show();
                            $('#layer' + key + '_status').attr('disabled', false);
                            $('#note').attr('disabled', false);
                        }
                    });
                })
                .catch((error) => {
                    console.log(error);
                });
            } else if (type == "register") {
                $('#title').html('休暇届登録');
                $('.detail').hide();
                $('.register').show();
                $('#register').show();
                $('#start_at').val('');
                $('#end_at').val('');
                $('#reason').val('');
                $('#note').val('');
                $('#start_at').attr('disabled', false);
                $('#end_at').attr('disabled', false);
                $('#type').attr('disabled', false);
                $('#reason').attr('disabled', false);
                $('#note').attr('disabled', false);
            }
        });
        $("[id$='approve']").on('click', (e) => {
            const id = $('#id').val();
            const note = $('#note').val();
            const layer = $('#layer').val();
            const status = $('#layer' + layer + '_status').val();
            const url = '/admin/leave-reports/' + id;
            axios
            .put(url, {
                'note': note,
                'layer': layer,
                'status': status
            })
            .catch((error) => {
                console.log(error);
            })
            .finally(() => {
                location.reload();
            });
        });
        $('#register').on('click', () => {
            if (userId == '') {
                return;
            }
            const url = '/admin/users/' + userId + '/leave-reports';
            const startDate = $('#start_date').val();
            const startTime = $('#start_time').val();
            const endDate = $('#end_date').val();
            const endTime = $('#end_time').val();
            const type = $('#type').val();
            const reason = $('#reason').val();
            const note = $('#note').val();
            if (startDate == '') {
                alert('開始日付を入力してください。');
                return;
            }
            if (startTime == '') {
                alert('開始時間を入力してください。');
                return;
            }
            if (endDate == '') {
                alert('終了日付を入力してください。');
                return;
            }
            if (endTime == '') {
                alert('終了時間を入力してください。');
                return;
            }
            const startAt = Date.parse(startDate + ' ' + startTime);
            const endAt = Date.parse(endDate + ' ' + endTime);
            if (startAt >= endAt) {
                alert('正確の期間を入力してください。');
                return;
            }
            if (type != 1 && reason == '') {
                alert('有給以外、理由を入力してください。');
                return;
            }
            axios
            .post(url, {
                'start_at': startDate + ' ' + startTime,
                'end_at': endDate + ' ' + endTime,
                'type': type,
                'reason': reason,
                'note': note
            })
            .then(() => {
                location.reload();
            })
            .catch((error) => {
                $('#modal-body').html(error.response.data);
                $('#alertModal').modal('show');
            });
        });
        $('#start_date').change(() => {
            const date = $('#start_date').val();
            const url = '/admin/users/' + userId + '/attendance-times/' + date;
            axios
            .get(url)
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
        });
        $('#end_date').change(() => {
            const date = $('#end_date').val();
            const url = '/admin/users/' + userId + '/attendance-times/' + date;
            axios
            .get(url)
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
        });
    </script>
@endsection