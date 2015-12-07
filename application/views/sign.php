<form id="efs_form">
이메일 <input type="text" name="email1" placeholder="이메일" value="" maxlength="20"> @ <input type="text" name="email2" placeholder="이메일2" value="" maxlength="20"><br>
입력한 이메일로 회원 가입 URL 이 발송됩니다.<br>
<button type="button" id="efs_regist">회원가입 메일받기</button><br>
<span id="url">이곳에 URL이 나옵니다.</span>
</form>

<script>
$(document).ready(function() {
    $('#efs_regist').click(function() {
        var url = '/welcome/ax_set_emailforsign';
        var data = $('#efs_form').serialize();
        ax_post(url, data, function(ret) {
            if(ret.result == 'ok') {
                alert('메일이 발송되었습니다.');
                $('#url').html(ret.url);
            } else {
                alert(ret.msg);
            }
        });
    });
});
</script>
