<!-- View stored in resources/views/admin/admin-user/edit.blade.php -->

@extends('admin.layouts.layout')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col">
                <h3 class="mt-3">管理員資料</h3>
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
        <form method="POST" action="/admin/admin-users/{{ $user->id }}">
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
                            <td>id</td>
                            <td>{{ $user->id }}<input type="hidden" name="id" value="{{ $user->id }}"></td>
                        </tr>
                        <tr>
                            <td>Email</td>
                            <td><input type="email" class="form-control" name="email" value="{{ $user->email }}"></td>
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
                                        <option value="{{ $value }}" @selected($value == $user->role)>{{ $value }}</option>
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
