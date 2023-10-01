<!-- View stored in resources/views/admin/user/list.blade.php -->

@extends('admin.layouts.layout')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col">
                <h3 class="mt-3">部署一覧</h3>
            </div>
        </div>
        <div>
            <table class="table table-striped table-hover table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>id</th>
                        <th>部署名</th>
                        <th>マネージャー</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($departments as $department)
                        <tr>
                            <th>{{ $department->id }}</th>
                            <td>{{ $department->name }}</td>
                            <td>@if($department->manager){{ $department->manager->name }}@endif</td>
                            <td><a href="/admin/departments/{{ $department->id }}" class="btn btn-dark">詳細</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
