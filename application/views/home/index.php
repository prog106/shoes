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
                    <a href="#">
                        <img class="media-object" src="https://scontent.xx.fbcdn.net/hprofile-frc3/v/t1.0-1/c2.129.716.716/s160x160/1379218_655568767816835_1688760699_n.jpg?oh=a29e5a4d08056faa4889631cb9db3eba&oe=56D7F246" width="50">
                    </a>
                </div>
                <div class="media-body">
                    <span style="font-size:11px;line-height:25px;"><?=$v['mem_name']?></span>
                    <span style="font-size:11px;float:right;margin-right:20px;margin-top:5px;"><?=$v['create_at']?></span>
                    <h4 class="media-heading"><pre><?=$v['question']?></pre></h4>
                    <span style="font-size:11px;">응답 <?=number_format($v['respond'])?> 좋아요 <?=number_format($v['like'])?></span><br>
                </div>
            </div>
            <a href="/answer/view/<?=$v['que_srl']?>" class="btn btn-primary" style="width:95%;margin:10px 15px 0px 15px;">응답하기</a>
            <hr>
<?
}
?>
        </div>
    </div>
