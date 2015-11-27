<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
    <!-- meta name="viewport" content="width=device-width, initial-scale=1.0" -->
	<title>Welcome to Hell</title>
    <script src="/static/js/jquery-1.11.3.min.js"></script>
    <script src="/static/js/shoes.util.js"></script>
</head>
<body>

<form id="efs_form">
이메일 <input type="text" name="email1" placeholder="이메일" value="" maxlength="20">@<input type="text" name="email2" placeholder="이메일2" value="" maxlength="20"><br>
입력한 이메일로 회원 가입 URL 이 발송됩니다.<br>
<button type="button" id="efs_regist">회원가입 메일받기</button>
</form>

<script>
$(document).ready(function() {
    $('#efs_regist').click(function() {
        var url = '/welcome/ax_set_emailforsign';
        var data = $('#efs_form').serialize();
        ax_post(url, data, function(ret) {
            console.log(ret);
        });
    });
});
</script>
</body>
</html>
