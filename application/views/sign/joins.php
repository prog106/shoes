    <div class="row">
    <form class="form-horizontal" id="joins_form" onsubmit="return false;">
    <input type="hidden" name="mem" value="<?=$sign['sign_srl']?>">
    <input type="hidden" name="from" value="<?=$sign['sign_type']?>">
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10" style="left:50%;margin-left:-30px;">
                <img src="<?=$sign['sign_picture']?>" class="img-circle">
                <input type="hidden" name="picture" value="<?=$sign['sign_picture']?>">
            </div>
        </div>
        <div class="form-group">
            <label for="mem_name" class="col-sm-2 control-label" style="color:crimson">닉네임(필수)</label>
            <div class="col-sm-10">
                <div class="input-group">
                    <input type="text" class="form-control" id="mem_name" name="mem_name" placeholder="닉네임(필수)" value="<?=$sign['sign_name']?>">
                    <span class="input-group-btn">
                        <button class="btn btn-default" type="button" id="nickcheck">중복체크</button>
                    </span>
                </div>
                <span id="nickcheckst">닉네임 중복체크 해주세요.</span>
            </div>
        </div>
        <div class="form-group">
            <label for="mem_email" class="col-sm-2 control-label">이메일(선택)</label>
            <div class="col-sm-10">
                <input type="email" class="form-control" id="mem_email" name="mem_email" placeholder="이메일" value="<?=$sign['sign_email']?>">
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <h5><strong><?=strtoupper($sign['sign_type'])?></strong><?=($sign['sign_type']==='facebook')?"을":"를"?> 통해 회원 가입을 하고 계십니다.</h5>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="button" id="save" class="btn btn-default">가입하기</button>
            </div>
        </div>
    </form>
    </div>

<script>
$(document).ready(function() {
    var nickauth = false;
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
    }).keyup(function() {
        $('#nickcheckst').css('color', '').text('닉네임 중복체크 해주세요.');
        nickauth = false;
    });
    $('#save').click(function() {
        if(!nickauth) {
            alert('닉네임 중복체크를 해 주세요.');
            return false;
        }
        var url = '/sign/ax_set_sns_sign';
        var data = $('#joins_form').serialize();
        ax_post(url, data, function(ret) {
            if(ret.result == 'ok') {
                alert('회원 가입이 완료 되었습니다.');
            } else {
                alert(ret.msg);
            }
            window.location.reload();
        });
    });
});
</script>
