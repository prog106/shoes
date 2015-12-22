    <div class="row">
    <form class="form-horizontal" id="user_form" onsubmit="return false;">
    <input type="hidden" name="mem" value="<?=$info['mem_srl']?>">
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10" style="left:50%;margin-left:-30px;">
                <img src="<?=$info['mem_picture']?>" class="img-circle">
                <!-- input type="file" name="picture" accept="image/jpg|gif|png" capture="camera" -->
            </div>
        </div>
        <div class="form-group">
            <label for="mem_name" class="col-sm-2 control-label">닉네임</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="mem_name" name="mem_name" placeholder="닉네임" value="<?=$info['mem_name']?>">
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <h5><strong><?=strtoupper($info['mem_pwd'])?></strong><?=($info['mem_pwd']==='facebook')?"을":"를"?> 통해 가입(<?=$info['create_datetime']?>)하셨습니다.</h5>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="button" id="save" class="btn btn-default">저장하기</button>
            </div>
        </div>
    </form>
    </div>

<script>
$(document).ready(function() {
    $('#save').click(function() {
        if(!$('#mem_name').val()) {
            alert('닉네임을 입력해 주세요.');
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
