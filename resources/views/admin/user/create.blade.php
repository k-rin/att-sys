<!-- View stored in resources/views/admin/user/create.blade.php -->

@extends('admin.layouts.layout')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col">
                <h3 class="mt-3">社員登録</h3>
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
        <form method="POST" action="/admin/users">
            @csrf
            <input name="paid_leaves" value="0" hidden>
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
                            <td>Email</td>
                            <td><input type="email" class="form-control" name="email"></td>
                        </tr>
                        <tr>
                            <td>氏名</td>
                            <td><input type="text" class="form-control" name="name"></td>
                        </tr>
                        <tr>
                            <td>英語名</td>
                            <td><input type="text" class="form-control" name="alias"></td>
                        </tr>
                        <tr>
                            <td>性別</td>
                            <td>
                                <select class="form-select" name="sex">
                                    @foreach (\App\Enums\UserSex::getData() as $key => $value)
                                        <option value="{{ $key }}" @selected($key == \App\Enums\UserSex::Female)>{{ $value }}</option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>誕生日</td>
                            <td><input type="date" class="form-control" name="birthday"></td>
                        </tr>
                        <tr>
                            <td>入社日</td>
                            <td><input type="date" class="form-control" name="hire_date"></td>
                        </tr>
                        <tr>
                            <td>部署</td>
                            <td>
                                <select class="form-select" name="department_id">
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}">{{ $department->name }}</option>
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
                    <button type="submit" class="btn btn-dark">登録</button>
                </div>
            </div>
        </form>
    </div>
@endsection
