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

<?
if(!empty($member)) {
?>
로그인 정보<br>
<?=$member['mem_email']?> | <?=$member['mem_name']?><br>
<button type="button" id="logout">로그아웃</button>
<?
} else {
?>
<a href="/welcome/sign">회원가입</a> | <a href="/welcome/login">로그인</a>
<?
}
?>
<script>
$(document).ready(function() {
    $('#logout').click(function() {
        if(!confirm('로그아웃 하시겠습니까?')) {
            return false;
        }
        var url = '/welcome/ax_get_logout';
        ax_post(url, null, function(ret) {
            if(ret.result == 'ok') {
                alert('로그아웃 되었습니다.');
                self.location.reload();
            } else {
                alert(ret.msg);
            }
        });
    });
});
</script>
</body>
</html>
