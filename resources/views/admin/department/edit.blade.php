<!-- View stored in resources/views/admin/department/create.blade.php -->

@extends('admin.layouts.layout')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col">
                <h3 class="mt-3">編輯部門</h3>
            </div>
        </div>
        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form method="POST" action="/admin/departments/{{ $department->id }}">
            @csrf
            @method('PUT')
            <div class="col-md-6">
                <table class="table table-striped table-hover table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>カラム</th>
                            <th>內容</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>部署名</td>
                            <td><input type="text" class="form-control" name="name" value="{{ $department->name }}"></td>
                        </tr>
                        <tr>
                            <td>マネージャー</td>
                            <td><div class="input-group">
                                <input type="text" class="form-control" name="manager" id="manager" value="@if($department->manager){{ $department->manager->name }}@endif" readonly>
                                <input type="text" class="form-control" name="manager_id" id="manager_id" value="@if($department->manager){{ $department->manager->id }}@endif" hidden>
                                <div class="input-group-append">
                                <button
                                    type="button"
                                    class="btn btn-dark"
                                    data-bs-toggle="modal"
                                    data-bs-target="#searchModal"
                                    data-bs-department-id="{{ $department->id }}">
                                    検索
                                </button>
                                </div>
                            </div></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="row">
                <div class="col">
                    <button type="submit" class="btn btn-dark">編集</button>
                    <a href="javascript:history.back()" class="btn btn-secondary">戻る</a>
                </div>
            </div>
        </form>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="searchModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>社員検索</h5>
                </div>
                <div class="modal-body">
                    <div class="row g-3 align-items-center mb-3">
                        <div class="col-auto">
                            <div class="input-group">
                                <span class="input-group-text">氏名</span>
                                <input type="text" class="form-control" id="name" name="name" value="">
                                <input id="departmentId" value="" hidden>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="input-group">
                                <span class="input-group-text">英語名</span>
                                <input type="text" class="form-control" id="alias" name="alias" value="">
                            </div>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-dark" id="search">検索</button>
                        </div>
                        <div>
                            <table class="table table-striped table-hover table-bordered">
                                <thead class="table-dark">
                                    <tr>
                                        <th>id</th>
                                        <th>氏名</th>
                                        <th>英語名</th>
                                        <th>email</th>
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
        $('#searchModal').on('show.bs.modal', (e) => {
            const departmentId = e.relatedTarget.getAttribute('data-bs-department-id');
            $('#departmentId').val(departmentId);
            getUserList('', '', departmentId);
        });
        $('#search').on('click', (e) => {
            const name = $('#name').val();
            const alias = $('#alias').val();
            const departmentId = $('#departmentId').val();
            getUserList(name, alias, departmentId);
        });
        function getUserList(name, alias, departmentId) {
            let result = '';
            axios
            .get('/admin/users', {
                params: {
                    'name': name,
                    'alias': alias,
                    'department_id': departmentId
                }
            })
            .then((response) => {
                response.data.data.forEach((element) => {
                    result += '<tr><th>' + element.id + '</th><td>' + element.name + '</td><td>' + element.alias + '</td><td>' + element.email + '</td><td><button type="button" class="btn btn-secondary" onClick="pickUp(\'' + element.id + '\', \'' + element.name + '\')">選擇</button></td></tr>';
                });
            })
            .catch((error) => {
                console.log(error);
            })
            .finally(() => {
                $('#search-result').html(result);
            });
        };
        function pickUp(id, name) {
            $('#manager').val(name);
            $('#manager_id').val(id);
            $('#searchModal').modal('hide');
        };
    </script>
@endsection
