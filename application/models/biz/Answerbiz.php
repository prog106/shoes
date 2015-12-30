<?php
/**
 * @ description : Answer biz
 * @ author : prog106 <prog106@gmail.com>
 */
class Answerbiz extends CI_Model {
    public function __construct() {
        parent::__construct();
        $this->load->model('dao/Answerdao', 'answerdao');
    }

    // 답글 저장 처리
    public function answer($answer, $hashtag=null, $mem_srl, $mem_level, $mem_picture=null, $que_srl) { // {{{
        try {
            $this->db->trans_begin();

            // 답글 저장
            $result = self::save_answer($answer, $hashtag, $mem_srl, $mem_level, $mem_picture, $que_srl);
            if($result['result'] === 'error') throw new Exception($result['msg']);
            $step = $result['data'];
            // 질문 글 갱신
            $this->load->model('dao/Questiondao', 'questiondao');
            $result = $this->questiondao->update_question_answer($que_srl);
            if($result['result'] === 'error') throw new Exception('업데이트 에러입니다.');

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

    // 답글 저장
    public function save_answer($answer, $hashtag=null, $mem_srl, $mem_level, $mem_picture=null, $que_srl) { // {{{
        $error_result = error_result('필수값이 누락되었습니다.');
        $sql_param = array();
        if(!empty($que_srl)) $sql_param['que_srl'] = $que_srl;
        else return $error_result;
        if(!empty($mem_srl)) $sql_param['mem_srl'] = $mem_srl;
        else return $error_result;
        if(!empty($mem_level)) $sql_param['mem_level'] = $mem_level;
        else return $error_result;
        if(!empty($mem_picture)) $sql_param['mem_picture'] = $mem_picture;
        if(!empty($answer)) $sql_param['answer'] = $answer;
        else return $error_result;
        $sql_param['hashtag'] = $hashtag;
        $sql_param['create_at'] = YMD_HIS;
        return ok_result($this->answerdao->save_answer($sql_param));
    } // }}}

    // 답글 가져오기
    public function get_answer_list($page=1, $que_srl, $mem_srl=null) { // {{{
        $error_result = error_result('필수값이 누락되었습니다.');
        $sql_param = array();
        if(!empty($que_srl)) $sql_param['A.que_srl'] = $que_srl;
        else return $error_result;
        $sql_param['A.status'] = 'use';
        $limit = 20;
        $paging = ($page-1)*$limit;
        return $this->answerdao->get_answer_list($sql_param, $paging, $limit, $mem_srl);
    } // }}}

    // 답글 삭제 처리
    public function delete_answer($ans_srl, $mem_srl) { // {{{
        try {
            $this->db->trans_begin();

            // 답글 가져오기
            $result = self::get_answer_info($ans_srl);
            if($result['result'] === 'error') throw new Exception($result['msg']);
            $step = $result['data'];
            // 동일 회원
            if($step['mem_srl'] === $mem_srl) {
                // 질문 글 갱신
                $this->load->model('dao/Questiondao', 'questiondao');
                $result = $this->questiondao->update_question_answer_del($step['que_srl']);
                if($result['result'] === 'error') throw new Exception('업데이트 에러입니다.');

                $ans_prm = array();
                $ans_prm['update_at'] = YMD_HIS;
                $ans_prm['status'] = 'delete';
                $this->answerdao->update_answer_info($ans_prm, $ans_srl);
                if($this->db->trans_status() === FALSE) throw new Exception('트랜잭션 오류입니다');
            }
            $this->db->trans_commit();
            return ok_result(true);
        } catch (Exception $e) {
            $this->db->trans_rollback();
            $msg = $e->getMessage();
            return error_result($msg);
        } finally {
        }
    } // }}}

    // 답글 가져오기
    public function get_answer_info($ans_srl) { // {{{
        $sql_param = array();
        if(!empty($ans_srl)) $sql_param['ans_srl'] = $ans_srl;
        else return error_result('필수값이 누락되었습니다');
        $sql_param['status'] = 'use';
        return ok_result($this->answerdao->get_answer_info($sql_param));
    } // }}}

}
