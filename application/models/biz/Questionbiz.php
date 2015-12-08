<?php
/**
 * @ description : Question biz
 * @ author : prog106 <prog106@gmail.com>
 */
class Questionbiz extends CI_Model {
    public function __construct() {
        parent::__construct();
        $this->load->model('dao/Questiondao', 'questiondao');
    }

    // 질문 저장
    public function save_question($question, $mem_srl) { // {{{
        $error_result = error_result('필수값이 누락되었습니다.');
        $sql_param = array();
        if(!empty($mem_srl)) $sql_param['mem_srl'] = $mem_srl;
        else return $error_result;
        if(!empty($question)) $sql_param['question'] = $question;
        else return $error_result;
        $sql_param['create_at'] = YMD_HIS;
        return ok_result($this->questiondao->save_question($sql_param));
    } // }}}

    public function get_question_list($page=1) { // {{{
        $sql_param = array();
        $sql_param['status'] = 'use';
        $limit = 10;
        $paging = ($page-1)*$limit;
        return $this->questiondao->get_question_list($sql_param, $paging, $limit);
    } // }}}

}
