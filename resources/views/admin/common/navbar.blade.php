<ul class="nav nav-tabs my-3">
    <li class="nav-item">
        <a @class([
            "nav-link" => true,
            "active" => $page == "user"
        ]) href="/admin/users/{{ $id }}">プロフィール</a>
    </li>
    <li class="nav-item">
        <a @class([
            "nav-link" => true,
            "active" => $page == "service-record"
        ]) href="/admin/users/{{ $id }}/service-records">勤怠記録一覧</a>
    </li>
    <li class="nav-item">
        <a @class([
            "nav-link" => true,
            "active" => $page == "leave-report"
        ]) href="/admin/users/{{ $id }}/leave-reports">休暇届一覧</a>
    </li>
    <li class="nav-item">
        <a @class([
            "nav-link" => true,
            "active" => $page == "overtime-report",
        ]) href="/admin/users/{{ $id }}/overtime-reports">残業申請一覧</a>
    </li>
    <li class="nav-item">
        <a @class([
            "nav-link" => true,
            "active" => $page == "sub-attendance-report"
        ]) href="/admin/users/{{ $id }}/sub-attendance-reports">振替出勤一覧</a>
    </li>
    <li class="nav-item">
        <a @class([
            "nav-link" => true,
            "active" => $page == "sub-holiday-report"
        ]) href="/admin/users/{{ $id }}/sub-holiday-reports">振替休暇一覧</a>
    </li>
    <li class="nav-item">
        <a @class([
            "nav-link" => true,
            "active" => $page == "attendance-time"
        ]) href="/admin/users/{{ $id }}/attendance-times">始業・終業時間</a>
    </li>
    <li class="nav-item">
        <a @class([
            "nav-link" => true,
            "active" => $page == "calendar"
        ]) href="/admin/users/{{ $id }}/calendar">個人カレンダー</a>
    </li>
</ul>