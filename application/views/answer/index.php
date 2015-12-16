    <div class="row">
        <div class="col-sm-12">
            <div class="media">
                <div class="media-left" style="padding-top:5px;">
<?
if($question['mem_level'] !== 'manager') {
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
if($question['mem_level'] !== 'manager') {
?>
                    <span style="font-size:11px;line-height:25px;"><?$question['mem_name']?></span>
                    <span style="font-size:11px;float:right;margin-right:20px;margin-top:5px;"><?=$question['create_at']?></span>
<?
}
?>
                    <h4 class="media-heading" style="line-height:25px;"><?=$question['question']?></h4>
                    <span style="font-size:11px;">응답 <span id="respond"><?=number_format($question['respond'])?></span> &nbsp; 좋아요 <span id="likecount<?=$question['que_srl']?>"><?=number_format($question['likes'])?></span></span>
                    <a href="javascript:;" class="likethis" id="likethis<?=$question['que_srl']?>" data-question="<?=$question['que_srl']?>" data-status="<?=(empty($like[$question['que_srl']]))?"false":"true"?>" style="float:right;margin-right:25px;"><span class="glyphicon glyphicon-heart" id="like<?=$question['que_srl']?>" style="font-size:20px;color:<?=(empty($like[$question['que_srl']]))?"gray":"darkorange"?>;"></span></a>
                    <a href="javascript:;" id="answer_area_view" style="float:right;margin-right:25px;"><span class="glyphicon glyphicon-comment" style="font-size:20px;color:mediumorchid;"></span></a>
                </div>
            </div>
            <hr style="margin-top:15px;margin-bottom:10px">
        </div>
    </div>
    <div id="answer_area" style="display:none;">
        <form id="answer_form" onsubmit="return false;">
        <input type="hidden" name="question" id="question" value="<?=$question['que_srl']?>">
            <div class="form-group">
                <label for="answer">응답</label>
                <input type="text" class="form-control" name="answer" id="answer" maxlength="200" placeholder="<?=(empty($member))?"로그인 후 이용해 주세요.":"응답해 주세요!"?>"<?=(empty($member))?" READONLY":""?>></textarea>
            </div>
            <button type="button" id="regist" class="btn btn-default"<?=(empty($member))?" disabled=\"disabled\"":"";?>>응답했다!</button>
        </form>
        <br>
    </div>
    <ul class="list-group" data-role="listview" data-inset="true" id="answer_list">
    </ul>
<script type="text/javascript">
var page_num = 1;
var timer    = setInterval(function () { scrollOK = true; }, 100);
var scrollOK = true;

$(document).ready(function(){
    $('#answer_area_view').click(function() { $('#answer_area').toggle("slow"); });
    $('#regist').click(function() {
        if(!$('#answer').val()) {
            alert('응답글이 없어요!');
            return false;
        }
        var url = "/answer/ax_set_answer";
        var data = $('#answer_form').serialize();
        ax_post(url, data, function(ret) {
            if(ret.result == 'ok') {
                self.location.reload();
            } else {
                alert(ret.msg);
            }
        });
    });

    $(window).on('scroll', function () {
        if (scrollOK) {
            scrollOK = false;
            if ($(this).scrollTop() + $(this).height() >= ($(document).height() - 5)) {
                get_answer_list(page_num+1);
            }
        }
    });

    get_answer_list(1);
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

    function get_answer_list(page_val){
        page_num = page_val;
        url = '/answer/ax_get_answer';
        data = {page:page_num,question:$('#question').val()};
        ax_post(url, data, function(d) {
            if(d.recordsTotal > 0){
                var len = d.data.length;
                var html = '';
                for(var i = 0 ; i < len ; i++){
                    var data = d.data[i];
                    var like_color = 'gray';
                    var st = 'like';
                    if(data.la_srl) {
                        like_color = 'orange';
                        st = 'dontlike';
                    }
                    html += '<li class="list-group-item">';
                    if(data.me) {
                        html += '<a href="javascript:;" class="delthis" id="delthis'+data.ans_srl+'" data-ans="'+data.ans_srl+'"><span class="glyphicon glyphicon-trash" style="font-size:12px;color:deeppink;"></span></a>';
                    }
                    html += ' <span style="font-size:11px"> [ '+data.create_at+' ] '+data.mem_name+'</span> ';
                    html += ' <span style="color:darkorange;font-size:11px"> 좋아요 <span style="font-size:11px" id="likecount'+data.ans_srl+'">'+data.likes+'</span></span>';
                    html += '<a href="javascript:;" class="likeans" style="float:right;" id="likethis'+data.ans_srl+'" data-ans="'+data.ans_srl+'" data-status="'+st+'"><span class="glyphicon glyphicon-heart" id="like'+data.ans_srl+'" style="font-size:15px;color:'+like_color+';"></span></a><br>';
                    //html += data.ans_srl;
                    html += data.answer;
                    html += '</li>';
                }
                $('#answer_list').append(html);
            }
            $('.delthis').click(function() {
                if(confirm('댓글을 정말 삭제하시겠어요?')) {
                    var ans = $(this).data('ans');
                    var url = '/answer/ax_set_answer_delete';
                    var data = {answer:ans}
                    ax_post(url, data, function(ret) {
                        if(ret.result == 'ok') {
                            $('#delthis'+ans).parent('li').remove();
                            $('#respond').text(parseInt($('#respond').text() - 1));
                            alert('삭제되었습니다.');
                        } else {
                            alert(ret.msg);
                        }
                    });
                }
            });
            $('.likeans').click(function() {
                var sts = $(this).data('status');
                var ans = $(this).data('ans');
                var url = '/like/ax_set_answer_'+sts;
                var data = {answer:ans}
                ax_post(url, data, function(ret) {
                    if(ret.result == 'ok') {
                        already = true;
                        if(ret.data != 'already') already = false;
                        if(sts == 'like') {
                            $('#like'+ans).css('color', 'darkorange');
                            $('#likethis'+ans).data('status', 'dontlike');
                            if(!already) {
                                $('#likecount'+ans).text(parseInt($('#likecount'+ans).text())+1);
                            }
                        } else if(sts == 'dontlike') {
                            $('#like'+ans).css('color', 'gray');
                            $('#likethis'+ans).data('status', 'like');
                            if(!already) {
                                $('#likecount'+ans).text(parseInt($('#likecount'+ans).text())-1);
                            }
                        }
                    } else {
                        alert(ret.msg);
                    }
                });
            });
        });
    }
});
</script>
