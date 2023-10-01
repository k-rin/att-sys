<!-- View stored in resources/views/web/leave-report/create.blade.php -->

@extends('web.layouts.layout')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col">
                <h3 class="mt-3">假單申請</h3>
            </div>
        </div>
        <div class="col-md-6">
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
                                <option id="am_start_at" value="" selected></option>
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
                                <option id="pm_end_at" value="" selected></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>種類</td>
                        <td>
                            <select class="form-select" name="type" id="type">
                                @foreach (\App\Enums\LeaveType::getData() as $key => $value)
                                    <option value="{{ $key }}" @selected($key == \App\Enums\LeaveType::PaidLeave)>{{ $value }}</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>理由</td>
                        <td><input type="text" class="form-control" name="reason" id="reason" placeholder="特別休假不必填寫"></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="row">
            <div class="col">
                <button type="button" class="btn btn-success" id="apply">申請</button>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="alertModal" tabindex="-1" role="dialog" aria-labelledby="alertModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header"></div>
                <div class="modal-body" id="modal-body"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="close" data-bs-dismiss="modal">關閉</button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        $('#apply').on('click', () => {
            const startDate = $('#start_date').val();
            const startTime = $('#start_time').val();
            const endDate = $('#end_date').val();
            const endTime = $('#end_time').val();
            const type = $('#type').val();
            const reason = $('#reason').val();
            if (startDate == '') {
                $('#modal-body').html('請輸入開始的日期。');
                $('#alertModal').modal('show');
                return;
            }
            if (endDate == '') {
                $('#modal-body').html('請輸入結束的日期。');
                $('#alertModal').modal('show');
                return;
            }
            const startAt = Date.parse(startDate + ' ' + startTime);
            const now = Date.now();
            if (type == 1 && (startAt - now) / 86400000 < 6) {
                $('#modal-body').html('特別休假必須在一週前申請。');
                $('#alertModal').modal('show');
                return;
            }
            const endAt = Date.parse(endDate + ' ' + endTime);
            if (startAt >= endAt) {
                $('#modal-body').html('請輸入正確的日期。');
                $('#alertModal').modal('show');
                return;
            }
            if (type != 1 && reason == '') {
                $('#modal-body').html('特別休假以外請輸入理由。');
                $('#alertModal').modal('show');
                return;
            }
            axios
            .post('/leave-reports', {
                'start_at': startDate + ' ' + startTime,
                'end_at': endDate + ' ' + endTime,
                'type': type,
                'reason': reason
            })
            .then(() => {
                location.href='/leave-reports';
            })
            .catch((error) => {
                $('#modal-body').html(error.response.data);
                $('#alertModal').modal('show');
            });
        });
        $('#start_date').change(() => {
            const date = $('#start_date').val();
            axios
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
        });
        $('#end_date').change(() => {
            const date = $('#end_date').val();
            axios
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
        });
    </script>
@endsection