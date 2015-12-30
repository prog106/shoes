<? $this->load->view('/lists/tbar', array('recent' => '', 'respond' => 'on', 'likes' => ''), 'true'); ?>
<link rel="stylesheet" href="/static/css/swiper.min.css">
    <div class="row">
        <div class="col-sm-12">
            <div class="swiper-container">
                <div class="swiper-wrapper">
<?
foreach($list as $k => $v) {
?>
                    <div class="swiper-slide">
                        <div class="media" style="margin-top:-30px;">
                            <div class="media-body">
                                <img src="<?=(!empty($v['mem_picture'] && $v['mem_level'] !== 'manager')?$v['mem_picture']:"/static/image/komment.png")?>" width="35" class="swipeimg"><br>
                                <span style="font-size:11px;line-height:25px;"><?=($v['mem_level'] !== 'manager')?$v['mem_name']:"Komment"?></span>
                                <h4 class="media-heading link" style="line-height:25px;cursor:pointer;" data-link="/answer/view/<?=$v['que_srl']?>"><?=convert_hashtag($v['question'])?></h4>
                                <span style="font-size:11px;">응답 <?=number_format($v['respond'])?> &nbsp; 좋아요 <span id="likecount<?=$v['que_srl']?>"><?=number_format($v['likes'])?></span></span>
                            </div>
                        </div>
                    </div>
<?
}
?>
                </div>
                <!-- Add Pagination -->
                <div class="swiper-pagination"></div>
            </div>
        </div>
        <div class="col-sm-12" id="respond_list" style="margin-top:15px;">
        </div>
    </div>
    <button type="button" id="more" class="glyphicon glyphicon-chevron-down btn btn-default btn-sm" style="width:100%"> 더보기</button>
<script>
var page_num = 1;
var timer    = setInterval(function () { scrollOK = true; }, 100);
var scrollOK = true;
var swiper = new Swiper('.swiper-container', {
    pagination: '.swiper-pagination',
    effect: 'cube',
    grabCursor: true,
    cube: {
        shadow: true,
        slideShadows: true,
        shadowOffset: 10,
        shadowScale: 0.64
    }
});
$(document).ready(function() {
    $('#more').click(function() {
        get_question_list(page_num+1);
    });
    get_question_list(1);
    function get_question_list(page_val){
        page_num = page_val;
        url = '/lists/ax_get_lists';
        data = {page:page_num,tp:'respond'};
        ax_post(url, data, function(d) {
            if(d.recordsTotal > 0){
                var len = d.data.length;
                var html = '';
                for(var i = 0 ; i < len ; i++){
                    if(i > 4) {
                        var data = d.data[i];
                        html += data;
                    }
                }
                $('#respond_list').append(html);
                if(d.recordsTotal < 20) {
                    $('#more').hide();
                }
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
