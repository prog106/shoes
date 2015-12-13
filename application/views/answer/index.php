<form id="answer_form">
<? debug($question['question']); ?>
<? debug($question['create_at']); ?>
<input type="hidden" name="question" id="question" value="<?=$question['que_srl']?>">
    <div class="form-group">
        <label for="answer">답글</label>
        <input type="text" class="form-control" name="answer" id="answer" maxlength="200" placeholder="<?=(empty($member))?"로그인 후 이용해 주세요.":"답변을 해주세요!"?>"<?=(empty($member))?" READONLY":""?>></textarea>
    </div>
    <button type="button" id="regist" class="btn btn-default">응답했다!</button>
</form>
<br>
<ul class="list-group" data-role="listview" data-inset="true" id="answer_list">
</ul>
<script type="text/javascript">
var page_num = 1;
var timer    = setInterval(function () { scrollOK = true; }, 100);
var scrollOK = true;

$(document).ready(function(){
    $('#regist').click(function() {
        var url = "/answer/ax_set_answer";
        var data = $('#answer_form').serialize();
        ax_post(url, data, function(ret) {
            if(ret.result == 'ok') {
                self.location.reload();
            } else {
                alert(ret.msg);
            }
        });
    });

    $(window).on('scroll', function () {
        if (scrollOK) {
            scrollOK = false;
            if ($(this).scrollTop() + $(this).height() >= ($(document).height() - 5)) {
                console.log($(this).scrollTop());
                console.log($(this).height());
                console.log($(document).height());
                get_answer_list(page_num+1);
            }
        }
    });

    get_answer_list(1);
});

function get_answer_list(page_val){
    page_num = page_val;
    url = '/answer/ax_get_answer';
    data = {page:page_num,question:$('#question').val()};
    ax_post(url, data, function(d) {
        if(d.recordsTotal > 0){
            var len = d.data.length;
            var html = '';
            for(var i = 0 ; i < len ; i++){
                var data = d.data[i];
                html += '<li class="list-group-item">';
                html += data.ans_srl+". "+data.answer;
                html += '</li>';
            }
            $('#answer_list').append(html);
        }
    });
}
</script>
