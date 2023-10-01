<!-- View stored in resources/views/user/login.blade.php -->

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
                    <a href="/login"><img class="rounded mx-auto d-block" src="/images/btn_google_signin.png" width="100%"></a>
                </main>
            </div>
        </div>
    </body>
</html>
