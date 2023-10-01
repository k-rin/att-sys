<!doctype html>
<html lang="zh-TW">
    <head>
        <title>出勤系統</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous">
        <style>
            .pagination > li > a,
            .pagination > li > a:focus,
            .pagination > li > a:hover,
            .pagination > li > span,
            .pagination > li > span:focus,
            .pagination > li > span:hover {
                color: #212529;
            }
            .pagination > .active > a,
            .pagination > .active > a:focus,
            .pagination > .active > a:hover,
            .pagination > .active > span,
            .pagination > .active > span:focus,
            .pagination > .active > span:hover {
                background-color: #212529;
                border-color: #212529;
            }
            .page-item.active .page-link {
                z-index: 1;
                color: #fff;
                background-color: #212529;
                border-color: #212529;
            }
            .nav-tabs .nav-item .nav-link {
                /* background-color: #0080FF; */
                color: #212529;
            }
            .nav-tabs .nav-item .nav-link.active {
                color: #6c757d;
            }
            .table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
                background-color: #F0FFF0;
            }
        </style>
    </head>

    <body>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-/bQdsTh/da6pkI1MST/rWKFNjaCP5gBSY4sEBT38Q/9RBh9AH40zEOg7Hlq2THRZ" crossorigin="anonymous"></script>
        @include('web.common.header')
        <div class="container-fluid">
            <div class="row">
                <nav class="nav col-md-3 col-lg-2">
                    @include('web.common.sidebar')
                </nav>
                <main class="col-md-9 col-lg-10">
                    @yield('content')
                </main>
            </div>
        </div>
        @include('web.common.footer')
    </body>
</html>
