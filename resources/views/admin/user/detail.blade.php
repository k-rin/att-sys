<!-- View stored in resources/views/admin/user/detail.blade.php -->

@extends('admin.layouts.layout')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col">
                <h3 class="mt-3">{{ $user->name }}({{ $user->alias }}) 詳細</h3>
            </div>
        </div>
        @include('admin.common.navbar', [
            'page' => 'user',
            'id' => $user->id,
        ])
        <div class="col-md-6">
            <table class="table table-striped table-hover table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>カラム</th>
                        <th>内容</th>
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
                        <td>氏名</td>
                        <td>{{ $user->name }}</td>
                    </tr>
                    <tr>
                        <td>英語名</td>
                        <td>{{ $user->alias }}</td>
                    </tr>
                    <tr>
                        <td>性別</td>
                        <td>{{ \App\Enums\UserSex::getDescription($user->sex) }}</td>
                    </tr>
                    <tr>
                        <td>誕生日</td>
                        <td>{{ $user->birthday }}</td>
                    </tr>
                    <tr>
                        <td>入社日</td>
                        <td>{{ $user->hire_date }}</td>
                    </tr>
                    <tr>
                        <td>有給日数</td>
                        <td>{{ $leave->unexpired }}</td>
                    </tr>
                    <tr>
                        <td>時間切れ有給日数</td>
                        <td>{{ $leave->expired }}</td>
                    </tr>
                    <tr>
                        <td>支払った有給日数</td>
                        <td>{{ $user->paid_leaves }}</td>
                    </tr>
                    <tr>
                        <td>部署</td>
                        <td>{{ $user->department->name }}</td>
                    </tr>
                    <tr>
                        <td>状態</td>
                        <td>{{ \App\Enums\AccountStatus::getDescription($user->locked) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        @can('isNotReadonly')
            <div class="row">
                <div class="col-md-6 text-end">
                    <a href="/admin/users/{{ $user->id }}/edit"><button class="btn btn-dark">編集</button></a>
                </div>
            </div>
        @endcan
    </div>
@endsection
