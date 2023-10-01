<!-- View stored in resources/views/admin/calendar/list.blade.php -->

@extends('admin.layouts.layout')
@section('content')
    @php
        if (isset($user)) {
            $search   = "/admin/users/{$user->id}/calendar";
            $resource = "/admin/users/{$user->id}/calendar/%s";
        } else {
            $search   = "/admin/calendar";
            $resource = "/admin/calendar/%s";
        }
    @endphp
    <div class="container">
        @if (isset($user))
            <div class="row">
                <div class="col">
                    <h3 class="mt-3">{{ $user->name }}({{ $user->alias }}) 詳細</h3>
                </div>
            </div>
            @include('admin.common.navbar', [
                'page' => 'calendar',
                'id' => $user->id,
            ])
        @else
            <div class="row">
                <div class="col">
                    <h3 class="mt-3">{{ request()->input('year') }} 年 {{ request()->input('month') }} 月カレンダー</h3>
                </div>
            </div>
        @endif
        @if ($calendar->isEmpty() && isset($user))
            <h5 class="my-5">個人カレンダーはありません。</h5>
            @can('isNotReadonly')
                <div class="col-md-6 text-center">
                    <form action="/admin/users/{{ $user->id }}/calendar" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-dark">新規作成</button>
                    </form>
                </div>
            @endcan
        @else
            <form method="GET" action="{{ $search }}">
                @csrf
                <div class="row g-3 align-items-center mb-3">
                    <div class="col-md-2">
                        <div class="input-group">
                            <span class="input-group-text">年</span>
                            <input type="text" class="form-control" id="year" name="year" value="{{ request()->input('year') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group">
                            <span class="input-group-text">月</span>
                            <select class="form-select" name="month" id="month">
                                @for ($i = 1; $i <= 12; $i ++)
                                    <option value="{{ $i }}" @selected($i == request()->input('month'))>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-dark">検索</button>
                    </div>
                </div>
            </form>
            <div>
                <table class="table table-hover table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>日付</th>
                            <th>曜日</th>
                            <th>休み</th>
                            <th>追伸</th>
                            @can('isNotReadonly')
                                <th></th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($calendar as $day)
                            <tr class="@if ($day->holiday) table-secondary @else table-default @endif">
                                <td>{{ $day->date }}</td>
                                <td>{{ $day->week }}</td>
                                <td>@if ($day->holiday) はい @else いいえ @endif</td>
                                <td>{{ $day->note }}</td>
                                @can('isNotReadonly')
                                    <td>
                                        <button
                                            type="button"
                                            class="btn btn-dark"
                                            data-bs-toggle="modal"
                                            data-bs-target="#formModal"
                                            data-bs-date="{{ $day->date }}">
                                            更新
                                        </button>
                                    </td>
                                @endcan
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    <!-- Modal -->
    <div class="modal fade" id="formModal" tabindex="-1" role="dialog" aria-labelledby="formModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="formModalLabel"></h5>
                </div>
                <div class="modal-body" id="modal-body">
                    <table class="table table-striped table-hover table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>カラム</th>
                                <th>內容</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>日付</td>
                                <td id="date"></td>
                            </tr>
                            <tr>
                                <td>曜日</td>
                                <td id="week"></td>
                            </tr>
                            <tr>
                                <td>休み</td>
                                <td>
                                <select class="form-select" name="holiday" id="holiday">
                                    <option value="0">いいえ</option>
                                    <option value="1">はい</option>
                                </td>
                            </tr>
                            <tr>
                                <td>備考</td>
                                <td><input type="text" class="form-control" name="note" id="note" value=""></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-dark" id="update">更新</button>
                    <button type="button" class="btn btn-secondary" id="close" data-bs-dismiss="modal">閉じる</button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sprintf/1.1.2/sprintf.min.js" integrity="sha512-pmG0OkYtZVB2EqETE5HPsEaok7sNZFfStp5rNdpHv0tGQjbt1z8Qjzhtx88/4wsttOtDwq5DZGJyKyzEe7ribg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        const resource = '{{ $resource }}';
        $('#formModal').on('show.bs.modal', (e) => {
            const date = e.relatedTarget.getAttribute('data-bs-date');
            const url = sprintf(resource, date);
            $('#holiday option[selected]').removeAttr('selected');
            axios
            .get(url)
            .then((response) => {
                $('#date').html(response.data.date);
                $('#week').html(response.data.week);
                $("#holiday option[value='" + response.data.holiday + "']").attr('selected', true);
                $('#note').val(response.data.note);
            })
            .catch((error) => {
                console.log(error);
            });
        });
        $('#update').on('click', (e) => {
            const date = $('#date').html();
            const url = sprintf(resource, date);
            const holiday = $('#holiday').val();
            const note = $('#note').val();
            axios
            .put(url, {
                'holiday': holiday,
                'note': note
            })
            .catch((error) => {
                console.log(error);
            })
            .finally(() => {
                location.reload();
            });
        })
    </script>
@endsection
