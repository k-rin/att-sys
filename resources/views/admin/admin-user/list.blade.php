<!-- View stored in resources/views/admin/admin-user/list.blade.php -->

@extends('admin.layouts.layout')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col">
                <h3 class="mt-3">管理者一覧</h3>
            </div>
        </div>
        <div>
            <table class="table table-striped table-hover table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>id</th>
                        <th>email</th>
                        <th>權限</th>
                        <th>狀態</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <th>{{ $user->id }}</th>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->role }}</td>
                            <td>{{ \App\Enums\AccountStatus::getDescription($user->locked) }}</td>
                            <td><a href="admin-users/{{ $user->id }}"><button class="btn btn-dark">詳細</button></a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="row">
            {{ $users->appends(request()->input())->links() }}
        </div>
    </div>
@endsection