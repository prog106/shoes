<?
$like_color = "gray";
$st = "like";
if(!empty($la_srl)) {
    $like_color = "orange";
    $st = "dontlike";
}
?>
            <div class="media" style="margin-top:5px;">
                <div class="media-left" style="padding-top:5px;">
                    <a href="#">
                        <img class="media-object" src="<?=(!empty($mem_picture)?$mem_picture:"/static/image/komment.png")?>" style="width:30px;height:30px;">
                    </a>
                </div>
                <div class="media-body">
                    <span style="font-size:11px;line-height:25px;color:darkcyan;">
<?
if(!empty($me)) {
?>
                        <a href="javascript:;" class="delthis" id="delthis<?=$ans_srl?>" data-ans="<?=$ans_srl?>"><span class="glyphicon glyphicon-trash" style="font-size:12px;color:deeppink;"></span></a>
<?
}
?>
                        <?=$mem_name?>
                        <span style="color:darkorange;font-size:11px"> 좋아요 <span style="font-size:11px" id="likecount<?=$ans_srl?>"><?=number_format($likes)?></span></span>
                    </span>
                    <span style="font-size:11px;float:right;margin-right:20px;margin-top:5px;color:#555;"><?=$create_at?></span>
                    <h5 class="media-heading" style="line-height:18px;"><?=convert_hashtag($answer)?></h5>
                    <span class="links" onclick="alert('준비중입니다.')" data-link="/answer/reply/<?=$ans_srl?>" style="font-size:11px;cursor:pointer;"><span class="glyphicon glyphicon-leaf"></span> 댓글에 답변달기</span>
<?
if(empty($me)) {
?>
                        <a href="javascript:;" class="likeans" style="float:right;margin-right:15px;" id="likethis<?=$ans_srl?>" data-ans="<?=$ans_srl?>" data-status="<?=$st?>"><span class="glyphicon glyphicon-thumbs-up" id="like<?=$ans_srl?>" style="font-size:20px;color:<?=$like_color?>;"></span></a>
<?
}
?>
                </div>
                <hr style="margin-top:5px;margin-bottom:0px">
            </div>
