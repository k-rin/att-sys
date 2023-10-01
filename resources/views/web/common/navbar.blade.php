<ul class="nav nav-tabs my-3">
    <li class="nav-item">
        <a @class([
            "nav-link" => true,
            "active" => $page == "user",
        ]) href="/departments/users/{{ $user->id }}">基本資料</a>
    </li>
    <li class="nav-item">
        <a @class([
            "nav-link" => true,
            "active" => $page == "service-record",
        ]) href="/departments/users/{{ $user->id }}/service-records">出勤紀錄列表</a>
    </li>
    <li class="nav-item">
        <a @class([
            "nav-link" => true,
            "active" => $page == "leave-report",
        ]) href="/departments/users/{{ $user->id }}/leave-reports">假單列表</a>
    </li>
    <li class="nav-item">
        <a @class([
            "nav-link" => true,
            "active" => $page == "overtime-report",
        ]) href="/departments/users/{{ $user->id }}/overtime-reports">加班申請列表</a>
    </li>
    <li class="nav-item">
        <a @class([
            "nav-link" => true,
            "active" => $page == "sub-attendance-report",
        ]) href="/departments/users/{{ $user->id }}/sub-attendance-reports">假日上班申請列表</a>
    </li>
    <li class="nav-item">
        <a @class([
            "nav-link" => true,
            "active" => $page == "sub-holiday-report",
        ]) href="/departments/users/{{ $user->id }}/sub-holiday-reports">補假申請列表</a>
    </li>
</ul>