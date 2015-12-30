<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Search extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('biz/Searchbiz', 'searchbiz');
    }

    // hashtag 로 검색 
    public function hashtag($search='') { // {{{
        if(empty($search)) {
            redirect('/', 'refresh');
            die;
        }

        $data = array();
        $data['search'] = urldecode($search);
        load_view('search/index', $data);
    } // }}}

    // 검색결과 가져오기
    public function ax_get_search_hashtag() { // {{{
        $search = $this->input->post('search', true);
        $page = $this->input->post('page', true);
        $result = $this->searchbiz->get_search_from_hashtag($page, $search);
        $list = array();
        foreach($result as $k => $v) {
            //$v['question'] = nl2br(strip_tags($v['question']));
            $list[] = $this->load->view('search/item', $v, true);
        }
        $lists = array(
            'recordsTotal' => count($result),
            'data' => $list,
        );
        echo json_encode($lists);
    } // }}}

}
