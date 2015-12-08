<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Question extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('biz/Questionbiz', 'questionbiz');
    }

    public function index() { // {{{
        $data = array();
        $data['member'] = $this->session->userdata('loginmember');
        if(empty($data)) {
            alertmsg_move('로그인 후 이용해 주세요', '/');
            die;
        }
        load_view('question/index', $data);
    } // }}}

    public function ax_set_question() { // {{{
        $member = $this->session->userdata('loginmember');
        $question = $this->input->post('question', true);
        if(empty($question)) {
            echo json_encode(error_result());
            die;
        }
        $result = $this->questionbiz->save_question($question, $member['mem_srl']);
        if($result['result'] == 'ok') {
            echo json_encode(ok_result());
            die;
        }
        echo json_encode(error_result());
    } // }}}

    public function ax_get_question() { // {{{
        $page = $this->input->post('page', true);
        $result = $this->questionbiz->get_question_list($page);
        $list = array(
            'recordsTotal' => count($result),
            'data' => $result,
        );
        echo json_encode($list);
    } // }}}

}
