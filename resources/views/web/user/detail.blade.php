<!-- View stored in resources/views/web/user/detail.blade.php -->

@extends('web.layouts.layout')
@section('content')
    @php
        $isManagerRoute = Request::is('departments/*');
    @endphp
    <div class="container">
        @if ($isManagerRoute)
            <div class="row">
                <div class="col">
                    <h3 class="mt-3">{{ $user->name }}（{{ $user->alias }}）詳細</h3>
                </div>
            </div>
            @include('web.common.navbar', [
                'page' => 'user',
                'id' => $user->id,
            ])
        @else
        <div class="row">
                <div class="col">
                    <h3 class="mt-3">基本資料</h3>
                </div>
            </div>
        @endif
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
                        <td>Email</td>
                        <td>{{ $user->email }}</td>
                    </tr>
                    <tr>
                        <td>姓名</td>
                        <td>{{ $user->name }}</td>
                    </tr>
                    <tr>
                        <td>英文名稱</td>
                        <td>{{ $user->alias }}</td>
                    </tr>
                    <tr>
                        <td>性別</td>
                        <td>{{ \App\Enums\UserSex::getDescription($user->sex) }}</td>
                    </tr>
                    <tr>
                        <td>生日</td>
                        <td>{{ $user->birthday }}</td>
                    </tr>
                    <tr>
                        <td>到職日</td>
                        <td>{{ $user->hire_date }}</td>
                    </tr>
                    <tr>
                        <td>特休天數</td>
                        <td>{{ $leave->unexpired }}</td>
                    </tr>
                    <tr>
                        <td>過期特休天數</td>
                        <td>{{ $leave->expired }}</td>
                    </tr>
                    <tr>
                        <td>兌現特休天數</td>
                        <td>{{ $user->paid_leaves }}</td>
                    </tr>
                    <tr>
                        <td>部門</td>
                        <td>{{ $user->department->name }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection