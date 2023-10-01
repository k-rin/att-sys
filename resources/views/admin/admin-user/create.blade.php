<!-- View stored in resources/views/admin/admin-user/create.blade.php -->

@extends('admin.layouts.layout')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col">
                <h3 class="mt-3">新增管理員</h3>
            </div>
        </div>
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form method="POST" action="/admin/admin-users">
            @csrf
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
                            <td>Email</td>
                            <td><input type="email" class="form-control" name="email" value="{{ old('email') }}"></td>
                        </tr>
                        <tr>
                            <td>密碼</td>
                            <td><input type="text" class="form-control" name="password"></td>
                        </tr>
                        <tr>
                            <td>權限</td>
                            <td>
                                <select class="form-select" name="role">
                                    @foreach (\App\Enums\AdminRole::getData() as $value)
                                        <option value="{{ $value }}" @selected($value == \App\Enums\AdminRole::Operator)>{{ $value }}</option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>狀態</td>
                            <td>
                                <select class="form-select" name="locked">
                                    @foreach (\App\Enums\AccountStatus::getData() as $key => $value)
                                        <option value="{{ $key }}" @selected($key == \App\Enums\AccountStatus::Unlocked)>{{ $value }}</option>
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
                </div>
            </div>
        </form>
    </div>
@endsection
