<!-- View stored in resources/views/admin/sub-holiday-report/list.blade.php -->

@extends('admin.layouts.layout')
@section('content')
    @php
        $action = isset($user)
                ? "/admin/users/{$user->id}/sub-holiday-reports"
                : "/admin/sub-holiday-reports";
    @endphp
    <div class="container">
        @if (isset($user))
            <div class="row">
                <div class="col">
                    <h3 class="mt-3">{{ $user->name }}({{ $user->alias }}) 詳細</h3>
                </div>
            </div>
            @include('admin.common.navbar', [
                'page' => 'sub-holiday-report',
                'id' => $user->id,
            ])
        @else
            <div class="row">
                <div class="col">
                    <h3 class="mt-3">振替休暇一覧</h3>
                </div>
            </div>
        @endif
        <form method="GET" action="{{ $action }}">
            @csrf
            <div class="row g-3 align-items-center mb-3">
                @if (empty($user))
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-text" id="name">氏名</span>
                            <input type="text" class="form-control" id="name" name="name" value="{{ request()->input('name') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-text" id="alias">英語名</span>
                            <input type="text" class="form-control" id="alias" name="alias" value="{{ request()->input('alias') }}">
                        </div>
                    </div>
                @endif
                <div class="col-md-2">
                    <div class="input-group">
                        <span class="input-group-text" id="year">年</span>
                        <input type="text" class="form-control" id="year" name="year" value="{{ request()->input('year') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="input-group">
                        <span class="input-group-text" id="month">月</span>
                        <select class="form-select" name="month" id="month">
                            @for ($i = 0; $i <= 12; $i ++)
                                <option value="{{ $i }}" @selected($i == request()->input('month'))>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-dark">検索</button>
                </div>
            </div>
        </form>
        <table class="table table-striped table-hover table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>id</th>
                    @if (empty($user))
                        <th>氏名</th>
                        <th>英語名</th>
                    @endif
                    <th>振替休暇日付</th>
                    <th>振替出勤日付</th>
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
                                class="btn btn-dark"
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
                    <h5>振替休暇詳細</h5>
                </div>
                <div class="modal-body" id="modal-body">
                    <input id="id" hidden>
                    <input id="layer" hidden>
                    <table class="table table-striped table-hover table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>カラム</th>
                                <th>內容</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>振替休暇</td>
                                <td><input type="date" class="form-control" name="date" id="date" disabled></td>
                            </tr>
                            <tr>
                                <td>振替出勤</td>
                                <td><input type="text" class="form-control" name="report" id="report" disabled></td>
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
            const layer = '{{ Auth::user()->approval_layer }}';
            const url = '/admin/sub-holiday-reports/' + id;
            $('#id').val(id);
            $('#layer').val(layer);
            $('#note').attr('disabled', true);
            for (let i = 1; i <= 4; i++) {
                $('#layer' + i +'_status option[selected]').removeAttr('selected');
                $('#layer' + i +'_status').attr('disabled', true);
                $('#layer' + i +'_approve').hide();
                $('.layer' + i + 'status').hide();
            }
            axios
            .get(url)
            .then((response) => {
                $('#date').val(response.data.date);
                $('#report').val('日付：' + response.data.sub_attendance_report.date + '　理由：' + response.data.sub_attendance_report.reason);
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
        });
        $("[id$='approve']").on('click', (e) => {
            const id = $('#id').val();
            const layer = $('#layer').val();
            const status = $('#layer' + layer + '_status').val();
            const url = '/admin/sub-holiday-reports/' + id;
            axios
            .put(url, {
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
    </script>
@endsection