가입 가능 시간 : <?=$end?>
<form id="sign_form">
<input type="hidden" name="efs_srl" value="<?=$efs_srl?>">
* 이메일 <input type="text" name="email1" placeholder="이메일" value="<?=$email1?>" maxlength="20" readonly>
@<input type="text" name="email2" placeholder="이메일2" value="<?=$email2?>" maxlength="20" readonly><br>
* 이름 <input type="text" name="name" placeholder="이름" value="" maxlength="20" id="name"><br>
* 비밀번호 <input type="password" name="pwd" value="" id="pwd1"><br>
* 비밀번호 다시 <input type="password" name="pwd1" value="" id="pwd2"><br>
<button type="button" id="sign_regist">회원가입</button>
</form>

<script>
$(document).ready(function() {
    $('#sign_regist').click(function() {
        var name = $('#name').val();
        var pwd1 = $('#pwd1').val();
        var pwd2 = $('#pwd2').val();
        if(name.length < 2) {
            alert('이름 입력해요');
            return false;
        }
        if(pwd1.length < 6) {
            alert('비밀번호가 짧아요 6자 이상');
            return false;
        }
        if(pwd1 != pwd2) {
            alert('비밀번호가 달라요');
            return false;
        }
        var url = '/welcome/ax_set_sign';
        var data = $('#sign_form').serialize();
        ax_post(url, data, function(ret) {
            if(ret.result == 'ok') {
                alert('회원가입 완료');
                window.location.href='/welcome/login';
            } else {
                alert(ret.msg);
            }
            return false;
        });
    });
});
</script>
