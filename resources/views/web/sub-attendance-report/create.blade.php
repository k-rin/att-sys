<!-- View stored in resources/views/web/sub-attendance-report/create.blade.php -->

@extends('web.layouts.layout')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col">
                <h3 class="mt-3">假日上班申請</h3>
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
                        <td>日期</td>
                        <td><input type="date" class="form-control" name="date" id="date"></td>
                    </tr>
                    <tr>
                        <td>理由</td>
                        <td><input type="text" class="form-control" name="reason" id="reason"></td>
                    </tr>
                    <tr>
                        <td>補填</td>
                        <td>
                            <select class="form-select" name="compensation" id="compensation">
                                @foreach (\App\Enums\CompensationType::getData() as $key => $value)
                                    <option value="{{ $key }}" @selected($key == \App\Enums\CompensationType::Cash)>{{ $value }}</option>
                                @endforeach
                            </select>
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
    <!-- Modal -->
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
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        $('#apply').on('click', () => {
            const date = $('#date').val();
            const reason = $('#reason').val();
            const compensation = $('#compensation').val();
            const url = '/sub-attendance-reports';
            if (date == '') {
                $('#modal-body').html('請輸入日期。');
                $('#alertModal').modal('show');
                return;
            }
            if (reason == '') {
                $('#modal-body').html('請輸入理由。');
                $('#alertModal').modal('show');
                return;
            }
            axios
            .post(url, {
                'date': date,
                'reason': reason,
                'compensation': compensation
            })
            .then(() => {
                location.href = '/sub-attendance-reports';
            })
            .catch((error) => {
                $('#modal-body').html(error.response.data);
                $('#alertModal').modal('show');
            });
        });
    </script>
@endsection