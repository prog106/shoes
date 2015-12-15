<?php
/**
 * @ description : Like biz
 * @ author : prog106 <prog106@gmail.com>
 */
class Likebiz extends CI_Model {
    public function __construct() {
        parent::__construct();
        $this->load->model('dao/Likedao', 'likedao');
    }

    // 좋아요 처리
    public function like($mem_srl, $que_srl) { // {{{
        try {
            $this->db->trans_begin();

            $like_prm = array();
            $like_prm['que_srl'] = $que_srl;
            $like_prm['mem_srl'] = $mem_srl;
            //$like_prm['likes'] = 'like';
            $like_prm['status'] = 'use';
            $like = $this->likedao->get_like($like_prm);
            if(empty($like)) {
                // 저장
                $result = self::save_like($mem_srl, $que_srl);
                if($result['result'] === 'error') throw new Exception($result['msg']);
            } else if(!empty($like) && $like['likes'] == 'dontlike') {
                // 갱신
                $result = self::update_like($like['like_srl']);
                if($result['result'] === 'error') throw new Exception($result['msg']);
            } else {
                // 이미 좋아요를 했어요
                $this->db->trans_commit();
                return ok_result('already');
            }
            $step = $result['data'];
            // 갱신
            $this->load->model('dao/Questiondao', 'questiondao');
            $result = $this->questiondao->update_question_like($que_srl);
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

    // 좋아요 등록 
    public function save_like($mem_srl, $que_srl) { // {{{
        $error_result = error_result('필수값이 누락되었습니다.');
        $sql_param = array();
        if(!empty($que_srl)) $sql_param['que_srl'] = $que_srl;
        else return $error_result;
        if(!empty($mem_srl)) $sql_param['mem_srl'] = $mem_srl;
        else return $error_result;
        $sql_param['likes'] = 'like';
        $sql_param['create_at'] = YMD_HIS;
        return ok_result($this->likedao->save_like($sql_param));
    } // }}}

    // 좋아요 갱신
    public function update_like($like_srl) { // {{{
        $error_result = error_result('필수값이 누락되었습니다.');
        $sql_param = array();
        if(empty($like_srl)) return $error_result;
        $sql_param['likes'] = 'like';
        $sql_param['update_at'] = YMD_HIS;
        return ok_result($this->likedao->update_like($sql_param, $like_srl));
    } // }}}

    // 좋아요 취소 처리
    public function dontlike($mem_srl, $que_srl) { // {{{
        try {
            $this->db->trans_begin();

            $like_prm = array();
            $like_prm['que_srl'] = $que_srl;
            $like_prm['mem_srl'] = $mem_srl;
            $like_prm['likes'] = 'like';
            $like_prm['status'] = 'use';
            $like = $this->likedao->get_like($like_prm);
            if(!empty($like)) {
                // 저장
                $result = self::save_dontlike($like['like_srl']);
                if($result['result'] === 'error') throw new Exception($result['msg']);
                $step = $result['data'];
                // 갱신
                $this->load->model('dao/Questiondao', 'questiondao');
                $result = $this->questiondao->update_question_dontlike($que_srl);
                if($result['result'] === 'error') throw new Exception('업데이트 에러입니다.');

                if($this->db->trans_status() === FALSE) throw new Exception('트랜잭션 오류입니다');

                $this->db->trans_commit();
                return ok_result(true);
            } else {
                // 이미 좋아요 취소를 했어요
                $this->db->trans_commit();
                return ok_result('already');
            }
        } catch (Exception $e) {
            $this->db->trans_rollback();
            $msg = $e->getMessage();
            return error_result($msg);
        } finally {
        }
    } // }}}

    // 좋아요 취소
    public function save_dontlike($like_srl) { // {{{
        $error_result = error_result('필수값이 누락되었습니다.');
        $sql_param = array();
        if(empty($like_srl)) return $error_result;
        $sql_param['likes'] = 'dontlike';
        $sql_param['update_at'] = YMD_HIS;
        return ok_result($this->likedao->update_like($sql_param, $like_srl));
    } // }}}

    // 좋아요 가져오기
    public function get_like_info($mem_srl, $que_srls) { // {{{
        $error_result = error_result('필수값이 누락되었습니다.');
        $sql_param = array();
        if(!empty($que_srls)) $sql_param['que_srl IN ('.implode(",",$que_srls).')'] = null;
        else return $error_result;
        if(!empty($mem_srl)) $sql_param['mem_srl'] = $mem_srl;
        else return $error_result;
        $sql_param['likes'] = 'like';
        $sql_param['status'] = 'use';
        return $this->likedao->get_like_info($sql_param);
    } // }}}

    // 댓글 좋아요 처리
    public function answerlike($mem_srl, $ans_srl) { // {{{
        try {
            $this->db->trans_begin();

            $like_prm = array();
            $like_prm['ans_srl'] = $ans_srl;
            $like_prm['mem_srl'] = $mem_srl;
            //$like_prm['likes'] = 'like';
            $like_prm['status'] = 'use';
            $like = $this->likedao->get_answerlike($like_prm);
            if(empty($like)) {
                // 저장
                $result = self::save_answerlike($mem_srl, $ans_srl);
                if($result['result'] === 'error') throw new Exception($result['msg']);
            } else if(!empty($like) && $like['likes'] == 'dontlike') {
                // 갱신
                $result = self::update_answerlike($like['like_ans_srl']);
                if($result['result'] === 'error') throw new Exception($result['msg']);
            } else {
                // 이미 좋아요를 했어요
                $this->db->trans_commit();
                return ok_result('already');
            }
            $step = $result['data'];
            // 갱신
            $this->load->model('dao/Questiondao', 'questiondao');
            $result = $this->questiondao->update_question_answerlike($ans_srl);
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

    // 댓글 좋아요 등록 
    public function save_answerlike($mem_srl, $ans_srl) { // {{{
        $error_result = error_result('필수값이 누락되었습니다.');
        $sql_param = array();
        if(!empty($ans_srl)) $sql_param['ans_srl'] = $ans_srl;
        else return $error_result;
        if(!empty($mem_srl)) $sql_param['mem_srl'] = $mem_srl;
        else return $error_result;
        $sql_param['likes'] = 'like';
        $sql_param['create_at'] = YMD_HIS;
        return ok_result($this->likedao->save_answerlike($sql_param));
    } // }}}

    // 댓글 좋아요 갱신
    public function update_answerlike($like_ans_srl) { // {{{
        $error_result = error_result('필수값이 누락되었습니다.');
        $sql_param = array();
        if(empty($like_ans_srl)) return $error_result;
        $sql_param['likes'] = 'like';
        $sql_param['update_at'] = YMD_HIS;
        return ok_result($this->likedao->update_answerlike($sql_param, $like_ans_srl));
    } // }}}

    // 댓글 좋아요 취소 처리
    public function answerdontlike($mem_srl, $ans_srl) { // {{{
        try {
            $this->db->trans_begin();

            $like_prm = array();
            $like_prm['ans_srl'] = $ans_srl;
            $like_prm['mem_srl'] = $mem_srl;
            $like_prm['likes'] = 'like';
            $like_prm['status'] = 'use';
            $like = $this->likedao->get_answerlike($like_prm);
            if(!empty($like)) {
                // 저장
                $result = self::save_answerdontlike($like['like_ans_srl']);
                if($result['result'] === 'error') throw new Exception($result['msg']);
                $step = $result['data'];
                // 갱신
                $this->load->model('dao/Questiondao', 'questiondao');
                $result = $this->questiondao->update_question_answerdontlike($ans_srl);
                if($result['result'] === 'error') throw new Exception('업데이트 에러입니다.');

                if($this->db->trans_status() === FALSE) throw new Exception('트랜잭션 오류입니다');

                $this->db->trans_commit();
                return ok_result(true);
            } else {
                // 이미 좋아요 취소를 했어요
                $this->db->trans_commit();
                return ok_result('already');
            }
        } catch (Exception $e) {
            $this->db->trans_rollback();
            $msg = $e->getMessage();
            return error_result($msg);
        } finally {
        }
    } // }}}

    // 댓글 좋아요 취소
    public function save_answerdontlike($like_ans_srl) { // {{{
        $error_result = error_result('필수값이 누락되었습니다.');
        $sql_param = array();
        if(empty($like_ans_srl)) return $error_result;
        $sql_param['likes'] = 'dontlike';
        $sql_param['update_at'] = YMD_HIS;
        return ok_result($this->likedao->update_answerlike($sql_param, $like_ans_srl));
    } // }}}

}
