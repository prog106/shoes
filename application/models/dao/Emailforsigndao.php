<?php
/**
 * @ description : Email for sign dao
 * @ author : prog106 <prog106@gmail.com>
 */
class Emailforsigndao extends CI_Model {
    public function __construct() {
        parent::__construct();
    }

    // 이메일 발송을위한 이메일 정보 저장/있으면 갱신
    public function save_emailforsign($sql_param) { // {{{
        $sql = "INSERT INTO email_for_sign (email1, email2, efs_code, start_datetime, end_datetime) VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE efs_code = VALUES(efs_code), start_datetime = VALUES(start_datetime), end_datetime = VALUES(end_datetime)";
        $this->db->query($sql, $sql_param);
        return $this->db->affected_rows();
    } // }}}

    // 상태 갱신 - status 등
    public function update_emailforsign($sql_param, $efs_srl) { // {{{
        $this->db->set($sql_param);
        $this->db->where('efs_srl', $efs_srl);
        $this->db->update('email_for_sign');
        return $this->db->affected_rows();
    } // }}}

    // 정보 조회
    public function get_emailforsign($sql_param) { // {{{
        $this->db->select('*');
        $this->db->where($sql_param);
        $result = $this->db->get('email_for_sign');
        return $result->result_array();
    } // }}}

}
