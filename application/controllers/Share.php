<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Share extends CI_Controller {

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
        $this->load->library(array('common','encryption', 'SnsLogin'));
        $this->load->helper('url');
        $this->load->model('biz/Emailforsignbiz', 'emailforsignbiz');
        $this->efs_hour = "2"; // 인증 가능 시간
        $this->url = "http://shoes.prog106.indoproc.xyz/";
    }

    // facebook 회원가입 & 로그인
    public function facebooklogin() { // {{{
        $member = $this->session->userdata('loginmember');
        if(!empty($member)) {
            redirect('/', 'refresh');
            //close_reload();
            die;
        }

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
                    redirect('/sign/joins', 'refresh');
                    //close_reload('/sign/joins');
                    die;
                // 가입이 되어 있으면 로그인 처리
                } else {
                    if(in_array($mem['status'], array('normal', 'manager'))) {
                        self::save_login($mem['mem_srl'], $this->encryption->decrypt($mem['mem_email']), $mem['mem_name'], $mem['status'], $mem['mem_picture']);
                        redirect('/', 'refresh');
                        //close_reload();
                        die;
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
            width: \"+=5%\"
        }, \"slow\");
        timeout();
    }, 800);
}
timeout();
</script>
</body>
</html>";
            $data['login_url'] = $this->facebook->getLoginUrl(array(
                'redirect_uri' => 'http://shoes.prog106.indoproc.xyz/sign/facebooklogin', 
                'scope' => array('user_birthday,public_profile,email'), // permissions here
            ));
            redirect($data['login_url'], 'refresh');
        }
    } // }}}

    // 카카오 회원가입 & 로그인
    public function kakaoshare() { // {{{
        $member = $this->session->userdata('loginmember');
        if($member['token']) {

            $_SESSION['kakao_token'] = $member['token'];
            
            $url = $this->input->get('share_url', true);
            $url = "https://kapi.kakao.com/v1/api/linkinfo?url=".$url;
            $param = "";
            $header = array("Authorization: Bearer " .$member['token']);


            // Get User Info
            $user_data = $this->common->restful_curl($url, $param, 'POST', $header);
            $user_data = json_decode($user_data);
            $properties = $user_data->properties;

            $kakao_id = $user_data->id;
            $kakao_nickname = $properties->nickname;
            $kakao_picture = $properties->thumbnail_image;

            if(!empty($kakao_id) && !empty($kakao_nickname)) {
                $this->load->model('biz/Signbiz', 'signbiz');
                $result = $this->signbiz->sns_login_member('kakao', $kakao_id);
                if($result['result'] === 'ok') {
                    $mem = $result['data'];
                    // 가입이 안되어 있으면 가입 처리
                    if(empty($mem)) {
                        self::save_sign('kakao', $kakao_id, '', $kakao_nickname, $kakao_picture); 
                        redirect('/sign/login', 'refresh');
                        //echo json_encode(error_result('joins'));
                    // 가입이 되어 있으면 로그인 처리
                    } else {
                        if(in_array($mem['status'], array('normal', 'manager'))) {
                            self::save_login($mem['mem_srl'], $this->encryption->decrypt($mem['mem_email']), $mem['mem_name'], $mem['status'], $mem['mem_picture']);
                            redirect('/', 'refresh');
                            //echo json_encode(ok_result());
                        } else {
                            redirect('/sign/login', 'refresh');
                            //echo json_encode(error_result('로그인을 할 수 없는 정보입니다.'));
                        }
                    }
                } else {
                    redirect('/sign/login', 'refresh');
                    //echo json_encode(error_result());
                }
            }

        } else {
            redirect('/sign/login', 'refresh');
        }
    } // }}}

}
