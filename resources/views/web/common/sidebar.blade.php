<style>
    .bi {
    vertical-align: -.125em;
    pointer-events: none;
    fill: currentColor;
    }
    .dropdown-toggle {
        outline: 0;
    }
    .nav-flush .nav-link {
    border-radius: 0;
    }
    .btn-toggle {
    display: inline-flex;
    align-items: center;
    padding: .35rem .5rem;
    font-weight: 600;
    color: rgba(0, 0, 0, .65);
    background-color: transparent;
    border: 1;
    }
    .btn-toggle:hover,
    .btn-toggle:focus {
    color: rgba(0, 0, 0, .85);
    background-color: #d2f4ea;
    }
    .btn-toggle::before {
    width: 1.25em;
    line-height: 0;
    content: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='rgba%280,0,0,.5%29' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M5 14l6-6-6-6'/%3e%3c/svg%3e");
    transition: transform .35s ease;
    transform-origin: .5em 50%;
    }
    .btn-toggle[aria-expanded="true"] {
    color: rgba(0, 0, 0, .85);
    }
    .btn-toggle[aria-expanded="true"]::before {
    transform: rotate(90deg);
    }
    .btn-toggle-nav a {
    display: inline-flex;
    padding: .1875rem .5rem;
    margin-top: .125rem;
    margin-left: 3.025rem;
    text-decoration: none;
    }
    .btn-toggle-nav a:hover,
    .btn-toggle-nav a:focus {
    background-color: #d2f4ea;
    }
</style>

<div class="d-flex flex-column flex-shrink-0 p-3 bg-light" style="width: 280px;">
    <div class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-dark text-decoration-none">
        <svg class="bi me-2" width="40" height="32"></svg>
        <span class="fs-4">Dashboard</span>
    </div>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <button class="btn btn-toggle align-items-center rounded collapsed" data-bs-toggle="collapse" data-bs-target="#service-records-collapse" aria-expanded="false">
                <svg class="bi me-2" width="20" height="20"></svg>
                出勤紀錄
            </button>
            <div class="collapse" id="service-records-collapse">
                <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                    <li><a href="/service-records" class="link-dark rounded">紀錄列表</a></li>
                </ul>
            </div>
        </li>
        <li>
            <button class="btn btn-toggle align-items-center rounded collapsed" data-bs-toggle="collapse" data-bs-target="#leave-reports-collapse" aria-expanded="false">
                <svg class="bi me-2" width="20" height="20"></svg>
                假單
            </button>
            <div class="collapse" id="leave-reports-collapse">
                <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                    <li><a href="/leave-reports" class="link-dark rounded">假單列表</a></li>
                    <li><a href="/leave-reports/create" class="link-dark rounded">假單申請</a></li>
                </ul>
            </div>
        </li>
        <li>
            <button class="btn btn-toggle align-items-center rounded collapsed" data-bs-toggle="collapse" data-bs-target="#overtime-reports-collapse" aria-expanded="false">
                <svg class="bi me-2" width="20" height="20"></svg>
                加班
            </button>
            <div class="collapse" id="overtime-reports-collapse">
                <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                    <li><a href="/overtime-reports" class="link-dark rounded">加班申請列表</a></li>
                    <li><a href="/overtime-reports/create" class="link-dark rounded">加班申請</a></li>
                </ul>
            </div>
        </li>
        <li>
            <button class="btn btn-toggle align-items-center rounded collapsed" data-bs-toggle="collapse" data-bs-target="#sub-attendance-reports-collapse" aria-expanded="false">
                <svg class="bi me-2" width="20" height="20"></svg>
                假日上班
            </button>
            <div class="collapse" id="sub-attendance-reports-collapse">
                <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                    <li><a href="/sub-attendance-reports" class="link-dark rounded">假日上班申請列表</a></li>
                    <li><a href="/sub-attendance-reports/create" class="link-dark rounded">假日上班申請</a></li>
                </ul>
            </div>
        </li>
        <li>
            <button class="btn btn-toggle align-items-center rounded collapsed" data-bs-toggle="collapse" data-bs-target="#sub-holiday-reports-collapse" aria-expanded="false">
                <svg class="bi me-2" width="20" height="20"></svg>
                補假
            </button>
            <div class="collapse" id="sub-holiday-reports-collapse">
                <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                    <li><a href="/sub-holiday-reports" class="link-dark rounded">補假申請列表</a></li>
                    <li><a href="/sub-holiday-reports/create" class="link-dark rounded">補假申請</a></li>
                </ul>
            </div>
        </li>
        @can('isManager')
            <li>
                <button class="btn btn-toggle align-items-center rounded collapsed" data-bs-toggle="collapse" data-bs-target="#departments-collapse" aria-expanded="false">
                    <svg class="bi me-2" width="20" height="20"></svg>
                    部門管理
                </button>
                <div class="collapse" id="departments-collapse">
                    <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                        <li><a href="/departments/users" class="link-dark rounded">員工列表</a></li>
                        <li><a href="/departments/leave-reports" class="link-dark rounded">假單列表</a></li>
                        <li><a href="/departments/overtime-reports" class="link-dark rounded">加班申請列表</a></li>
                        <li><a href="/departments/sub-attendance-reports" class="link-dark rounded">假日上班申請列表</a></li>
                        <li><a href="/departments/sub-holiday-reports" class="link-dark rounded">補班申請列表</a></li>
                    </ul>
                </div>
            </li>
        @endcan
        <hr>
    </ul>
</div>
<script type="text/javascript">
    let target = '';
    let url = window.location.href;
    if (url.indexOf('departments') !== -1) {
        target = 'departments';
    } else if (url.indexOf('service-records') !== -1) {
        target = 'service-records';
    } else if (url.indexOf('leave-reports') !== -1) {
        target = 'leave-reports';
    } else if (url.indexOf('overtime-reports') !== -1) {
        target = 'overtime-reports';
    } else if (url.indexOf('sub-attendance-reports') !== -1) {
        target = 'sub-attendance-reports';
    } else if (url.indexOf('sub-holiday-reports') !== -1) {
        target = 'sub-holiday-reports';
    }
    let collapse = document.querySelector('#' + target + '-collapse');
    if (collapse) new bootstrap.Collapse(collapse);
</script>