  <div class="slidemenu slidemenu-left">
    <div class="slidemenu-header">
      <div>
        코멘트
      </div>
    </div>
    <div class="slidemenu-body">
      <ul class="slidemenu-content">
<?
if(!empty($member)) {
?>
        <li><a href="javascript:;"><img src="<?=$member['mem_picture']?>" width="50"> <?=$member['mem_name'].(($member['level']==='manager')?" 관리자":"");?> 님</a></li>
        <li><a href="/question/">질문 올리기</a></li>
        <li><a href="/lists/">다른 질문 보기</a></li>
        <!-- li><a href="javascript:alert('준비중');">[준비중] 내가 올린 질문</a></li -->
        <li><a href="javascript:;" id="logout">로그아웃</a></li>
<?
} else {
?>
        <li><a href="/lists/">다른 질문 보기</a></li>
        <li><a href="/sign/login/">로그인</a></li>
<?
}
?>
      </ul>
    </div>
  </div>
  <div id="main">
    <header id="header">
    <span style="font-size:20px;margin-top:9px;position:absolute;top:0;left:50%;margin-left:-30px;padding:6px 5px;">코멘트</span>
    <button type="button" class="navbar-toggle" data-canvas="body">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
    </button>
      <span class="button menu-button-left">
      </span>
    </header>
    <div id="contents">
<script>
$(document).ready(function() {
<?
if(!empty($member)) {
?>
    $('#logout').click(function() {
        if(!confirm('로그아웃 하시겠습니까?')) {
            return false;
        }
        var url = '/sign/ax_get_logout';
        ax_post(url, null, function(ret) {
            if(ret.result == 'ok') {
                alert('로그아웃 되었습니다.');
                self.location.reload();
            } else {
                alert(ret.msg);
            }
        });
    });
<?
}
?>
var menu = SpSlidemenu('#main', '.slidemenu-left', '.menu-button-left', {direction: 'left'});

});
</script>

<div class="container theme-showcase" role="main">
