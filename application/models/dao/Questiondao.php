<?php
/**
 * @ description : Question dao
 * @ author : prog106 <prog106@gmail.com>
 */
class Questiondao extends CI_Model {
    public function __construct() {
        parent::__construct();
    }

    // 질문 저장
    public function save_question($sql_param) { // {{{
        $this->db->set($sql_param);
        $this->db->insert('question');
        return $this->db->insert_id();
    } // }}}

    // 질문 리스트
    public function get_question_list($sql_param, $paging, $limit) { // {{{
        $this->db->select('Q.*, M.*');
        $this->db->from('question Q');
        $this->db->join('members M', 'M.mem_srl = Q.mem_srl');
        $this->db->where($sql_param);
        $this->db->order_by('que_srl', 'DESC');
        $this->db->limit($limit, $paging);
        $result = $this->db->get();
        return $result->result_array();
    } // }}}

}
