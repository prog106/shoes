<!-- Fixed navbar -->
<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar" style="float:left;margin-left:10px;">
                <span class="sr-only"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/" style="color:#FFFFFF">Today's 코멘트</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
                <!--- li><a href="#">2015</a></li>
                <li><a href="#about">1997</a></li>
                <li><a href="#contact">1987</a></li -->
<?
if(!empty($member)) {
?>
                <li>
                    <a href="javascript:;">어서오세요! <?=$member['mem_name'].(($member['level']==='manager')?" 관리자":"");?> 님</a>
                </li>
                <li><a href="/question">질문 올리기</a></li>
                <li><a href="/lists">다른 질문 보기</a></li>
                <!-- li><a href="javascript:alert('준비중');">[준비중] 내가 올린 질문</a></li -->
                <li><a href="javascript:;" id="logout">로그아웃</a></li>
<?
} else {
?>
                <li><a href="/lists">다른 질문 보기</a></li>
                <li><a href="/sign/login">로그인</a></li>
<?
}
?>
            </ul>
        </div><!--/.nav-collapse -->
    </div>
</nav>
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
});
</script>

<div class="container theme-showcase" role="main" style="margin-top:70px;">
