<!-- View stored in resources/views/emails/admin/password_reset.blade.php -->

<html>
    <head>
        <meta charset="UTF-8">
    </head>
    <body>
        <br>
        <br>
        此郵件是為了重置密碼，由Attendance system自動發送。<br>
        <br>
        密碼重置URL如下<br>
        <a href="{{ $url }}">{{ $url }}</a><br>
        <br>
        URL有效期限為將發送郵件後的3天。<br>
        <br>
    </body>
</html>
