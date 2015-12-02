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
        $this->load->library('encryption');
        $this->load->helper('url');
        $this->load->model('biz/Emailforsignbiz', 'emailforsignbiz');
        $this->efs_hour = "2"; // 인증 가능 시간
        $this->efs_url = "http://shoes.prog106.indoproc.xyz/welcome/auth/";
    }

	public function index() { // {{{
		//$this->load->view('welcome_message');
        //$this->load->view('welcome_test');
        /*$a = password_hash('123', PASSWORD_BCRYPT);
        debug($a);
        debug(password_verify('123', $a));*/
        $data = array();
        $data['member'] = $this->session->userdata('loginmember');
        $this->load->view('welcome', $data);
    } // }}}

    // 회원가입 절차 1단계
    public function sign() { // {{{
        $this->load->view('sign');
	} // }}}

    // 회원가입 절차 2단계
    public function auth() { // {{{
        $efs_code = $this->input->get('efs_code', true);
        $result = $this->emailforsignbiz->get_emailforsign(null, null, null, $efs_code);
        // step 전달 받은 이메일 주소 처리
        if($result['result'] !== 'ok') alertmsg_move('유효한 경로가 아닙니다.', '/');
        $step = (!empty($result['data']))?$result['data'][0]:array();
        if(empty($step)) alertmsg_move('유효한 경로가 아닙니다.', '/');
        if($step['efs_status'] === 'auth') alertmsg_move('인증된 URL 입니다.', '/');
        if($step['start_datetime'] > YMD_HIS || $step['end_datetime'] < YMD_HIS) alertmsg_move('인증 기간이 만료된 URL 입니다.', '/');
        $email1 = $this->encryption->decrypt($step['email1']);
        $email2 = $step['email2'];
        $data = array();
        $data['email1'] = $email1;
        $data['email2'] = $email2;
        $data['end'] = $step['end_datetime'];
        $data['efs_srl'] = $step['efs_srl'];
        $this->load->view('signform', $data);
    } // }}}

    // 로그인
    public function login() { // {{{

        $this->load->library('facebook'); // Automatically picks appId and secret from config
        // OR
        // You can pass different one like this
        //$this->load->library('facebook', array(
        //    'appId' => 'APP_ID',
        //    'secret' => 'SECRET',
        //    ));

        $user = $this->facebook->getUser();
        
        if ($user) {
            try {
                $data['user_profile'] = $this->facebook->api('/me?fields=name,email,birthday,gender');
            } catch (FacebookApiException $e) {
                $user = null;
            }
        }else {
            // Solves first time login issue. (Issue: #10)
            //$this->facebook->destroySession();
        }

        if ($user) {
            self::save_login($data['user_profile']['id'], $data['user_profile']['email'], $data['user_profile']['name']);
            redirect('/', 'refresh');

            $data['logout_url'] = site_url('welcome/logout'); // Logs off application
            // OR 
            // Logs off FB!
            // $data['logout_url'] = $this->facebook->getLogoutUrl();

        } else {
            $data['login_url'] = $this->facebook->getLoginUrl(array(
                'redirect_uri' => 'http://shoes.prog106.indoproc.xyz/welcome/login', 
                'scope' => array('user_birthday,public_profile,email'), // permissions here
            ));
        }
        debug($data);
        $this->load->view('login', $data);
    } // }}}

    public function ax_set_emailforsign() { // {{{
        $email1 = $this->input->post('email1', true);
        $email2 = $this->input->post('email2', true);
        // email1 암호화 + efs_code 생성 + start & end
        $email1 = $this->encryption->encrypt($email1);
        $efs_code = $this->encryption->encrypt($email1);
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

    public function ax_set_sign() { // {{{
        $email1 = $this->input->post('email1', true);
        $email2 = $this->input->post('email2', true);
        $efs_srl = $this->input->post('efs_srl', true);
        $pwd = $this->input->post('pwd', true);
        $pwd1 = $this->input->post('pwd1', true);
        $name = $this->input->post('name', true);
        // 회원가입시 체크할 꺼 - 이메일주소와 efs_srl 비교, 비밀번호 비교
        $result = $this->emailforsignbiz->get_emailforsign($efs_srl);
        $step = $result['data'][0];
        $save_email1 = $this->encryption->decrypt($step['email1']);
        if($save_email1 !== $email1 || $step['email2'] !== $email2 || $step['efs_srl'] !== $efs_srl || empty($pwd) || $pwd !== $pwd1 || empty($name) || $step['efs_status'] !== 'notauth') {
            echo json_encode(error_result('필수값이 누락되었습니다.'));
            die;
        }

        $email = $this->encryption->encrypt($email1."@".$email2); // 이메일 전체 암호화 저장
        $email1 = sha1($email1); // @ 앞자리 md5 저장 - 로그인시 필요
        $pwd = password_hash($pwd, PASSWORD_BCRYPT); // 비밀번호 암호화

        $this->load->model('biz/Signbiz', 'signbiz');
        $result = $this->signbiz->sign_member($efs_srl, $email1, $email2, $pwd, $name, $email);
        echo json_encode($result);
    } // }}}

    public function ax_get_login() { // {{{
        $email = $this->input->post('email', true);
        $pwd = $this->input->post('pwd', true);
        if(empty($email) || empty($pwd)) {
            echo json_encode(error_result('필수값이 누락되었습니다.'));
            die;
        }
        list($email1, $email2) = explode("@", $email);
        $email1 = sha1($email1);
        $this->load->model('biz/Signbiz', 'signbiz');
        $result = $this->signbiz->login_member($email1, $email2);
        if($result['result'] !== 'ok' || empty($result['data'])) {
            echo json_encode(error_result('회원정보가 없습니다.'));
            die;
        }
        $info = $result['data'][0];
        if($info['status'] === 'normal') {
            if(password_verify($pwd, $info['mem_pwd'])) {
                $this->session->set_userdata('loginmember', $loginmember);
                self::save_login($info['mem_srl'], $email, $info['mem_name']);
                echo json_encode(ok_result());
                die;
            } else {
                echo json_encode(error_result('회원정보가 일치하지 않습니다.'));
                die;
            }
        } else if($info['status'] === 'hold') {
            echo json_encode(error_result('로그인이 불가능 합니다.'));
            die;
        } else {
            echo json_encode(error_result('회원정보가 없습니다.'));
            die;
        }

    } // }}}

    public function ax_get_logout() { // {{{
        $this->load->library('facebook');
        $this->facebook->destroySession();
        if(!empty($this->session->userdata('loginmember'))) {
            $this->session->unset_userdata('loginmember');
        }
        echo json_encode(ok_result());
    } // }}}

    private function save_login($srl, $email, $name) { // {{{
        // 로그인 성공
        $loginmember = array(
            'mem_srl' => $srl,
            'mem_email' => $email,
            'mem_name' => $name,
        );
        $this->session->set_userdata('loginmember', $loginmember);
    } // }}}
}
