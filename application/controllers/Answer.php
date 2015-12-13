<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Answer extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('biz/Answerbiz', 'answerbiz');
    }

    private function manager($member) { // {{{
        if(empty($member)) {
            echo json_encode(error_result('로그인 후 이용해 주세요'));
            die;
        }
    } // }}}

    // 질문 리스트 & 답글 올리기 폼
    public function view($que_srl) { // {{{
        $member = $this->session->userdata('loginmember');
        if(empty($que_srl)) {
            alertmsg_move('질문이 없습니다.');
            die;
        }
        $data = array();
        $data['member'] = $member;
        $this->load->model('biz/Questionbiz', 'questionbiz');
        $question = array();
        $question = $this->questionbiz->get_question($que_srl);
        if(empty($question)) {
            alertmsg_move('질문이 없습니다.');
            die;
        }
        $data['question'] = $question;
        load_view('answer/index', $data);
    } // }}}

    // 답글 올리기
    public function ax_set_answer() { // {{{
        $member = $this->session->userdata('loginmember');
        self::manager($member);

        $que_srl = $this->input->post('question', true);
        $answer = $this->input->post('answer', true);
        if(empty($answer)) {
            echo json_encode(error_result());
            die;
        }
        $result = $this->answerbiz->answer($answer, $member['mem_srl'], $member['level'], $que_srl);
        if($result['result'] == 'ok') {
            echo json_encode(ok_result());
            die;
        }
        echo json_encode(error_result());
    } // }}}

    // 답글 가져오기
    public function ax_get_answer() { // {{{
        $que_srl = $this->input->post('question', true);
        $page = $this->input->post('page', true);
        $result = $this->answerbiz->get_answer_list($page, $que_srl);
        $list = array(
            'recordsTotal' => count($result),
            'data' => $result,
        );
        echo json_encode($list);
    } // }}}

}
