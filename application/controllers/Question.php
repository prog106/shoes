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

        $question = trim(strip_tags($this->input->post('question', true)));
        $start = $this->input->post('start', true);
        $main_start = $this->input->post('main_start', true);
        $main_end = $this->input->post('main_end', true);
        $que_srl = $this->input->post('que_srl', true);
        if(empty($question)) {
            echo json_encode(error_result('질문을 입력하세요.'));
            die;
        }
        if(!empty($que_srl) && $que_srl > 0) {
            $result = $this->questionbiz->update_question($que_srl, $question, $member['mem_srl']);
        } else {
            $result = $this->questionbiz->save_question($question, $member['mem_srl'], $member['mem_name'], $member['level'], $member['mem_picture'], $main_start, $main_end, $start);
        }
        if($result['result'] == 'ok') {
            echo json_encode(ok_result());
            die;
        } else {
            echo json_encode(error_result($result['msg']));
        }
    } // }}}

    // 질문 지우기
    public function ax_set_question_del() { // {{{
        $member = $this->session->userdata('loginmember');
        self::manager($member);

        $que_srl = $this->input->post('que', true);
        if(!empty($que_srl) && $que_srl > 0) {
            $result = $this->questionbiz->delete_question($que_srl, $member['mem_srl']);
        } else {
            return error_result('잘못된 접근입니다.');
        }
        if($result['result'] == 'ok') {
            echo json_encode(ok_result());
            die;
        } else {
            echo json_encode(error_result($result['msg']));
        }
    } // }}}

    // 질문 가져오기
    public function ax_get_question() { // {{{
        $member = $this->session->userdata('loginmember');
        self::manager($member);

        $page = $this->input->post('page', true);
        $result = $this->questionbiz->get_question_list($page, $member['mem_srl']);
        $list = array();
        foreach($result as $k => $v) {
            $v['question'] = nl2br(strip_tags($v['question']));
            if($member['level'] === 'manager') {
                $list[] = $this->load->view('question/mitem', $v, true);
            } else {
                $list[] = $this->load->view('question/item', $v, true);
            }
        }
        $list = array(
            'recordsTotal' => count($result),
            'data' => $list,
        );
        echo json_encode($list);
    } // }}}

}
