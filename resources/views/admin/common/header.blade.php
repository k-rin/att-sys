<header class="p-3 bg-dark text-white">
    <div class="container">
        <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
            <a href="/admin/users" class="d-flex align-items-center mb-2 mb-lg-0 text-white text-decoration-none col-12 col-lg-auto me-lg-auto"></a>
            <div class="col-12 col-lg-auto mb-3 mb-lg-0 me-lg-3 align-items-center">
                <span>{{ auth()->user()->email }}<span>
            </div>
            <div class="text-end">
                <form method="POST" action="/admin/logout">
                    @csrf
                    <button type="submit" class="btn btn-outline-light me-2">Logout</button>
                </form>
            </div>
        </div>
    </div>
</header>
