<!-- View stored in resources/views/admin/admin-user/detail.blade.php -->

@extends('admin.layouts.layout')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col">
                <h3 class="mt-3">管理員資料</h3>
            </div>
        </div>
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
                        <td>{{ $user->id }}</td>
                    </tr>
                    <tr>
                        <td>Email</td>
                        <td>{{ $user->email }}</td>
                    </tr>
                    <tr>
                        <td>權限</td>
                        <td>{{ $user->role }}</td>
                    </tr>
                    <tr>
                        <td>狀態</td>
                        <td>{{ \App\Enums\AccountStatus::getDescription($user->locked) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="row">
            <div class="col">
                <a href="{{ $user->id }}/edit" class="btn btn-dark">編輯</a>
            </div>
        </div>
    </div>
@endsection
