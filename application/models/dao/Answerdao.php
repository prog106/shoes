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
    public function get_answer_list($sql_param, $paging, $limit, $mem_srl) { // {{{
        if(!empty($mem_srl)) {
            $this->db->select('A.*, M.mem_name, LA.ans_srl AS la_srl');
        } else {
            $this->db->select('A.*, M.mem_name');
        }
        $this->db->from('answer A');
        $this->db->join('members M', 'M.mem_srl = A.mem_srl');
        if(!empty($mem_srl)) {
            $this->db->join('likes_answer LA', 'A.ans_srl = LA.ans_srl AND LA.likes = \'like\' AND LA.mem_srl = '.$mem_srl, 'left');
        }
        $this->db->where($sql_param);
        $this->db->order_by('A.likes DESC, A.ans_srl ASC');
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

    // 답글 업데이트 - 삭제
    public function update_answer_info($sql_param, $ans_srl) { // {{{
        $this->db->set($sql_param);
        $this->db->where('ans_srl', $ans_srl);
        $this->db->update('answer');
        return $this->db->affected_rows();
    } // }}}

    // 답글 가져오기
    public function get_answer_info($sql_param) { // {{{
        $this->db->where($sql_param);
        $result = $this->db->get('answer');
        return $result->row_array();
    } // }}}
}
