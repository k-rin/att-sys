<!-- View stored in resources/views/web/user/list.blade.php -->

@extends('web.layouts.layout')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col">
                <h3 class="mt-3">{{ Auth::user()->department->name }} 員工列表</h3>
            </div>
        </div>
        <form method="GET" action="/departments/users">
            @csrf
            <div class="row g-3 align-items-center mb-3">
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text" id="name">姓名</span>
                        <input type="text" class="form-control" id="name" name="name" value="{{ request()->input('name') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text" id="alias">英文名稱</span>
                        <input type="text" class="form-control" id="alias" name="alias" value="{{ request()->input('alias') }}">
                    </div>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-success">搜尋</button>
                </div>
            </div>
        </form>
        <div>
            <table class="table table-striped table-hover table-bordered">
                <thead class="table-success">
                    <tr>
                        <th>id</th>
                        <th>姓名</th>
                        <th>英文名稱</th>
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
                            <td><a href="/departments/users/{{ $user->id }}" class="btn btn-success">詳細</a></td>
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
