<form class="form-horizontal" id="question_form" onsubmit="return false;">
    <div class="form-group">
        <label for="question" class="col-sm-2 control-label">질문</label>
        <div class="col-sm-10">
            <textarea class="form-control" name="question" id="question" rows="3" maxlength="200" placeholder="궁금한게 있나요?"></textarea>
        </div>
    </div>
    <div class="form-group">
        <label for="main_start" class="col-sm-2 control-label">메인 노출 시작일</label>
        <div class="col-sm-10">
            <select name="main_start" id="main_start" class="form-control">
                <option value="">안함</option>
<?
for($i=0;$i<10;$i++) {
    $d = date('Y-m-d 00:00:00', strtotime("+".$i." day"));
?>
                <option value="<?=$d?>"><?=$d?></option>
<?
}
?>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label for="main_end" class="col-sm-2 control-label">메인 노출 종료일</label>
        <div class="col-sm-10">
            <select name="main_end" id="main_end" class="form-control">
                <option value="">안함</option>
<?
for($i=0;$i<20;$i++) {
    $d = date('Y-m-d 23:59:59', strtotime("+".$i." day"));
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
            <button type="button" id="regist" class="btn btn-warning">이 질문에 응답해라!</button>
        </div>
    </div>
</form>
<br>
<ul class="list-group" data-role="listview" data-inset="true" id="question_list">
</ul>
<button type="button" id="more" class="glyphicon glyphicon-chevron-down btn btn-default btn-sm" style="width:100%"> 더보기</button>
<script type="text/javascript">
var page_num = 1;
var timer    = setInterval(function () { scrollOK = true; }, 100);
var scrollOK = true;

$(document).ready(function(){
    $('#regist').click(function() {
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
        var url = "/question/ax_set_question";
        var data = $('#question_form').serialize();
        ax_post(url, data, function(ret) {
            if(ret.result == 'ok') {
                self.location.reload();
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
                var col = 'black';
                html += '<li class="list-group-item">';
                html += data.que_srl;
                if(data.main_start < '<?=YMD_HIS?>' && data.main_end > '<?=YMD_HIS?>') {
                    col = 'crimson';
                } else if(data.main_end < '<?=YMD_HIS?>') {
                    col = 'gray';
                } else if(data.main_start > '<?=YMD_HIS?>') {
                    col = 'darkorange';
                }
                if(data.main_start) {
                    html += "<br>"+data.main_start+" ~ "+data.main_end;
                    html += "<b>";
                }
                html += "<br><span style=\"color:"+col+";\">"+data.question+"</span>";
                if(data.main_start) {
                    html += "</b></span>";
                }
                html += '</li>';
            }
            $('#question_list').append(html);
        } else {
            $('#more').hide();
        }
    });
}
</script>
