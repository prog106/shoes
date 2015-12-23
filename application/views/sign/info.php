    <form class="form-horizontal" id="user_form" onsubmit="return false;">
    <input type="hidden" name="mem" value="<?=$info['mem_srl']?>">
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10" style="text-align:center">
                <img src="<?=$info['mem_picture']?>" class="img-circle">
                <!-- input type="file" name="picture" accept="image/jpg|gif|png" capture="camera" -->
            </div>
        </div>
        <div class="form-group">
            <label for="mem_name" class="col-sm-2 control-label" style="color:crimson">닉네임(필수)</label>
            <div class="col-sm-10">
                <div class="input-group">
                    <input type="text" class="form-control" id="mem_name" name="mem_name" placeholder="닉네임(필수)" value="<?=$info['mem_name']?>">
                    <span class="input-group-btn">
                        <button class="btn btn-default" type="button" id="nickcheck">중복체크</button>
                    </span>
                </div>
                <span id="nickcheckst"></span>
            </div>
        </div>
        <div class="form-group">
            <label for="mem_email" class="col-sm-2 control-label">이메일(선택)</label>
            <div class="col-sm-10">
                <input type="email" class="form-control" id="mem_email" name="mem_email" placeholder="이메일" value="<?=$info['mem_email']?>">
            </div>
        </div>
        <div class="form-group">
            <label for="regist" class="col-sm-2 control-label">가입일</label>
            <div class="col-sm-10">
                <?=$info['create_datetime']?>
            </div>
        </div>
        <div class="form-group">
            <label for="from" class="col-sm-2 control-label">가입경로</label>
            <div class="col-sm-10">
                <?=strtoupper($info['mem_pwd'])?>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="button" id="save" class="btn btn-default">저장하기</button>
            </div>
        </div>
    </form>

<script>
$(document).ready(function() {
    var nickauth = true;
    $('#nickcheck').click(function() {
        if(!$('#mem_name').val()) {
            alert('닉네임을 입력해 주세요.');
            return false;
        }
        var url = '/sign/ax_get_nickname';
        var data = {nickname:$('#mem_name').val()};
        ax_post(url, data, function(ret) {
            if(ret.result == 'ok') {
                $('#nickcheckst').css('color', '').text('사용이 가능한 닉네임입니다.');
                nickauth = true;
            } else {
                $('#nickcheckst').css('color', 'crimson').text('이미 사용중인 닉네임입니다.');
                nickauth = false;
            }
        });
    });
    $('#mem_name').change(function() {
        $('#nickcheckst').css('color', 'crimson').text('닉네임 중복체크 해주세요.');
        nickauth = false;
    });
    $('#save').click(function() {
        if(!nickauth) {
            alert('닉네임 중복체크를 해 주세요.');
            return false;
        }
        var url = '/sign/ax_set_info';
        var data = $('#user_form').serialize();
        ax_post(url, data, function(ret) {
            if(ret.result == 'ok') {
                alert('저장되었습니다.');
                window.location.reload();
            } else {
                if(ret.msg == 'loginerror') {
                    alert('다시 로그인 후 이용해 주세요.');
                    window.location.href='/sign/logout';
                } else {
                    alert(ret.msg);
                }
            }
        });
    });
});
</script>
