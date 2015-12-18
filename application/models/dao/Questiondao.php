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

    // 전체 질문 리스트
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

    // 메인 노출 질문 리스트
    public function get_main_question_list($sql_param) { // {{{
        $this->db->select('Q.*, M.*');
        $this->db->from('question Q');
        $this->db->join('members M', 'M.mem_srl = Q.mem_srl');
        $this->db->where($sql_param);
        $this->db->order_by('que_srl', 'DESC');
        $result = $this->db->get();
        return $result->result_array();
    } // }}}

    // 답변글 +1 업데이트
    public function update_question_answer($que_srl) { // {{{
        $this->db->set('respond', 'respond+1', false);
        $this->db->where('que_srl', $que_srl);
        $this->db->update('question');
        return $this->db->affected_rows();
    } // }}}

    // 답변글 -1 업데이트
    public function update_question_answer_del($que_srl) { // {{{
        $this->db->set('respond', 'respond-1', false);
        $this->db->where('que_srl', $que_srl);
        $this->db->update('question');
        return $this->db->affected_rows();
    } // }}}

    // 좋아요 +1 업데이트
    public function update_question_like($que_srl) { // {{{
        $this->db->set('likes', 'likes+1', false);
        $this->db->where('que_srl', $que_srl);
        $this->db->update('question');
        return $this->db->affected_rows();
    } // }}}

    // 좋아요 -1 업데이트
    public function update_question_dontlike($que_srl) { // {{{
        $this->db->set('likes', 'likes-1', false);
        $this->db->where('que_srl', $que_srl);
        $this->db->update('question');
        return $this->db->affected_rows();
    } // }}}

    // 질문 가져오기
    public function get_question($que_srl) { // {{{
        $this->db->select('Q.*, M.*');
        $this->db->from('question Q');
        $this->db->join('members M', 'M.mem_srl = Q.mem_srl');
        $this->db->where('Q.que_srl', $que_srl);
        $this->db->where('Q.status', 'use');
        $result = $this->db->get();
        return $result->row_array();
    } // }}}

    // 댓글 좋아요 +1 업데이트
    public function update_question_answerlike($ans_srl) { // {{{
        $this->db->set('likes', 'likes+1', false);
        $this->db->where('ans_srl', $ans_srl);
        $this->db->update('answer');
        return $this->db->affected_rows();
    } // }}}

    // 댓글 좋아요 -1 업데이트
    public function update_question_answerdontlike($ans_srl) { // {{{
        $this->db->set('likes', 'likes-1', false);
        $this->db->where('ans_srl', $ans_srl);
        $this->db->update('answer');
        return $this->db->affected_rows();
    } // }}}

}
