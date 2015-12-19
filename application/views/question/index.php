<form class="form-horizontal" id="question_form" onsubmit="return false;">
    <input type="hidden" name="que_srl" id="que_srl" value="0">
    <div class="form-group">
        <label for="question" class="col-sm-2 control-label">질문을 올려주세요. <span style="color:crimson">주의! 질문은 하루 한개만 가능합니다.</font></label>
        <div class="col-sm-10">
            <textarea class="form-control" name="question" id="question" rows="3" maxlength="200" placeholder="질문을 입력해 주세요."></textarea>
        </div>
    </div>
<?
if($member['level'] === 'manager') {
?>
    <div class="form-group">
        <!-- label for="main_start" class="col-sm-2 control-label">메인 노출 시작일</label -->
        <div class="col-sm-offset-2 col-sm-10">
            <select name="start" id="start" class="form-control" style="color:crimson">
                <option value="">노출 시작 - 필수</option>
                <option value="<?=date('Y-m-d')?>">오늘부터</option>
<?
    for($i=1;$i<11;$i++) {
        $d = date('Y-m-d', strtotime("+".$i." day"));
?>
                <option value="<?=$d?>"><?=$d?></option>
<?
    }
?>
            </select>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <select name="main_start" id="main_start" class="form-control">
                <option value="">메인 노출 시작 - 필수아님</option>
<?
    for($i=0;$i<10;$i++) {
        $d = date('Y-m-d', strtotime("+".$i." day"));
?>
                <option value="<?=$d?> 00:00:00"><?=$d?></option>
<?
    }
?>
            </select>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <select name="main_end" id="main_end" class="form-control">
                <option value="">메인 노출 종료일 - 필수아님</option>
<?
    for($i=0;$i<20;$i++) {
        $d = date('Y-m-d', strtotime("+".$i." day"));
?>
                <option value="<?=$d?> 23:59:59"><?=$d?></option>
<?
    }
?>
            </select>
        </div>
    </div>
<?
}
?>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <button type="button" id="regist" class="btn btn-success">질문 올리기</button>
            <button type="button" id="cancel" class="btn btn-danger" style="display:none;">취소</button>
        </div>
    </div>
</form>
<br>
<table class="table table-condensed" id="question_list">
    <tr>
        <th width="5%"></th>
        <th width="80%">내가 올린 질문</th>
        <th width="10%">수정</th>
    </tr>
</table>
<button type="button" id="more" class="glyphicon glyphicon-chevron-down btn btn-default btn-sm" style="width:100%"> 더보기</button>
<script type="text/javascript">
var page_num = 1;
var timer    = setInterval(function () { scrollOK = true; }, 100);
var scrollOK = true;

$(document).ready(function(){
    $('#regist').click(function() {
<?
if($member['level'] === 'manager') {
?>
        if(!$('#start option:selected').val()) {
            alert('노출 시작일을 선택해 주세요.');
            return false;
        }
        if(!$('#main_start option:selected').val() && $('#main_end option:selected').val()) {
            alert('시작일과 종료일 다시 체크해 주세요');
            return false;
        }
        if($('#main_start option:selected').val() && !$('#main_end option:selected').val()) {
            alert('시작일과 종료일 다시 체크해 주세요');
            return false;
        }
        if($('#main_start option:selected').val() > $('#main_end option:selected').val()) {
            alert('시작일과 종료일 다시 체크해 주세요');
            return false;
        }
<?
}
?>
        var url = "/question/ax_set_question";
        var data = $('#question_form').serialize();
        ax_post(url, data, function(ret) {
            if(ret.result == 'ok') {
                window.location.reload();
            } else {
                alert(ret.msg);
            }
        });
    });

    $('#more').click(function() {
        get_question_list(page_num+1);
    });
/*    $(window).on('scroll', function () {
        if (scrollOK) {
            scrollOK = false;
            if ($(this).scrollTop() + $(this).height() >= ($(document).height() - 5)) {
                get_question_list(page_num+1);
            }
        }
    });
*/
    get_question_list(1);
    $('#cancel').click(function() {
        if(!confirm('취소 하시겠습니까?')) return false;
        $('#cancel').hide();
        $('#regist').text('질문 올리기');
        $('#que_srl').val(0);
        $('#question').val('');
    });
});

function get_question_list(page_val){
    page_num = page_val;
    url = '/question/ax_get_question';
    data = {page:page_num};
    ax_post(url, data, function(d) {
        if(d.recordsTotal > 0){
            var len = d.data.length;
            var html = '';
            for(var i = 0 ; i < len ; i++){
                var data = d.data[i];
                html += data;
<?/*
                var col = 'black';
                html += '<li class="list-group-item">';
                html += data.que_srl;
                console.log(data.start);
                if(data.start >= '<?=date('Y-m-d')?>') {
                    if(data.main_start < '<?=YMD_HIS?>' && data.main_end > '<?=YMD_HIS?>') {
                        col = 'crimson';
                    } else if(data.main_start > '<?=YMD_HIS?>') {
                        col = 'darkorange';
                    }
                }
                if(data.main_start) {
                    html += "<br>"+data.main_start+" ~ "+data.main_end;
                }
                if(data.status == 'delete') {
                    html += '<span style="text-decoration:line-through;">';
                }
                html += "<br><span style=\"color:"+col+";\">"+data.question+"</span>";
                if(data.status == 'delete') {
                    html += '</span>';
                }
                if(data.main_start) {
                    html += "</span>";
                }
                html += '<br>댓글 '+data.respond+' 좋아요 '+data.likes;
                html += '</li>';
*/?>
            }
            $('#question_list tbody').append(html);
        } else {
            $('#more').hide();
        }
        $('.link').click(function() { window.location.href=$(this).data('link'); });
        $('.modthis').click(function() {
            if(!confirm('질문을 수정하시겠습니까?')) return false;
            $('#question').val($('#question'+$(this).data('que')).text());
            $('#que_srl').val($(this).data('que'));
            $('#regist').text('질문 수정하기');
            $('#cancel').show();
        });
        $('.delthis').click(function() {
            if(!confirm('작성된 응답과 좋아요 정보가 삭제됩니다.\n\n정말 삭제하시겠습니까?')) return false;
            var q = $(this).data('que');
            var url = "/question/ax_set_question_del";
            var data = {que:q};
            ax_post(url, data, function(ret) {
                if(ret.result == 'ok') {
                    $('#delthis'+q).parent('td').parent('tr').remove();
                } else {
                    alert(ret.msg);
                }
            });
        });
    });
}
</script>
