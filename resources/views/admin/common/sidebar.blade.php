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
            <button class="btn btn-toggle align-items-center rounded collapsed" data-bs-toggle="collapse" data-bs-target="#users-collapse" aria-expanded="false">
                <svg class="bi me-2" width="20" height="20"></svg>
                社員
            </button>
            <div class="collapse" id="users-collapse">
                <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                    <li><a href="/admin/users" class="link-dark rounded">社員一覧</a></li>
                    @can('isNotReadonly')
                        <li><a href="/admin/users/create" class="link-dark rounded">社員登録</a></li>
                    @endcan
                </ul>
            </div>
        </li>
        @can('isMaster')
            <li>
                <button class="btn btn-toggle align-items-center rounded collapsed" data-bs-toggle="collapse" data-bs-target="#admin-users-collapse" aria-expanded="false">
                    <svg class="bi me-2" width="20" height="20"></svg>
                    管理者
                </button>
                <div class="collapse" id="admin-users-collapse">
                    <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                        <li><a href="/admin/admin-users" class="link-dark rounded">管理者一覧</a></li>
                        <li><a href="/admin/admin-users/create" class="link-dark rounded">管理者登録</a></li>
                    </ul>
                </div>
            </li>
            <li>
                <button class="btn btn-toggle align-items-center rounded collapsed" data-bs-toggle="collapse" data-bs-target="#departments-collapse" aria-expanded="false">
                <svg class="bi me-2" width="20" height="20"></svg>
                部署
                </button>
                <div class="collapse" id="departments-collapse">
                    <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                        <li><a href="/admin/departments" class="link-dark rounded">部署一覧</a></li>
                        <li><a href="/admin/departments/create" class="link-dark rounded">部署登録</a></li>
                    </ul>
                </div>
            </li>
            <li>
                <button class="btn btn-toggle align-items-center rounded collapsed" data-bs-toggle="collapse" data-bs-target="#report-approvers-collapse" aria-expanded="false">
                <svg class="bi me-2" width="20" height="20"></svg>
                承認者
                </button>
                <div class="collapse" id="report-approvers-collapse">
                    <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                        <li><a href="/admin/report-approvers" class="link-dark rounded">承認者一覧</a></li>
                    </ul>
                </div>
            </li>
        @endcan
        <li>
            <button class="btn btn-toggle align-items-center rounded collapsed" data-bs-toggle="collapse" data-bs-target="#leave-reports-collapse" aria-expanded="false">
                <svg class="bi me-2" width="20" height="20"></svg>
                休暇届
            </button>
            <div class="collapse" id="leave-reports-collapse">
                <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                    <li><a href="/admin/leave-reports" class="link-dark rounded">休暇届一覧</a></li>
                </ul>
            </div>
        </li>
        <li>
            <button class="btn btn-toggle align-items-center rounded collapsed" data-bs-toggle="collapse" data-bs-target="#overtime-reports-collapse" aria-expanded="false">
                <svg class="bi me-2" width="20" height="20"></svg>
                残業申請
            </button>
            <div class="collapse" id="overtime-reports-collapse">
                <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                    <li><a href="/admin/overtime-reports" class="link-dark rounded">残業申請一覧</a></li>
                </ul>
            </div>
        </li>
        <li>
            <button class="btn btn-toggle align-items-center rounded collapsed" data-bs-toggle="collapse" data-bs-target="#sub-attendance-reports-collapse" aria-expanded="false">
                <svg class="bi me-2" width="20" height="20"></svg>
                振替出勤
            </button>
            <div class="collapse" id="sub-attendance-reports-collapse">
                <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                    <li><a href="/admin/sub-attendance-reports" class="link-dark rounded">振替出勤一覧</a></li>
                </ul>
            </div>
        </li>
        <li>
            <button class="btn btn-toggle align-items-center rounded collapsed" data-bs-toggle="collapse" data-bs-target="#sub-holiday-reports-collapse" aria-expanded="false">
                <svg class="bi me-2" width="20" height="20"></svg>
                振替休暇
            </button>
            <div class="collapse" id="sub-holiday-reports-collapse">
                <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                    <li><a href="/admin/sub-holiday-reports" class="link-dark rounded">振替休暇一覧</a></li>
                </ul>
            </div>
        </li>
        @can('isNotReadonly')
            <li>
                <button class="btn btn-toggle align-items-center rounded collapsed" data-bs-toggle="collapse" data-bs-target="#service-record-collapse" aria-expanded="false">
                    <svg class="bi me-2" width="20" height="20"></svg>
                    勤怠記録
                </button>
                <div class="collapse" id="service-record-collapse">
                    <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                        <li><a href="/admin/service-record/import" class="link-dark rounded">勤怠記録インポート</a></li>
                    </ul>
                </div>
            </li>
        @endcan
        <li>
            <button class="btn btn-toggle align-items-center rounded collapsed" data-bs-toggle="collapse" data-bs-target="#calendar-collapse" aria-expanded="false">
                <svg class="bi me-2" width="20" height="20"></svg>
                カレンダー
            </button>
            <div class="collapse" id="calendar-collapse">
                <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                    <li><a href="/admin/calendar" class="link-dark rounded">カレンダー表示</a></li>
                    @can('isNotReadonly')
                        <li><a href="/admin/calendar/import" class="link-dark rounded">カレンダーインポート</a></li>
                    @endcan
                </ul>
            </div>
        </li>
        <hr>
    </ul>
</div>
<script type="text/javascript">
    let url = new URL(window.location.href);
    let collapse = document.querySelector('#' + url.pathname.split('/')[2] + '-collapse');
    if (collapse) new bootstrap.Collapse(collapse);
</script>