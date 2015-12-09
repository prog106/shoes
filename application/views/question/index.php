<form id="question_form">
    <div class="form-group">
        <label for="question">질문</label>
        <textarea class="form-control" name="question" id="question" rows="3" maxlength="200" placeholder="궁금한게 있나요?"></textarea>
    </div>
    <button type="button" id="regist" class="btn btn-default">응답해라</button>
</form>
<br>
<ul class="list-group" data-role="listview" data-inset="true" id="question_list">
</ul>
<script type="text/javascript">
var page_num = 1;
var timer    = setInterval(function () { scrollOK = true; }, 100);
var scrollOK = true;

$(document).ready(function(){
    $('#regist').click(function() {
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

    $(window).on('scroll', function () {
        if (scrollOK) {
            scrollOK = false;
            if ($(this).scrollTop() + $(this).height() >= ($(document).height() - 5)) {
                get_question_list(page_num+1);
            }
        }
    });

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
                html += '<li class="list-group-item">';
                html += data.que_srl+". "+data.question;
                html += '</li>';
            }
            $('#question_list').append(html);
        }
    });
}
</script>
