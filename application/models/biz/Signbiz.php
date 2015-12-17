<?php
/**
 * @ description : Sign biz
 * @ author : prog106 <prog106@gmail.com>
 */
class Signbiz extends CI_Model {
    public function __construct() {
        parent::__construct();
        $this->load->model('dao/Signdao', 'signdao');
    }

    // sns 회원가입
    public function sns_member($mem_type, $efs_srl, $email, $name, $picture=null) { // {{{
        $sns_prm = array();
        $sns_prm = array(
            'mem_type' => $mem_type,
            'efs_srl' => $efs_srl,
        );
        $mem = $this->signdao->sns_login_member($sns_prm);
        $mem_info = array();
        if(!empty($mem)) {
            $name_prm = array();
            $mem_info['mem_srl'] = $mem['mem_srl'];
            $mem_info['level'] = $mem['status'];
            $mem_info['mem_name'] = $mem['mem_name'];
            $mem_info['mem_picture'] = $mem['mem_picture'];
            if($mem['mem_name'] !== $name) {
                $name_prm['mem_name'] = $name;
                $mem_info['mem_name'] = $name;
            }
            if($mem['mem_picture'] !== $picture) {
                $name_prm['mem_picture'] = $picture;
                $mem_info['mem_picture'] = $picture;
            }
            if(!empty($name_prm)) {
                $this->signdao->sns_update_member($name_prm, $mem['mem_srl']);
            }
        } else {
            $mem_srl = self::save_member($mem_type, $efs_srl, $mem_type.$efs_srl, $mem_type, $mem_type, $name, $email, $picture);
            $mem_info['mem_srl'] = $mem_srl;
            $mem_info['level'] = 'normal';
            $mem_info['mem_name'] = $name;
            $mem_info['mem_picture'] = $picture;
        }
        $result = ok_result($mem_info);
        return $result;
    } // }}}

    // 회원 가입 처리
    public function sign_member($mem_type, $efs_srl, $email1, $email2, $pwd, $name, $email) { // {{{
        try {
            $this->db->trans_begin();

            // 회원 가입 저장
            $result = self::save_member($mem_type, $efs_srl, $email1, $email2, $pwd, $name, $email);
            if($result['result'] === 'error') throw new Exception($result['msg']);
            $step = $result['data'];
            // efs 정보 갱신
            $this->load->model('dao/Emailforsigndao', 'emailforsigndao');
            $efs_prm = array();
            $efs_prm['efs_status'] = 'auth';
            $efs_prm['efs_code'] = '';
            $this->emailforsigndao->update_emailforsign($efs_prm, $efs_srl);

            if($this->db->trans_status() === FALSE) throw new Exception('트랜잭션 오류입니다');

            $this->db->trans_commit();
            return ok_result(true);
        } catch (Exception $e) {
            $this->db->trans_rollback();
            $msg = $e->getMessage();
            return error_result($msg);
        } finally {
        }
    } // }}}

    // 회원 가입 저장
    public function save_member($mem_type, $efs_srl, $email1, $email2, $pwd, $name, $email, $picture=null) { // {{{
        $error_result = error_result('필수값이 누락되었습니다.');
        $sql_param = array();
        if(!empty($mem_type)) $sql_param['mem_type'] = $mem_type;
        else return $error_result;
        if(!empty($efs_srl)) $sql_param['efs_srl'] = $efs_srl;
        else return $error_result;
        if(!empty($email1)) $sql_param['mem_email1'] = $email1;
        else return $error_result;
        if(!empty($email2)) $sql_param['mem_email2'] = $email2;
        else return $error_result;
        if(!empty($pwd)) $sql_param['mem_pwd'] = $pwd;
        else return $error_result;
        if(!empty($name)) $sql_param['mem_name'] = $name;
        else return $error_result;
        if(!empty($email)) $sql_param['mem_email'] = $email;
        else return $error_result;
        if(!empty($picture)) $sql_param['mem_picture'] = $picture;
        $sql_param['create_datetime'] = YMD_HIS;
        return ok_result($this->signdao->save_member($sql_param));
    } // }}}

    // 로그인
    public function login_member($email1, $email2) { // {{{
        $error_result = error_result('필수값이 누락되었습니다.');
        $sql_param = array();
        $sql_param['mem_type'] = 'efs';
        if(!empty($email1)) $sql_param['mem_email1'] = $email1;
        else return $error_result;
        if(!empty($email2)) $sql_param['mem_email2'] = $email2;
        else return $error_result;
        return ok_result($this->signdao->login_member($sql_param));
    } // }}}

}
