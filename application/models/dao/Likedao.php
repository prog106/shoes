<?php
/**
 * @ description : Like dao
 * @ author : prog106 <prog106@gmail.com>
 */
class Likedao extends CI_Model {
    public function __construct() {
        parent::__construct();
    }

    public function get_like($sql_param) { // {{{
        $this->db->where($sql_param);
        $result = $this->db->get('likes');
        return $result->row_array();
    } // }}}

    // 좋아요
    public function save_like($sql_param) { // {{{
        $this->db->set($sql_param);
        $this->db->insert('likes');
        return $this->db->insert_id();
    } // }}}

    // 좋아요 갱신
    public function update_like($sql_param, $like_srl) { // {{{
        $this->db->set($sql_param);
        $this->db->where('like_srl', $like_srl);
        $this->db->update('likes');
        return $this->db->affected_rows();
    } // }}}

    // 좋아요 한거
    public function get_like_info($sql_param) { // {{{
        $this->db->where($sql_param);
        $result = $this->db->get('likes');
        return $result->result_array();
    } // }}}


    // 댓글
    public function get_answerlike($sql_param) { // {{{
        $this->db->where($sql_param);
        $result = $this->db->get('likes_answer');
        return $result->row_array();
    } // }}}

    // 댓글 좋아요
    public function save_answerlike($sql_param) { // {{{
        $this->db->set($sql_param);
        $this->db->insert('likes_answer');
        return $this->db->insert_id();
    } // }}}

    // 댓글 좋아요 갱신
    public function update_answerlike($sql_param, $like_ans_srl) { // {{{
        $this->db->set($sql_param);
        $this->db->where('like_ans_srl', $like_ans_srl);
        $this->db->update('likes_answer');
        return $this->db->affected_rows();
    } // }}}

}
