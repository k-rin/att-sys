<!-- View stored in resources/views/web/sub-holiday-report/create.blade.php -->

@extends('web.layouts.layout')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col">
                <h3 class="mt-3">補假申請</h3>
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
                        <td>補假日期</td>
                        <td><input type="date" class="form-control" name="date" id="date"></td>
                    </tr>
                    <tr>
                        <td>假日上班日期</td>
                        <td>
                            <div class="input-group">
                                <input type="text" class="form-control" name="sub_attendance_report" id="sub_attendance_report" value="" readonly>
                                <input type="text" class="form-control" name="report_id" id="report_id" value="" hidden>
                                <div class="input-group-append">
                                    <button
                                        type="button"
                                        class="btn btn-success"
                                        data-bs-toggle="modal"
                                        data-bs-target="#searchModal">
                                        選擇
                                    </button>
                                </div>
                            </div>
                        </td>
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
    </div>
    <!-- Alert Modal -->
    <div class="modal fade" id="alertModal" tabindex="-1" role="dialog" aria-labelledby="alertModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header"></div>
                <div class="modal-body" id="modal-body"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="close" data-bs-dismiss="modal">關閉</button>
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
        $('#apply').on('click', () => {
            const date = $('#date').val();
            const id = $('#report_id').val();
            const url = '/sub-holiday-reports';
            if (date == '') {
                $('#modal-body').html('請輸入日期。');
                $('#alertModal').modal('show');
                return;
            }
            if (id == '') {
                $('#modal-body').html('請選擇假日上班日期。');
                $('#alertModal').modal('show');
                return;
            }
            axios
            .post(url, {
                'date': date,
                'sub_attendance_report_id': id
            })
            .then(() => {
                location.href = '/sub-holiday-reports';
            })
            .catch((error) => {
                $('#modal-body').html(error.response.data);
                $('#alertModal').modal('show');
            });
        });
        $('#searchModal').on('show.bs.modal', (e) => {
            const date = $('#date').val();
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
        function pickUp(id) {
            const url = '/sub-attendance-reports/' + id;
            axios
            .get(url)
            .then((response) => {
                $('#sub_attendance_report').val(response.data.date + '；理由：' + response.data.reason);
                $('#report_id').val(response.data.id);
            })
            .catch((error) => {
                console.log(error);
            })
            .finally(() => {
                $('#searchModal').modal('hide');
            });
        };
    </script>
@endsection