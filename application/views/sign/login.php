    <br>
    <!-- form id="login_form">
    이메일 <input type="text" name="email" placeholder="이메일" value="" maxlength="20" id="email"><br>
    비밀번호 <input type="password" name="pwd" value="" maxlength="20" id="pwd"><br>
    <button type="button" id="login">로그인</button>
    </form -->
    <a href="/sign/facebooklogin" class="btn btn-lg btn-primary btn-block" role="button">Facebook Login</a>
    <br>
    <a href="https://kauth.kakao.com/oauth/authorize?client_id=6659ded339e34f9bcc811e56f4a955aa&redirect_uri=http://shoes.prog106.indoproc.xyz/sign/kakaologin&response_type=code" class="btn btn-lg btn-warning btn-block" role="button" id="custom-login-btn">Kakao Login</a>
    <br>
    <a href="javascript:;" class="btn btn-lg btn-success btn-block" role="button" id="nologin">둘러보기</a>

<script src="/static/js/kakao.min.js"></script>
<script>
<?/*
// 사용할 앱의 JavaScript 키를 설정해 주세요.
Kakao.init('6c745ff7fa2422e6fe5ba3411fa5efcb');
function kakaologin() {
// 로그인 창을 띄웁니다.
    Kakao.Auth.login({
        success: function(authObj) {
            Kakao.API.request({
                url: '/v1/user/me',
                success: function(res) {
                    var url = '/sign/ax_set_kakao';
                    var data = {'id':res.id, 'name':res.properties.nickname, 'picture':res.properties.thumbnail_image};
                    ax_post(url, data, function(ret) {
                        if(ret.result == 'ok') {
                            window.location.href='/';
                        } else {
                            if(ret.msg == 'joins') {
                                window.location.replace('/sign/joins');
                            } else {
                                alert(ret.msg);
                            }
                        }
                    });
                },
                fail: function(error) {
                    alert(JSON.stringify(error))
                }
            });
        },
        fail: function(err) {
            alert(JSON.stringify(err))
        }
    });
}
*/?>
$(document).ready(function() {
    $('#nologin').click(function() {
        var url = '/sign/ax_set_nologin';
        var data = [];
        ax_post(url, data, function(ret) {
            window.location.href='/';
        });
    });
    $('#login').click(function() {
        var email = $('#email').val();
        var pwd = $('#pwd').val();
        if(email.length < 4 || pwd.length < 6) {
            alert('로그인 정보를 다시 입력해 주세요.');
            return false;
        }
        var url = '/sign/ax_get_login';
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
<?/*
function facebooklogin() {
    window.open('http://shoes.prog106.indoproc.xyz/sign/facebooklogin', 'facebook', 'width=600, height=600');
}*/?>
</script>
