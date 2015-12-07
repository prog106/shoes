<form id="login_form">
이메일 <input type="text" name="email" placeholder="이메일" value="" maxlength="20" id="email"><br>
비밀번호 <input type="password" name="pwd" value="" maxlength="20" id="pwd"><br>
<button type="button" id="login">로그인</button>
</form>

<div class="container">
    <form class="form-signin">
        <?php if (@$user_profile):  // call var_dump($user_profile) to view all data ?>
            <div class="row">
                <div class="col-lg-12 text-center">
                    <!-- img class="img-thumbnail" data-src="holder.js/140x140" alt="140x140" src="https://graph.facebook.com/<?=$user_profile['id']?>/picture?type=large" style="width: 140px; height: 140px;" -->
                    <h2><?=$user_profile['name']?></h2>
                    <a href="#" class="btn btn-lg btn-primary btn-block" id="logout" role="button">Logout</a>
                </div>
            </div>
        <?php else: ?>
            <a href="<?= $login_url ?>" class="btn btn-lg btn-primary btn-block" role="button">Facebook Login</a>
        <?php endif; ?>
    </form>
</div> <!-- /container -->

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
    $('#logout').click(function() {
        var url = '/welcome/ax_get_logout';
        ax_post(url, null, function(ret) {
            if(ret.result == 'ok') {
                self.location.reload();
            } else {
                alert(ret.msg);
            }
        });
    });
});
</script>
