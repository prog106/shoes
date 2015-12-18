<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

	public function index() { // {{{
        $member = $this->session->userdata('loginmember');
        if(empty($this->input->cookie('nologin')) && empty($member)) {
            redirect('/sign/login', 'refresh');
            die;
        }
        $this->load->model('biz/Questionbiz', 'questionbiz');
        $result = $this->questionbiz->get_main_question_list(1);
        $like = array();
        if(!empty($result)) {
            if(!empty($member) && $member['mem_srl'] > 0) {
                $que_srls = array();
                foreach($result as $k => $v) {
                    $que_srls[] = $v['que_srl'];
                }
                $this->load->model('biz/Likebiz', 'likebiz');
                $likes = $this->likebiz->get_like_info($member['mem_srl'], $que_srls);
                foreach($likes as $k => $v) {
                    $like[$v['que_srl']] = $v['like_srl'];
                }
            }
        } else {
            $result = array(
                array(
                    'que_srl' => 0,
                    'mem_srl' => 0,
                    'mem_name' => '개발자',
                    'mem_level' => 'manager',
                    'mem_picture' => '',
                    'question' => '운영자가 메인에 아무것도 안올렸네요. 이런 인간적인 운영자의 모습을 어떻게 생각하세요? ',
                    'respond' => 0,
                    'likes' => 0,
                    'create_at' => '2015-12-08 16:50:34',
                ),
            );
        }
        $data = array();
        $data['member'] = $member;
        $data['list'] = $result;
        $data['like'] = $like;
        load_view('home/index', $data);
    } // }}}

}
