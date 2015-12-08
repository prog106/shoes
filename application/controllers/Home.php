<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

	public function index() { // {{{
        $this->load->model('biz/Questionbiz', 'questionbiz');
        $result = $this->questionbiz->get_question_list(1);
        $data = array();
        $data['member'] = $this->session->userdata('loginmember');
        $data['list'] = $result;
        load_view('home/index', $data);
    } // }}}

}
