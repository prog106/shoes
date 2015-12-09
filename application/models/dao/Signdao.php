<?php
/**
 * @ description : Sign dao
 * @ author : prog106 <prog106@gmail.com>
 */
class Signdao extends CI_Model {
    public function __construct() {
        parent::__construct();
    }

    // 회원 가입 정보 저장
    public function save_member($sql_param) { // {{{
        $this->db->set($sql_param);
        $this->db->insert('members');
        return $this->db->insert_id();
    } // }}}

    // 회원 로그인 하기
    public function login_member($sql_param) { // {{{
        $this->db->select('*');
        $this->db->where('mem_type', $sql_param['mem_type']);
        $this->db->where('mem_email1', $sql_param['mem_email1']);
        $this->db->where('mem_email2', $sql_param['mem_email2']);
        $result = $this->db->get('members');
        return $result->result_array();
    } // }}}

    // 비밀번호 찾기
    public function search_member($sql_param) { // {{{
        $this->db->select('*');
        $this->db->where('mem_email1', $sql_param['mem_email1']);
        $this->db->where('mem_email2', $sql_param['mem_email2']);
        $this->db->where('mem_name', $sql_param['mem_name']);
        $result = $this->db->get('members');
        return $result->result_array();
    } // }}}

    // sns 회원 로그인 하기
    public function sns_login_member($sql_param) { // {{{
        $this->db->select('*');
        $this->db->where($sql_param);
        $result = $this->db->get('members');
        return $result->row_array();
    } // }}}

    // sns 회원 정보 업데이트
    public function sns_update_member($sql_param, $mem_srl) { // }}}
        $where = "mem_srl = '".$mem_srl."'";
        $str = $this->db->update_string('members', $sql_param, $where);
        $result = $this->db->query($str);
        return $this->db->affected_rows();
    } // }}}

}
