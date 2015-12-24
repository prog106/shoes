<? $this->load->view('/lists/tbar', array('recent' => '', 'respond' => '', 'likes' => ''), 'true'); ?>
    <div class="row">
        <div class="col-sm-12">
<?
foreach($list as $k => $v) {
?>
            <div class="media">
                <div class="media-left" style="padding-top:5px;">
                    <a href="#">
                        <img class="media-object" src="<?=(!empty($v['mem_picture'] && $v['mem_level'] !== 'manager')?$v['mem_picture']:"/static/image/komment.png")?>" width="35">
                    </a>
                </div>
                <div class="media-body">
                    <span style="font-size:11px;line-height:25px;"><?=($v['mem_level'] !== 'manager')?$v['mem_name']:"Komment"?></span>
                    <span style="font-size:11px;float:right;margin-right:20px;margin-top:5px;"><?=$v['create_at']?></span>
                    <h4 class="media-heading link" style="line-height:25px;cursor:pointer;" data-link="/answer/view/<?=$v['que_srl']?>"><?=$v['question']?></h4>
                    <span style="font-size:11px;">응답 <?=number_format($v['respond'])?> &nbsp; 좋아요 <span id="likecount<?=$v['que_srl']?>"><?=number_format($v['likes'])?></span></span>
<?
    if($v['mem_srl'] !== $member['mem_srl']) {
?>
                    <a href="javascript:;" class="likethis" id="likethis<?=$v['que_srl']?>" data-question="<?=$v['que_srl']?>" data-status="<?=(empty($like[$v['que_srl']]))?"false":"true"?>" style="float:right;margin-right:25px;"><span class="glyphicon glyphicon-heart" id="like<?=$v['que_srl']?>" style="font-size:20px;color:<?=(empty($like[$v['que_srl']]))?"gray":"darkorange"?>;"></span></a>
<?
    }
?>
                </div>
            </div>
            <hr style="margin-top:15px;margin-bottom:10px">
<?
}
?>
        </div>
    </div>
<script>
$(document).ready(function() {
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
</script>
