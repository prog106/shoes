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
가입 가능 시간 : <?=$end?>
<form id="sign_form">
이메일 <input type="text" name="email1" placeholder="이메일" value="<?=$email1?>" maxlength="20" readonly>
@<input type="text" name="email2" placeholder="이메일2" value="<?=$email2?>" maxlength="20" readonly><br>
비밀번호 <input type="password" name="pwd" value="" id="pwd1"><br>
비밀번호 다시 <input type="password" name="pwd1" value="" id="pwd2"><br>
<button type="button" id="sign_regist">회원가입</button>
</form>

<script>
$(document).ready(function() {
    $('#sign_regist').click(function() {
        var pwd1 = $('#pwd1').val();
        var pwd2 = $('#pwd2').val();
        if(pwd1.length < 6) {
            alert('짧아요 6자 이상');
            return false;
        }
        if(pwd1 != pwd2) {
            alert('달라요');
            return false;
        }
        var url = '/welcome/ax_set_sign';
        var data = $('#sign_form').serialize();
        ax_post(url, data, function(ret) {
            console.log(ret);
        });
    });
});
</script>
</body>
</html>
