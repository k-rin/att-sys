<!-- View stored in resources/views/admin/password_reset.blade.php -->

<!doctype html>
<html lang="zh-TW">
    <head>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous">
        <style>
            body {
                height: 100%;
                display: flex;
                align-items: center;
                padding-top: 40px;
                padding-bottom: 40px;
                background-color: #f5f5f5;
            }
            .form-login {
                width: 100%;
                max-width: 330px;
                padding: 15px;
                margin: auto;
            }
            .form-login .form-floating:focus-within {
                z-index: 2;
            }
            .form-login input[type="email"] {
                margin-bottom: -1px;
                border-bottom-right-radius: 0;
                border-bottom-left-radius: 0;
            }
            .form-login input[type="password"] {
                margin-bottom: 10px;
                border-top-left-radius: 0;
                border-top-right-radius: 0;
            }
        </style>
    </head>
    <body>
        <div class="container-fluid">
            <div class="row">
                <main class="col-md-9 form-login">
                    @if ($error)
                        <div class="alert alert-danger" role="alert">
                            {{ $error }}
                        </div>
                    @else
                        <br>
                        <div class="form-floating">
                            <input type="hidden" class="form-control" id="token" name="token" value="{{ $token }}">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                            <label for="email">Password</label>
                        </div>
                        <div class="form-floating">
                            <input type="password" class="form-control" id="confirmation" name="confirmation" placeholder="Confirm Password">
                            <label for="password">Confirm Password</label>
                        </div>
                        <br>
                        <button type="button" class="w-100 btn btn-lg btn-dark" id="reset">Reset</button>
                    @endif
                </main>
            </div>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="resultModal" tabindex="-1" role="dialog" aria-labelledby="resultModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="resultModalLabel">密碼重置</h5>
                    </div>
                    <div class="modal-body" id="modal-body"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">關閉</button>
                    </div>
                </div>
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-/bQdsTh/da6pkI1MST/rWKFNjaCP5gBSY4sEBT38Q/9RBh9AH40zEOg7Hlq2THRZ" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
        <script>
            $('#reset').click((e) => {
                const token = $('#token').val();
                const password = $('#password').val();
                const confirmation = $('#confirmation').val();
                if (!password || !confirmation) {
                    $('#modal-body').html('<p class="text-danger">請輸入密碼 / 確認密碼</p>');
                    $('#resultModal').modal('show');
                } else if (password != confirmation) {
                    $('#modal-body').html('<p class="text-danger">密碼與確認密碼不相符</p>');
                    $('#resultModal').modal('show');
                } else {
                    const data = {
                        'token': token,
                        'password': password
                    };
                    axios
                    .put('/admin/password', data)
                    .then(() => {
                        $('#modal-body').html('密碼重置成功，將重新導向Login頁面');
                        $('#resultModal').modal('show');
                        setTimeout(() => {
                            window.location.href = '/admin/index';
                        }, 3000);
                    })
                    .catch(() => {
                        $('#modal-body').html('<p class="text-danger">密碼重置失敗</p>');
                        $('#resultModal').modal('show');
                    });
                }
            });
        </script>
    </body>
</html>
