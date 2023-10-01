<!-- View stored in resources/views/admin/user/list.blade.php -->

@extends('admin.layouts.layout')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col">
                <h3 class="mt-3">社員一覧</h3>
            </div>
        </div>
        <form method="GET" action="/admin/users">
            @csrf
            <div class="row g-3 align-items-center mb-3">
                <div class="col-auto">
                    <div class="input-group">
                        <span class="input-group-text" id="name">氏名</span>
                        <input type="text" class="form-control" id="name" name="name" value="{{ request()->input('name') }}">
                    </div>
                </div>
                <div class="col-auto">
                    <div class="input-group">
                        <span class="input-group-text" id="alias">英語名</span>
                        <input type="text" class="form-control" id="alias" name="alias" value="{{ request()->input('alias') }}">
                    </div>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-dark">検索</button>
                </div>
            </div>
        </form>
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
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <th>{{ $user->id }}</th>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->alias }}</td>
                            <td>{{ $user->email }}</td>
                            <td><a href="/admin/users/{{ $user->id }}" class="btn btn-dark">詳細</a></td>
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
