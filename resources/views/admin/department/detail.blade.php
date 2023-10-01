<!-- View stored in resources/views/admin/department/detail.blade.php -->

@extends('admin.layouts.layout')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col">
                <h3 class="mt-3">部署詳細</h3>
            </div>
        </div>
        <div class="col-md-6">
            <table class="table table-striped table-hover table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>カラム</th>
                        <th>內容</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>id</td>
                        <td>{{ $department->id }}</td>
                    </tr>
                    <tr>
                        <td>部署名</td>
                        <td>{{ $department->name }}</td>
                    </tr>
                    <tr>
                        <td>マネージャー</td>
                        <td>@if($department->manager){{ $department->manager->name }}@endif</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="row">
            <div class="col">
                <a href="/admin/departments/{{ $department->id }}/edit"><button class="btn btn-dark">編集</button></a>
                <a href="javascript:history.back()" class="btn btn-secondary">戻る</a>
            </div>
        </div>
    </div>
@endsection
