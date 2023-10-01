<!-- View stored in resources/views/admin/login.blade.php -->

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
                    <form method="POST" action="/admin/login">
                        @csrf
                        <br>
                        <div class="form-floating">
                            <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com">
                            <label for="email">Email address</label>
                        </div>
                        <div class="form-floating">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                            <label for="password">Password</label>
                        </div>
                        @error('email')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                        <button class="w-100 btn btn-lg btn-dark" type="submit">Login</button>
                    </form>
                    <br>
                    <button type="button" class="w-100 btn btn-lg btn-dark" data-bs-toggle="modal" data-bs-target="#confirmModal">Reset</button>
                </main>
            </div>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmModalLabel">密碼重置</h5>
                    </div>
                    <div class="modal-body" id="modal-body"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" id="close" data-bs-dismiss="modal">關閉</button>
                        <button type="button" class="btn btn-dark" id="confirm">確認</button>
                        <button class="btn btn-dark" id="loading" type="button" disabled>
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Loading...
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-/bQdsTh/da6pkI1MST/rWKFNjaCP5gBSY4sEBT38Q/9RBh9AH40zEOg7Hlq2THRZ" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
        <script>
            $('#confirmModal').on('show.bs.modal', (e) => {
                $('#loading').hide();
                const regex = /^[a-z]+[a-z0-9_-]+@[a-z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/i;
                const email = $('#email').val();
                if (regex.test(email)) {
                    $('#modal-body').html('是否確定重置此帳號：' + email + ' 的密碼？');
                    $('#confirm').show();
                } else {
                    $('#modal-body').html('<p class="text-danger">請輸入電子信箱</p>');
                    $('#confirm').hide();
                }
            });
            $('#confirm').click((e) => {
                $('#confirm').hide();
                $('#close').hide();
                $('#loading').show();
                const email = $('#email').val();
                axios
                .post('/admin/password/reset', { 'email': email })
                .then(() => {
                    $('#modal-body').html('已將密碼重置URL寄至 ' + email);
                })
                .catch(() => {
                    $('#modal-body').html('<p class="text-danger">' + email + ' 不存在</p>');
                })
                .finally(() => {
                    $('#loading').hide();
                    $('#close').show();
                });
            });
        </script>
    </body>
</html>
