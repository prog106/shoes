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
