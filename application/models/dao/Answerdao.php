<?php
/**
 * @ description : Answer dao
 * @ author : prog106 <prog106@gmail.com>
 */
class Answerdao extends CI_Model {
    public function __construct() {
        parent::__construct();
    }

    // 답글 저장
    public function save_answer($sql_param) { // {{{
        $this->db->set($sql_param);
        $this->db->insert('answer');
        return $this->db->insert_id();
    } // }}}

    // 답글 리스트
    public function get_answer_list($sql_param, $paging, $limit) { // {{{
        $this->db->select('A.*, M.*');
        $this->db->from('answer A');
        $this->db->join('members M', 'M.mem_srl = A.mem_srl');
        $this->db->where($sql_param);
        $this->db->order_by('A.que_srl', 'DESC');
        $this->db->limit($limit, $paging);
        $result = $this->db->get();
        return $result->result_array();
    } // }}}

    // 답글 업데이트
    public function update_answer($sql_param, $que_srl) { // {{{
        $this->db->set($sql_param);
        $this->db->where('que_srl', $que_srl);
        $this->db->update('answer');
        return $this->db->affected_rows();
    } // }}}

}
