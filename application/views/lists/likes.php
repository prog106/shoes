<? $this->load->view('/lists/tbar', array('recent' => '', 'respond' => '', 'likes' => 'on'), 'true'); ?>
    <div class="row">
        <div class="col-sm-12" id="recent_list">
        </div>
    </div>
    <button type="button" id="more" class="glyphicon glyphicon-chevron-down btn btn-default btn-sm" style="width:100%"> 더보기</button>
<script>
var page_num = 1;
var timer    = setInterval(function () { scrollOK = true; }, 100);
var scrollOK = true;

$(document).ready(function() {
    $('#more').click(function() {
        get_question_list(page_num+1);
    });
    get_question_list(1);
    function get_question_list(page_val){
        page_num = page_val;
        url = '/lists/ax_get_lists';
        data = {page:page_num,tp:'like'};
        ax_post(url, data, function(d) {
            if(d.recordsTotal > 0){
                var len = d.data.length;
                var html = '';
                for(var i = 0 ; i < len ; i++){
                    var data = d.data[i];
                    html += data;
                }
                $('#recent_list').append(html);
            } else {
                $('#more').hide();
            }
            $('.link').click(function() { window.location.href=$(this).data('link'); });
            function likes(que, already) {
                $('#like'+que).css('color', 'darkorange');
                $('#likethis'+que).data('status', true);
                if(!already) {
                    $('#likecount'+que).text(parseInt($('#likecount'+que).text())+1);
                }
            }
            function dontlikes(que, already) {
                $('#like'+que).css('color', 'gray');
                $('#likethis'+que).data('status', false);
                if(!already) {
                    $('#likecount'+que).text(parseInt($('#likecount'+que).text())-1);
                }
            }
            $('.likethis').click(function() {
                if($(this).data('status')) {
                    var st = 'dontlike';
                } else {
                    var st = 'like';
                }
                var url = '/like/ax_set_'+st;
                var que = $(this).data('question');
                var data = {question:que}
                ax_post(url, data, function(ret) {
                    if(ret.result == 'ok') {
                        already = true;
                        if(ret.data != 'already') already = false;
                        if(st == 'like') likes(que, already);
                        else if(st == 'dontlike') dontlikes(que, already);
                    } else {
                        alert(ret.msg);
                    }
                });
            });
        });
    }
});
</script>
