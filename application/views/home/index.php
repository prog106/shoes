    <div class="row">
        <div class="col-sm-12">
<?
foreach($list as $k => $v) {
    $label = "";
    if(stripos($v['create_at'], date('Y-m-d')) !== true) {
        $label = " <span class=\"label label-success\">오늘</span>";
    }
?>
            <div class="media">
                <div class="media-left" style="padding-top:5px;">
<?
if($v['mem_level'] !== 'manager') {
?>
                    <a href="#">
                        <img class="media-object" src="https://scontent.xx.fbcdn.net/hprofile-frc3/v/t1.0-1/c2.129.716.716/s160x160/1379218_655568767816835_1688760699_n.jpg?oh=a29e5a4d08056faa4889631cb9db3eba&oe=56D7F246" width="50">
                    </a>
<?
}
?>
                </div>
                <div class="media-body">
<?
if($v['mem_level'] !== 'manager') {
?>
                    <span style="font-size:11px;line-height:25px;"><?$v['mem_name']?></span>
                    <span style="font-size:11px;float:right;margin-right:20px;margin-top:5px;"><?=$v['create_at']?></span>
<?
}
?>
                    <h4 class="media-heading" style="line-height:25px;"><?=$v['question']?></h4>
                    <span style="font-size:11px;">응답 <?=number_format($v['respond'])?> &nbsp; 좋아요 <span id="likecount<?=$v['que_srl']?>"><?=number_format($v['likes'])?></span></span>
                    <a href="javascript:;" class="likethis" id="likethis<?=$v['que_srl']?>" data-question="<?=$v['que_srl']?>" data-status="<?=(empty($like[$v['que_srl']]))?"false":"true"?>" style="float:right;margin-right:25px;"><span class="glyphicon glyphicon-heart" id="like<?=$v['que_srl']?>" style="font-size:20px;color:<?=(empty($like[$v['que_srl']]))?"gray":"darkorange"?>;"></span></a>
                    <a href="/answer/view/<?=$v['que_srl']?>" style="float:right;margin-right:25px;"><span class="glyphicon glyphicon-comment" style="font-size:20px;color:mediumorchid;"></span></a>
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
