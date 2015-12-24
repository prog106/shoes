<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sign extends CI_Controller {

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
        $this->efs_url = "http://shoes.prog106.indoproc.xyz/sign/auth/";
    }

    // 회원가입 절차 1단계
    public function sign() { // {{{
        load_view('sign/sign');
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
        load_view('sign/signform', $data);
    } // }}}

    // 로그인
    public function login() { // {{{
        $member = $this->session->userdata('loginmember');
        if(!empty($member)) redirect('/', 'refresh');
        $data = array();
        $data['sign'] = true;
        load_view('sign/login', $data);
    } // }}}

    // 이메일 회원가입 efs
    public function ax_set_emailforsign() { // {{{
        $email1 = $this->input->post('email1', true);
        $email2 = $this->input->post('email2', true);
        $this->load->helper('email');
        if(valid_email($email1."@".$email2)) {
            // email1 암호화 + efs_code 생성 + start & end
            $email1 = $this->encryption->encrypt($email1);
            $efs_code = $this->encryption->encrypt($email1);
            $start_datetime = YMD_HIS;
            $end_datetime = date('Y-m-d H:i:s', strtotime('+ '.$this->efs_hour.' hour'));
            $result = $this->emailforsignbiz->check_emailforsign($email1, $email2, $efs_code, $start_datetime, $end_datetime);
            if($result['result'] === 'ok') {
                // 이메일 발송
                $url = $this->efs_url."?efs_code=".urlencode($efs_code);
                $result['url'] = $url;
                //debug_log($url);
            }
        } else {
            $result = array('result' => 'notemail', 'msg' => '이메일 주소를 다시 확인해 주세요');
        }
        echo json_encode($result);
    } // }}}

    // facebook 회원가입 & 로그인
    public function facebooklogin() { // {{{
        $member = $this->session->userdata('loginmember');
        if(!empty($member)) close_reload();

        echo "<!DOCTYPE html>
<html lang=\"en\">
<head>
    <meta charset=\"utf-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>응답하라</title>
    <script src=\"/static/js/jquery-1.11.3.min.js\"></script>
    <!-- Latest compiled and minified CSS -->
    <link rel=\"stylesheet\" href=\"/static/css/bootstrap.min.css\">

    <!-- Optional theme -->
    <link rel=\"stylesheet\" href=\"/static/css/bootstrap-theme.min.css\">

    <!-- Latest compiled and minified JavaScript -->
    <script src=\"/static/js/bootstrap.min.js\"></script>
</head>
<body>
<div class=\"col-xs-12 col-sm-12 progress-container\">
    <div class=\"progress progress-striped active\">
        <div class=\"progress-bar progress-bar-success\" style=\"width:0%\"></div>
    </div>
</div>
<h5>Facebook 접속중입니다... 잠시만 기다려 주세요...</h5>
<script>
function timeout() {
    setTimeout(function () {
        $(\".progress-bar\").animate({
            width: \"+=50px\"
        }, \"slow\");
        timeout();
    }, 800);
}
timeout();
</script>
</body>
</html>";

        $this->load->library('facebook'); // Automatically picks appId and secret from config
        $user = $this->facebook->getUser();
        if ($user) {
            try {
                $data['user_profile'] = $this->facebook->api('/me?fields=name,email,picture');
            } catch (FacebookApiException $e) {
                $user = null;
            }
        }else {
        }

        if ($user) {
            $this->load->model('biz/Signbiz', 'signbiz');
            $result = $this->signbiz->sns_login_member('facebook', $data['user_profile']['id']);
            if($result['result'] === 'ok') {
                $mem = $result['data'];
                // 가입이 안되어 있으면 가입 처리
                if(empty($mem)) {
                    self::save_sign('facebook', $data['user_profile']['id'], $data['user_profile']['email'], $data['user_profile']['name'], $data['user_profile']['picture']['data']['url']); 
                    close_reload('/sign/joins');
                // 가입이 되어 있으면 로그인 처리
                } else {
                    if(in_array($mem['status'], array('normal', 'manager'))) {
                        self::save_login($mem['mem_srl'], $this->encryption->decrypt($mem['mem_email']), $mem['mem_name'], $mem['status'], $mem['mem_picture']);
                        close_reload();
                    } else {
                        alertmsg_move('로그인을 할 수 없는 정보입니다.');
                    }
                }
            } else {
                alertmsg_move('로그인에 문제가 있습니다. 잠시후 다시 시도해 주세요.');
            }
            die;
            /*// 회원가입 시킨다. facebook id, email, picture 
            $mem = $this->signbiz->sns_member('facebook', $data['user_profile']['id'], $this->encryption->encrypt($data['user_profile']['email']), $data['user_profile']['name'], $data['user_profile']['picture']['data']['url']);
            if(!empty($mem) && $mem['result'] === 'ok') {
                $mem_srl = $mem['data']['mem_srl'];
                $level = $mem['data']['level'];
                $picture = $mem['data']['mem_picture'];
                self::save_sign($mem_srl, $data['user_profile']['email'], $data['user_profile']['name'], $level, $picture);
                close_reload();
            } else {
                alertmsg_move('로그인을 실패하였습니다.');
            }
            die;
            $data['logout_url'] = site_url('sign/logout'); // Logs off application*/
        } else {
            $data['login_url'] = $this->facebook->getLoginUrl(array(
                'redirect_uri' => 'http://shoes.prog106.indoproc.xyz/sign/facebooklogin', 
                'scope' => array('user_birthday,public_profile,email'), // permissions here
            ));
            redirect($data['login_url'], 'refresh');
        }
    } // }}}

    // 카카오 회원가입 & 로그인
    public function ax_set_kakao() { // {{{
        $kakao_id = $this->input->post('id', true);
        $kakao_nickname = $this->input->post('name', true);
        $kakao_picture = $this->input->post('picture', true);
        if(!empty($kakao_id) && !empty($kakao_nickname)) {
            $this->load->model('biz/Signbiz', 'signbiz');
            $result = $this->signbiz->sns_login_member('kakao', $kakao_id);
            if($result['result'] === 'ok') {
                $mem = $result['data'];
                // 가입이 안되어 있으면 가입 처리
                if(empty($mem)) {
                    self::save_sign('kakao', $kakao_id, '', $kakao_nickname, $kakao_picture); 
                    echo json_encode(error_result('joins'));
                // 가입이 되어 있으면 로그인 처리
                } else {
                    if(in_array($mem['status'], array('normal', 'manager'))) {
                        self::save_login($mem['mem_srl'], $this->encryption->decrypt($mem['mem_email']), $mem['mem_name'], $mem['status'], $mem['mem_picture']);
                        echo json_encode(ok_result());
                    } else {
                        echo json_encode(error_result('로그인을 할 수 없는 정보입니다.'));
                    }
                }
            } else {
                echo json_encode(error_result());
            }
            die;
            /*// 회원가입 시킨다. kakao id, name
            $this->load->model('biz/Signbiz', 'signbiz');
            $mem = $this->signbiz->sns_member('kakao', $kakao_id, $this->encryption->encrypt($kakao_id."@kakao"), $kakao_nickname, $kakao_picture);
            if(!empty($mem) && $mem['result'] === 'ok') {
                $mem_srl = $mem['data']['mem_srl'];
                $level = $mem['data']['level'];
                $picture = $mem['data']['mem_picture'];
                self::save_login($mem_srl, $kakao_id.'@kakao', $kakao_nickname, $level, $picture);
                echo json_encode(ok_result());
            } else {
                alertmsg_move('로그인을 실패하였습니다.');
            }
            die;*/
        }
        echo json_encode(error_result());
        die;
    } // }}}

    // 회원가입
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
        $result = $this->signbiz->sign_member('efs', $efs_srl, $email1, $email2, $pwd, $name, $email);
        echo json_encode($result);
    } // }}}

    // 로그인
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

    // 일반 로그아웃
    public function logout() { // {{{
        $this->load->library('facebook');
        $this->facebook->destroySession();
        if(!empty($this->session->userdata('loginmember'))) {
            $this->session->unset_userdata('loginmember');
        }
        redirect('/', 'refresh');
    } // }}}

    // 로그아웃
    public function ax_get_logout() { // {{{
        $this->load->library('facebook');
        $this->facebook->destroySession();
        if(!empty($this->session->userdata('loginmember'))) {
            $this->session->unset_userdata('loginmember');
        }
        echo json_encode(ok_result());
    } // }}}

    // no login
    public function ax_set_nologin() { // {{{
        $cookie = array(
            'name' => 'nologin',
            'value' => 'true',
            'expire' => '0',
        );
        $this->input->set_cookie($cookie);
        echo json_encode(ok_result());
    } // }}}

    // sns 를 통한 회원가입
    public function joins() { // {{{
        $data = array();
        $sign = $this->session->tempdata();
        if(empty($sign)) {
            redirect('/sign/login', 'refresh');
            die;
        }
        $data['sign'] = $sign;
        load_view('sign/joins', $data);
    } // }}}

    // 회원 가입을 위한 세션 저장
    private function save_sign($type, $srl, $email, $name, $picture=null) { // {{{
        if(!empty($type) && !empty($name) && !empty($srl)) {
            $signmember = array(
                'sign_srl' => $srl,
                'sign_type' => $type,
                'sign_email' => $email,
                'sign_name' => $name,
                'sign_picture' => $picture,
            );
            // 5분후 사라지는 세션 생성
            $this->session->set_tempdata($signmember, NULL, 60*5);
        }
    } // }}}

    // 로그인 세션 저장
    private function save_login($srl, $email, $name, $level, $picture=null) { // {{{
        if($srl > 0) {
            // 로그인 성공
            $loginmember = array(
                'mem_srl' => $srl,
                'mem_email' => $email,
                'mem_name' => $name,
                'mem_picture' => $picture,
                'level' => $level,
            );
            $this->session->set_userdata('loginmember', $loginmember);
        }
    } // }}}

    // 정보 확인 & 수정
    public function info() { // {{{
        $member = $this->session->userdata('loginmember');
        if(empty($member)) redirect('/', 'refresh');
        $this->load->model('biz/Signbiz', 'signbiz');
        $result = $this->signbiz->get_member($member['mem_srl']);
        if($result['result'] !== 'ok') redirect('/', 'refresh');
        $info = $result['data'];
        if(empty($info) || (!empty($info) && !in_array($info['status'], array('normal', 'manager')))) redirect('/', 'refresh');
        $info['mem_email'] = $this->encryption->decrypt($info['mem_email']);
        $data['member'] = $member;
        $data['info'] = $info;
        load_view('sign/info', $data);
    } // }}}

    // 회원 정보 수정
    public function ax_set_info() { // {{{
        $member = $this->session->userdata('loginmember');
        $mem_srl = $this->input->post('mem', true);
        $name = trim(strip_tags($this->input->post('mem_name', true)));
        $mem_email = $this->input->post('mem_email', true);
        if($member['mem_srl'] !== $mem_srl) {
            echo json_encode(error_result('loginerror'));
            die;
        }
        $this->load->helper('email');
        if(!valid_email($mem_email)) {
            echo json_encode(error_result('이메일 주소를 확인해 주세요.'));
            die;
        } else {
            $email = $this->encryption->encrypt($mem_email);
        }
        $picture = null;
        $this->load->model('biz/Signbiz', 'signbiz');
        $result = $this->signbiz->update_member($mem_srl, $name, $email);
        if($result['result'] === 'ok') {
            $mem_srl = $member['mem_srl'];
            //$mem_email = $member['mem_email'];
            $level = $member['level'];
            $picture = $member['mem_picture'];
            // 로그아웃 후 다시 로그인 처리
            $this->session->unset_userdata('loginmember');
            self::save_login($mem_srl, $mem_email, $name, $level, $picture);
            echo json_encode(ok_result());
        } else {
            echo json_encode(error_result($result['msg']));
        }
    } // }}}

    // 회원 가입
    public function ax_set_sns_sign() { // {{{
        $member = $this->session->userdata('loginmember');
        $sign = $this->session->tempdata();
        if(!empty($member) || empty($sign)) {
            echo json_encode(error_result());
            die;
        }
        $efs_srl = $this->input->post('mem', true);
        $mem_type = $this->input->post('from', true);
        $mem_email1 = $mem_type.$efs_srl;
        $mem_email2 = $mem_type;
        $mem_email = $this->input->post('mem_email', true);
        $mem_name = trim(strip_tags($this->input->post('mem_name', true)));
        $mem_pwd = $mem_type;
        $mem_picture = $this->input->post('picture', true);
        if($sign['sign_srl'] !== $efs_srl || $sign['sign_type'] !== $mem_type) {
            echo json_encode(error_result());
            die;
        }
        $this->load->helper('email');
        if(!valid_email($mem_email)) {
            echo json_encode(error_result('이메일 주소를 확인해 주세요.'));
            die;
        }
        $this->load->model('biz/Signbiz', 'signbiz');
        $result = $this->signbiz->save_member($mem_type, $efs_srl, $mem_email1, $mem_email2, $mem_type, $mem_name, $this->encryption->encrypt($mem_email), $mem_picture);
        if($result['result'] === 'ok' && $result['data'] > 0) {
            self::save_login($result['data'], $mem_email, $mem_name, 'normal', $mem_picture);
            echo json_encode(ok_result());
            die;
        }
        echo json_encode(error_result());
    } // }}}

    // 닉네임 중복체크
    public function ax_get_nickname() { // {{{
        $name = $this->input->post('nickname', true);
        if(empty($name)) {
            echo json_encode(error_result());
            die;
        }
        $this->load->model('biz/Signbiz', 'signbiz');
        $result = $this->signbiz->get_nickname($name);
        if($result['result'] === 'ok') {
            if(empty($result['data'])) {
                echo json_encode(ok_result());
                die;
            }
        }
        echo json_encode(error_result());
    } // }}}

}
