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
        $start_y = $this->input->post('start_y', true);
        $start_m = $this->input->post('start_m', true);
        $start_d = $this->input->post('start_d', true);
        $start = $start_y."-".sprintf('%02d', $start_m)."-".sprintf('%02d', $start_d);
        $main_start_y = $this->input->post('main_start_y', true);
        $main_start_m = $this->input->post('main_start_m', true);
        $main_start_d = $this->input->post('main_start_d', true);
        $main_start = null;
        if(!empty($main_start_y) && !empty($main_start_m) && !empty($main_start_d)) {
            $main_start = $main_start_y."-".sprintf('%02d', $main_start_m)."-".sprintf('%02d', $main_start_d);
        }
        $main_end_y = $this->input->post('main_end_y', true);
        $main_end_m = $this->input->post('main_end_m', true);
        $main_end_d = $this->input->post('main_end_d', true);
        $main_end = null;
        if(!empty($main_end_y) && !empty($main_end_m) && !empty($main_end_d)) {
            $main_end = $main_end_y."-".sprintf('%02d', $main_end_m)."-".sprintf('%02d', $main_end_d);
        }
        if(($main_start > $main_end) || (!empty($main_start) && empty($main_end)) || (empty($main_start) && !empty($main_end))) {
            echo json_encode(error_result('메인 노출 날짜 에러'));
            die;
        }

        $que_srl = $this->input->post('que_srl', true);
        if(empty($question)) {
            echo json_encode(error_result('질문을 입력하세요.'));
            die;
        }
        if(!empty($que_srl) && $que_srl > 0) {
            if($member['level'] === 'manager') {
                $result = $this->questionbiz->update_question($que_srl, $question, $member['mem_srl'], $start, $main_start, $main_end);
            } else {
                $result = $this->questionbiz->update_question($que_srl, $question, $member['mem_srl']);
            }
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
        $lists = array(
            'recordsTotal' => count($result),
            'data' => $list,
        );
        echo json_encode($lists);
    } // }}}

}
