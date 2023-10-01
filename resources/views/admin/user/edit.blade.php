<!-- View stored in resources/views/admin/user/edit.blade.php -->

@extends('admin.layouts.layout')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col">
                <h3 class="mt-3">員工資料</h3>
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
        <form method="POST" action="/admin/users/{{ $user->id }}">
            @csrf
            @method('PUT')
            <div class="col-md-6">
                <table class="table table-striped table-hover table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>欄位</th>
                            <th>內容</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>編號</td>
                            <td>{{ $user->id }}<input type="hidden" name="id" value="{{ $user->id }}"></td>
                        </tr>
                        <tr>
                            <td>Email</td>
                            <td><input type="email" class="form-control" name="email" value="{{ $user->email }}"></td>
                        </tr>
                        <tr>
                            <td>姓名</td>
                            <td><input type="text" class="form-control" name="name" value="{{ $user->name }}"></td>
                        </tr>
                        <tr>
                            <td>英文名稱</td>
                            <td><input type="text" class="form-control" name="alias" value="{{ $user->alias }}"></td>
                        </tr>
                        <tr>
                            <td>性別</td>
                            <td>
                                <select class="form-select" name="sex">
                                    @foreach (\App\Enums\UserSex::getData() as $key => $value)
                                        <option value="{{ $key }}" @selected($key == $user->sex)>{{ $value }}</option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>生日</td>
                            <td><input type="date" class="form-control" name="birthday" value="{{ $user->birthday }}"></td>
                        </tr>
                        <tr>
                            <td>到職日</td>
                            <td><input type="date" class="form-control" name="hire_date" value="{{ $user->hire_date }}"></td>
                        </tr>
                        <tr>
                            <td>兌現特休天數</td>
                            <td><input type="number" class="form-control" name="paid_leaves" value="{{ $user->paid_leaves }}"></td>
                        </tr>
                        <tr>
                            <td>部門</td>
                            <td>
                                <select class="form-select" name="department_id">
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}" @selected($department->id == $user->department_id)>{{ $department->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>狀態</td>
                            <td>
                                <select class="form-select" name="locked">
                                    @foreach (\App\Enums\AccountStatus::getData() as $key => $value)
                                        <option value="{{ $key }}" @selected($key == $user->locked)>{{ $value }}</option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="row">
                <div class="col">
                    <button type="submit" class="btn btn-dark">儲存</button>
                    <a href="javascript:history.back()" class="btn btn-secondary">返回</a>
                </div>
            </div>
        </form>
    </div>
@endsection
