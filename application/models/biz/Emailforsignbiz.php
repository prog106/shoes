<?php
/**
 * @ description : Email for sign biz
 * @ author : prog106 <prog106@gmail.com>
 */
class Emailforsignbiz extends CI_Model {
    public function __construct() {
        parent::__construct();
        $this->load->model('dao/Emailforsigndao', 'emailforsigndao');
    }

    // 이메일 발송을 위한 중복 데이터 확인하기
    public function check_emailforsign($email1, $email2, $efs_code, $start_datetime, $end_datetime) { // {{{
        try {
            // 트랜잭션 시작
            $this->db->trans_begin();

            // step 이메일 주소로 정보 가져오기
            $result = self::get_emailforsign($email1, $email2);
            if($result['result'] == 'error') throw new Exception($result['msg']);
            $step = $result['data'];
            if(!empty($step)) {
                if($step['efs_status'] === 'auth') {
                    throw new Exception('가입된 이메일입니다.');
                } else {
                    self::save_emailforsign($email1, $email2, $efs_code, $start_datetime, $end_datetime);
                }
            } else {
                self::save_emailforsign($email1, $email2, $efs_code, $start_datetime, $end_datetime);
            }

            if($this->db->trans_status() === FALSE) throw new Exception('트랜잭션 오류입니다.');

            $this->db->trans_commit();
            return ok_result(true);
        } catch(Exception $e) {
            $this->db->trans_rollback();
            $msg = $e->getMessage();
            return error_result($msg);
        } finally {
        }
    } // }}}

    // 이메일 발송을위한 이메일 정보 저장/있으면 갱신
    public function save_emailforsign($email1, $email2, $efs_code, $start_datetime, $end_datetime) { // {{{
        $error_result = error_result('필수값이 누락되었습니다.');
        $sql_param = array();
        if(!empty($email1)) $sql_param['email1'] = $email1;
        else return $error_result;
        if(!empty($email2)) $sql_param['email2'] = $email2;
        else return $error_result;
        if(!empty($efs_code)) $sql_param['efs_code'] = $efs_code;
        else return $error_result;
        if(!empty($start_datetime)) $sql_param['start_datetime'] = $start_datetime;
        else return $error_result;
        if(!empty($end_datetime)) $sql_param['end_datetime'] = $end_datetime;
        else return $error_result;

        return ok_result($this->emailforsigndao->save_emailforsign($sql_param));
    } // }}}

    // 상태 갱신 - status 등
    public function update_emailforsign($efs_srl=null, $email1, $email2, $efs_code, $start_datetime, $end_datetime) { // {{{
        $error_result = error_result('필수값이 누락되었습니다.');
        $sql_param = array();
        if(empty($efs_srl)) return $error_result;
        if(!empty($email1)) $sql_param['email1'] = $email1;
        if(!empty($email2)) $sql_param['email2'] = $email2;
        if(!empty($efs_code)) $sql_param['efs_code'] = $efs_code;
        if(!empty($start_datetime)) $sql_param['start_datetime'] = $start_datetime;
        if(!empty($end_datetime)) $sql_param['end_datetime'] = $end_datetime;

        return ok_result($this->emailforsigndao->update_emailforsign($sql_param, $efs_srl));
    } // }}}

    // 정보 조회
    public function get_emailforsign($email1=null, $email2=null, $efs_code=null, $start_datetime=null, $end_datetime=null) { // {{{
        $error_result = error_result('필수값이 누락되었습니다.');
        $sql_param = array();
        if(!empty($email1)) $sql_param['email1'] = $email1;
        if(!empty($email2)) $sql_param['email2'] = $email2;
        if(!empty($efs_code)) $sql_param['efs_code'] = $efs_code;
        if(empty($sql_param)) return $error_result;
        if(!empty($start_datetime)) $sql_param['start_datetime'] = $start_datetime;
        if(!empty($end_datetime)) $sql_param['end_datetime'] = $end_datetime;

        return ok_result($this->emailforsigndao->get_emailforsign($sql_param));
    } // }}}

}
