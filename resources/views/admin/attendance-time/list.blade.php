<!-- View stored in resources/views/admin/attendance-time/list.blade.php -->

@extends('admin.layouts.layout')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col">
                <h3 class="mt-3">{{ $user->name }}({{ $user->alias }}) 詳細</h3>
            </div>
        </div>
        @include('admin.common.navbar', [
            'page' => 'attendance-time',
            'id' => $user->id,
        ])
        <div class="col-md-6">
            <table class="table table-hover table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>日付</th>
                        <th>始業時間</th>
                        <th>終業時間</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($times as $time)
                        <tr>
                            <td>{{ $time->date }}</td>
                            <td>{{ Str::substr($time->start_time, 0, 5) }}</td>
                            <td>{{ Str::substr($time->end_time, 0, 5) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @can('isNotReadonly')
            <div class="col-md-6">
                    <div class="text-end">
                        <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#formModal">追加</button>
                    </div>
                </div>
            </div>
        @endcan
    </div>
    <!-- Modal -->
    <div class="modal fade" id="formModal" tabindex="-1" role="dialog" aria-labelledby="formModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="formModalLabel">始業／終業時間設定</h5>
                </div>
                <div class="modal-body" id="modal-body">
                    <form method="POST" action="/admin/users/{{ $user->id }}/attendance-times">
                        @csrf
                        <table class="table table-hover table-bordered">
                            <thead class="table-dark">
                                <tr>
                                    <th>カラム</th>
                                    <th>內容</th>
                                    <th>備考</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="align-middle">開始日期</td>
                                    <td class="align-middle"><input type="date" class="form-control" name="date" id="date"></td>
                                    <td>
                                        <p class="fw-light lh-sm">・設定した日付以降の勤務時間になります。<br>・日付が既に設定された場合、更新になります</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="align-middle">始業時間</td>
                                    <td class="align-middle"><input type="time" class="form-control" name="start_time" id="start_time"></td>
                                    <td rowspan="3">
                                        <p class="fw-light lh-sm">・設定した時間は遅刻、早退、休暇に連動しています。</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="align-middle">終業時間</td>
                                    <td class="align-middle"><input type="time" class="form-control" name="end_time" id="end_time"></td>
                                </tr>
                            </tbody>
                        </table>
                    </from>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="close" data-bs-dismiss="modal">取消</button>
                    <button type="submit" class="btn btn-dark" id="create">確認</button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
@endsection