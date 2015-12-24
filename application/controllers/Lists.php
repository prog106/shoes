<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lists extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    // 최신 
	public function recent() { // {{{
        $member = $this->session->userdata('loginmember');
        if(empty($this->input->cookie('nologin')) && empty($member)) {
            redirect('/sign/login', 'refresh');
            die;
        }
        $data = array();
        $data['member'] = $member;
        load_view('lists/recent', $data);
    } // }}}

    // 최신 
	public function ax_get_lists() { // {{{
        $member = $this->session->userdata('loginmember');
        if(empty($this->input->cookie('nologin')) && empty($member)) {
            echo json_encode(error_result());
            die;
        }
        $page = $this->input->post('page', true);
        $types = $this->input->post('tp', true);
        $this->load->model('biz/Questionbiz', 'questionbiz');
        $result = $this->questionbiz->get_question_list($page, null, $types);
        $like = array();
        $lists = array();
        $item = array();
        if(!empty($result)) {
            if(!empty($member) && $member['mem_srl'] > 0) {
                $que_srls = array();
                foreach($result as $k => $v) {
                    $que_srls[] = $v['que_srl'];
                    $item[] = $this->load->view('lists/item', $v, true);
                }
                $this->load->model('biz/Likebiz', 'likebiz');
                $likes = $this->likebiz->get_like_info($member['mem_srl'], $que_srls);
                foreach($likes as $k => $v) {
                    $like[$v['que_srl']] = $v['like_srl'];
                }
            }
        }
        $lists = array(
            'recordsTotal' => count($result),
            'data' => $item,
        );
        echo json_encode($lists);
    } // }}}

    // 인기
	public function respond() { // {{{
        $member = $this->session->userdata('loginmember');
        if(empty($this->input->cookie('nologin')) && empty($member)) {
            redirect('/sign/login', 'refresh');
            die;
        }
        $this->load->model('biz/Questionbiz', 'questionbiz');
        $result = $this->questionbiz->get_question_list(1, null, 'respond', 5);
        $like = array();
        if(!empty($result)) {
            if(!empty($member) && $member['mem_srl'] > 0) {
                $que_srls = array();
                foreach($result as $k => $v) {
                    $que_srls[] = $v['que_srl'];
                }
                //$this->load->model('biz/Likebiz', 'likebiz');
                //$likes = $this->likebiz->get_like_info($member['mem_srl'], $que_srls);
                //foreach($likes as $k => $v) {
                //    $like[$v['que_srl']] = $v['like_srl'];
                //}
            }
        }
        $data = array();
        $data['member'] = $member;
        $data['list'] = $result;
        $data['like'] = $like;
        load_view('lists/respond', $data);
    } // }}}

    // 관심 
	public function like() { // {{{
        $member = $this->session->userdata('loginmember');
        if(empty($this->input->cookie('nologin')) && empty($member)) {
            redirect('/sign/login', 'refresh');
            die;
        }
        $data = array();
        $data['member'] = $member;
        load_view('lists/likes', $data);
    } // }}}

}
