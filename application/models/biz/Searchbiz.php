<?php
/**
 * @ description : Search biz
 * @ author : prog106 <prog106@gmail.com>
 */
class Searchbiz extends CI_Model {
    public function __construct() {
        parent::__construct();
        $this->load->model('dao/Searchdao', 'searchdao');
    }

    // 검색 결과 가져오기
    public function get_search_from_hashtag($page=1, $search) { // {{{
        $error_result = error_result('필수값이 누락되었습니다.');
        $sql_param = array();
        if(empty($search)) return $error_result;
        $limit = 20;
        $paging = ($page-1)*$limit;
        return $this->searchdao->get_search_from_hashtag($search, $paging, $limit);
    } // }}}

}
