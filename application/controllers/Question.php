<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Question extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('biz/Questionbiz', 'questionbiz');
    }

    private function manager($member) { // {{{
        if(empty($member)) {
            alertmsg_move('로그인 후 이용해 주세요', '/');
            die;
        } else if($member['level'] !== 'manager') {
            alertmsg_move('정상적인 접근이 아닙니다.', '/');
            die;
        }
    } // }}}

    // 질문 리스트 & 올리기 폼
    public function index() { // {{{
        $member = $this->session->userdata('loginmember');
        self::manager($member);

        $data = array();
        $data['member'] = $member;
        load_view('question/index', $data);
    } // }}}

    // 질문 올리기
    public function ax_set_question() { // {{{
        $member = $this->session->userdata('loginmember');
        self::manager($member);

        $question = $this->input->post('question', true);
        $start = $this->input->post('start', true);
        $end = $this->input->post('end', true);
        if(empty($question)) {
            echo json_encode(error_result());
            die;
        }
        $result = $this->questionbiz->save_question($question, $member['mem_srl'], $member['level'], $start, $end);
        if($result['result'] == 'ok') {
            echo json_encode(ok_result());
            die;
        }
        echo json_encode(error_result());
    } // }}}

    // 질문 가져오기
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
