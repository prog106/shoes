    <div class="row">
        <div class="col-sm-12">
            <div class="media">
                <div class="media-left">
                    <a href="#">
                        <img class="media-object" src="https://scontent.xx.fbcdn.net/hprofile-frc3/v/t1.0-1/c2.129.716.716/s160x160/1379218_655568767816835_1688760699_n.jpg?oh=a29e5a4d08056faa4889631cb9db3eba&oe=56D7F246" width="50">
                    </a>
                </div>
                <div class="media-body">
                    nickname
                    <h4 class="media-heading">Question</h4>
                    like 10000 zzim 5000<br>
                </div>
            </div>
            <button type="button" class="btn btn-primary btn-sm" style="width:100%">응답하기</button>
            <div class="media">
                <div class="media-left">
                    <a href="#">
                        <img class="media-object" src="https://scontent.xx.fbcdn.net/hprofile-frc3/v/t1.0-1/c2.129.716.716/s160x160/1379218_655568767816835_1688760699_n.jpg?oh=a29e5a4d08056faa4889631cb9db3eba&oe=56D7F246" width="50">
                    </a>
                </div>
                <div class="media-body">
                    nickname
                    <h4 class="media-heading">Question</h4>
                    like 10000 zzim 5000<br>
                </div>
            </div>
            <button type="button" class="btn btn-primary btn-sm" style="width:100%">응답하기</button>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="list-group">
<?
foreach($list as $k => $v) {
    $label = "";
    if(stripos($v['create_at'], date('Y-m-d')) !== true) {
        $label = " <span class=\"label label-success\">오늘</span>";
    }
?>
                <a href="#" class="list-group-item">
                    <h4 class="list-group-item-heading"><?=$v['question']?></h4>
                    <p class="list-group-item-text"><?=$v['create_at']?><?=$label?></p>
                </a>
<?
}
?>
            </div>
        </div><!-- /.col-sm-4 -->
    </div>
