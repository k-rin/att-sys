<!-- View stored in resources/views/admin/report-approver/list.blade.php -->

@extends('admin.layouts.layout')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col">
                <h3 class="mt-3">承認者一覧</h3>
            </div>
        </div>
        <div class="col-md-6">
            <table class="table table-striped table-hover table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>レイア</th>
                        <th>id</th>
                        <th>email</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($approvers as $approver)
                        <tr>
                            <td class="text-center">{{ \App\Enums\ApprovalLayer::getDescription($approver->id) }}</td>
                            @if ($approver->id == 1)
                                <td>-</td>
                                <td>各部署のマネージャーになる</td>
                                <td></td>
                            @else
                                @if ($approver->admin_id == 0)
                                    <td>-</td>
                                    <td>-</td>
                                @else
                                    <td>{{ $approver->admin->id }}</td>
                                    <td>{{ $approver->admin->email }}</td>
                                @endif
                                <td>
                                    <button
                                        type="button"
                                        class="btn btn-dark"
                                        data-bs-toggle="modal"
                                        data-bs-target="#searchModal"
                                        data-bs-layer-id="{{ $approver->id }}">
                                        編集
                                    </button>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="searchModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>管理者一覧</h5>
                </div>
                <div class="modal-body">
                    <div class="row g-3 align-items-center mb-3">
                        <div>
                            <table class="table table-striped table-hover table-bordered">
                                <thead class="table-dark">
                                    <tr>
                                    <tr>
                                        <th>id</th>
                                        <th>email</th>
                                        <th>權限</th>
                                        <th>狀態</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="search-result">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">閉じる</button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        $('#searchModal').on('show.bs.modal', (e) => {
            const layerId = e.relatedTarget.getAttribute('data-bs-layer-id');
            let result = '';
            axios
            .get('/admin/admin-users')
            .then((response) => {
                response.data.data.forEach((element) => {
                    const status = element.locked ? '非稼働' : '稼働中';
                    result += '<tr><th>' + element.id + '</th><td>' + element.email + '</td><td>' + element.role + '</td><td>' + status + '</td><td><button type="button" class="btn btn-secondary" onClick="pickUp(\'' + layerId + '\', \'' + element.id + '\')">選擇</button></td></tr>';
                });
            })
            .catch((error) => {
                console.log(error);
            })
            .finally(() => {
                $('#search-result').html(result);
            });
        });
        function pickUp(layerId, adminId) {
            axios
            .put('/admin/report-approvers/' + layerId, {
                'admin_id': adminId
            })
            .catch((error) => {
                console.log(error);
            })
            .finally(() => {
                location.reload();
            })
        };
    </script>
@endsection