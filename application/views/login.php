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

<form id="login_form">
이메일 <input type="text" name="email" placeholder="이메일" value="" maxlength="20" id="email"><br>
비밀번호 <input type="password" name="pwd" value="" maxlength="20" id="pwd">
<button type="button" id="login">로그인</button>
</form>

<script>
$(document).ready(function() {
    $('#login').click(function() {
        var email = $('#email').val();
        var pwd = $('#pwd').val();
        if(email.length < 4 || pwd.length < 6) {
            alert('로그인 정보를 다시 입력해 주세요.');
            return false;
        }
        var url = '/welcome/ax_get_login';
        var data = $('#login_form').serialize();
        ax_post(url, data, function(ret) {
            if(ret.result == 'ok') {
                window.location.href='/';
            } else {
                alert(ret.msg);
            }
        });
    });
});
</script>
</body>
</html>
