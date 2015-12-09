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
        $result = $this->questionbiz->get_question_list(1);
        $data = array();
        $data['member'] = $member;
        $data['list'] = $result;
        load_view('home/index', $data);
    } // }}}

}
