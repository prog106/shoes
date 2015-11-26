<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
    public function __construct() {
        parent::__construct();
        $this->load->library('encrypt');
        $this->load->model('biz/Emailforsignbiz', 'emailforsignbiz');
        $this->efs_key = "code_for_sign"; // 암호화 키 - 여기서만 사용
        $this->efs_hour = "2"; // 인증 가능 시간
        $this->efs_url = "http://shoes.prog106.indoproc.xyz/welcome/auth/";
    }

	public function index()
	{
		//$this->load->view('welcome_message');
        //$this->load->view('welcome_test');
        $this->load->view('sign');
	}

    public function auth() { // {{{
        $efs_code = $this->input->get('efs_code', true);
        $result = $this->emailforsignbiz->get_emailforsign(null, null, $efs_code);
        // step 전달 받은 이메일 주소 처리
        $step = $result['data'][0];
        if(empty($step)) alertmsg_move('정상적인 경로가 아닙니다.', '/');
        if($step['efs_status'] === 'auth') alertmsg_move('인증된 URL 입니다.', '/');
        if($step['start_datetime'] > YMD_HIS || $step['end_datetime'] < YMD_HIS) alertmsg_move('인증 기간이 만료된 URL 입니다.', '/');
        $email1 = $this->encrypt->decode($step['email1']);
        $email2 = $step['email2'];
        //debug($step);
        //debug($email1."@".$email2);
        $data = array();
        $data['email1'] = $email1;
        $data['email2'] = $email2;
        $data['end'] = $step['end_datetime'];
        $this->load->view('signform', $data);
    } // }}}

    public function ax_set_emailforsign() { // {{{
        $email1 = $this->input->post('email1', true);
        $email2 = $this->input->post('email2', true);
        // email1 암호화 + efs_code 생성 + start & end
        $email1 = $this->encrypt->encode($email1);
        $efs_code = $this->encrypt->encode("efs_".$email1, $this->efs_key);
        $start_datetime = YMD_HIS;
        $end_datetime = date('Y-m-d H:i:s', strtotime('+ '.$this->efs_hour.' hour'));
        $result = $this->emailforsignbiz->check_emailforsign($email1, $email2, $efs_code, $start_datetime, $end_datetime);
        if($result['result'] === 'ok') {
            // 이메일 발송
            $url = $this->efs_url."?efs_code=".urlencode($efs_code);
            debug_log($url);
        }
        echo json_encode($result);
    } // }}}
}
