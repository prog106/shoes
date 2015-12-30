<? $this->load->view('/search/tbar', array('search' => $search), 'true'); ?>
    <div class="row">
        <div class="col-sm-12" id="search_list">
        </div>
    </div>
    <button type="button" id="more" class="glyphicon glyphicon-chevron-down btn btn-default btn-sm" style="width:100%"> 더보기</button>
<script>
var page_num = 1;
var timer    = setInterval(function () { scrollOK = true; }, 100);
var scrollOK = true;

$(document).ready(function() {
    $('#more').click(function() {
        get_question_list(page_num+1);
    });
    get_question_list(1);
    function get_question_list(page_val){
        page_num = page_val;
        url = '/search/ax_get_search_hashtag';
        data = {page:page_num,search:'<?=$search?>'};
        ax_post(url, data, function(d) {
            if(d.recordsTotal > 0){
                var len = d.data.length;
                var html = '';
                for(var i = 0 ; i < len ; i++){
                    var data = d.data[i];
                    html += data;
                }
                $('#search_list').append(html);
                if(d.recordsTotal < 20) {
                    $('#more').hide();
                }
            } else {
                $('#more').hide();
            }
            $('.link').click(function() { window.location.href=$(this).data('link'); });
        });
    }
});
</script>
