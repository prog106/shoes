<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if(!function_exists('ok_result')) {
    function ok_result($data=array(), $msg='정상처리') { // {{{
        return array('result' => 'ok', 'msg' => $msg, 'data' => $data);
    } // }}}
}

if(!function_exists('error_result')) {
    function error_result($msg='에러입니다.', $data=array()) { // {{{
        return array('result' => 'error', 'msg' => $msg, 'data' => $data);
    } // }}}
}

if(!function_exists('lang')) {
    function lang($en, $id=null) { // {{{
        $mode = 'view';
        if(empty($en)) return '';
        $local = (!empty($_COOKIE['languages'])) ? $_COOKIE['languages'] : 'english' ;

        // 영어, 인도네시아어 같이 들어올 경우 번역안타기
        if(!empty($en) && !empty($id)) {
            if(!empty($id) && $local == 'indonesian') {
                return $id;
            } else {
                return $en;
            }
        }

        if($local === 'translate-e'){
            $local = 'english';
            $mode = 'translate';
        }

        // 가져와서 memcache 로?
        $CI =& get_instance();
        $CI->load->driver('cache');
        $res = $CI->cache->memcached->get('langs');
        if(empty($res)) {
            $res = array();
            $sql = "SELECT * FROM langs";
            $langs = $CI->load->database('lang', TRUE);
            $result = $langs->query($sql);
            $return = $result->result_array();
            foreach($return as $k => $v) {
                $res['english'][strtolower($v['korean'])] = $v['english'];
                $res['indonesian'][strtolower($v['korean'])] = $v['indonesian'];
            }
            $CI->cache->memcached->save('langs', $res, 60*30);
        }

        if(!isset($res[$local]) || !array_key_exists(strtolower($en), $res[$local])) { // 없는 경우
            // DB에 저장
            $lang_prm['korean'] = $en;
            if(ENVIRONMENT !== 'development') {
                $langs = $CI->load->database('lang', TRUE);
                $query = $langs->insert_string('langs', $lang_prm);
                $query = str_replace("INSERT INTO", "INSERT IGNORE INTO", $query);
                $langs->query($query);
            }
            if($mode == 'translate'){
                return '<code>['.$en.']</code> : ';
            }
            else{
                return $en;
            }
        } else {
            if($mode == 'translate'){
                return (empty($res[$local][strtolower($en)])) ? '<code>['.$en.']</code> : ' : '<code>['.$en.']</code> : '.$res[$local][strtolower($en)];
            }
            else{
                return (empty($res[$local][strtolower($en)])) ? $en : $res[$local][strtolower($en)];
            }
        }
    } // }}}
}

// 팝업창이면 팝업 닫고
// 아니면 URL을 replace 하거나, back();
if(!function_exists('alertmsg_move')) {
    function alertmsg_move($msg, $url=''){ // {{{
        echo "<html>";
        echo "<head>";
        echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>";
        echo "</head>";
        echo "<body>";
        echo "<script>";
        echo "alert('".$msg."');";
        echo "if(window.opener){";
        echo "window.close();";
        echo "}else{";
        if(empty($url))
            echo "history.go(-1);";
        else
            echo "location.replace('".$url."');";
        echo "}";
        echo "</script>";
        echo "</body>";
        echo "</html>";
        exit;
    } // }}}
}

// 팝업창이면 팝업 닫고
// 아니면 URL을 replace 하거나, back();
if(!function_exists('close_reload')) {
    function close_reload($url=''){ // {{{
        echo "<html>";
        echo "<head>";
        echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>";
        echo "</head>";
        echo "<body>";
        echo "<script>";
        if(empty($url)) {
            echo "opener.location.reload();";
        } else {
            echo "opener.location.replace('".$url."');";
        }
        echo "window.close();";
        echo "</script>";
        echo "</body>";
        echo "</html>";
        exit;
    } // }}}
}


if(!function_exists('add_month')) {
    function add_month($ymd_his, $i) { // {{{
        // +i 번째 달의 마지막 날
        $last_day_of_i_th_month = date("t", strtotime(date("Y-m-01", strtotime($ymd_his))." +".$i." month"));
        // 오늘 날짜랑 비교해서 오늘날짜가 더 크면... 이달 1일의 다음달의 마지막날로 넣어줌.. 헥헥
        if(date("d", strtotime($ymd_his)) > $last_day_of_i_th_month){
            return date("Y-m-t H:i:s", strtotime(date("Y-m-01", strtotime($ymd_his))." +".$i." month"));
        }else{
            return date("Y-m-d H:i:s", strtotime($ymd_his." +".$i." month"));
        }
    } // }}}
}


// 주문시 필요한 memcache key 생성
if(!function_exists('generate_order_key')) {
    function generate_order_key(){ // {{{
        if(!defined('COMP_SRL')){
            return FALSE;
        }
        else{
            $order_key = '';
            $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
            $order_key = COMP_SRL.time();
            for($i=0; $i<5; $i++){
                $order_key .= $characters[rand(0, strlen($characters) - 1)];
            }

            return $order_key;
        }
    } // }}}
}

if(!function_exists('file_copy')) {
    /*
    * @param $file_list_arr = 
    *                       array(
    *                            array('file'=>'업로드된 파일 경로', 'folder'=>'업로드할 uploads 하위 폴더명', 'prefix'=>'파일 앞에 붙일 prefix', 'suffix'=>'파일 뒤에 붙일 suffix'),
    *                            array('file'=>'업로드된 파일 경로', 'folder'=>'업로드할 uploads 하위 폴더명', 'prefix'=>'파일 앞에 붙일 prefix', 'suffix'=>'파일 뒤에 붙일 suffix'),
    *                            array('file'=>'업로드된 파일 경로', 'folder'=>'업로드할 uploads 하위 폴더명', 'prefix'=>'파일 앞에 붙일 prefix', 'suffix'=>'파일 뒤에 붙일 suffix'),
    *                       )
    * @return $file_list_arr = 
    *                       array(
    *                           array('file'=>'업로드된 파일 경로', 'folder'=>'업로드할 uploads 하위 폴더명', 'prefix'=>'파일 앞에 붙일 prefix', 'suffix'=>'파일 뒤에 붙일 suffix', 'ret'=>결과, 'msg'=>결과문구, 'new_file'=>이동된 파일)
    *                           array('file'=>'업로드된 파일 경로', 'folder'=>'업로드할 uploads 하위 폴더명', 'prefix'=>'파일 앞에 붙일 prefix', 'suffix'=>'파일 뒤에 붙일 suffix', 'ret'=>결과, 'msg'=>결과문구, 'new_file'=>이동된 파일)
    *                           array('file'=>'업로드된 파일 경로', 'folder'=>'업로드할 uploads 하위 폴더명', 'prefix'=>'파일 앞에 붙일 prefix', 'suffix'=>'파일 뒤에 붙일 suffix', 'ret'=>결과, 'msg'=>결과문구, 'new_file'=>이동된 파일)
    *                       )
    *
    */
    function file_copy($file_list_arr) { // {{{
        $upload_root = $_SERVER['DOCUMENT_ROOT'];
        foreach($file_list_arr as $idx=>$file_row){
            if(empty($file_row['file'])){
                $file_row['ret'] = 'nok';
                $file_row['msg'] = 'no file';
            }
            else{
                $upload_path = '/uploads';
                $original_file_name = $file_row['file'];
                $folder_name_arr = array_key_exists('folder_arr', $file_row) ? $file_row['folder_arr'] : '';
                $file_name_prefix = array_key_exists('prefix', $file_row) ? $file_row['prefix'] : '';
                $file_name_suffix = array_key_exists('suffix', $file_row) ? $file_row['suffix'] : '';
                
                if(!empty($folder_name_arr)){
                    if(is_array($folder_name_arr)){
                        foreach($folder_name_arr as $folder_name){
                            $upload_path .= '/'.$folder_name;
                            if(!is_dir($upload_root.$upload_path)){
                                mkdir($upload_root.$upload_path,0777,TRUE);
                                chmod($upload_root.$upload_path, 0777);
                            }
                        }
                    }
                    else{
                        $upload_path .= '/'.$folder_name;
                        if(!is_dir($upload_root.$upload_path)){
                            mkdir($upload_root.$upload_path,0777,TRUE);
                            chmod($upload_root.$upload_path, 0777);
                        }
                    }
                }
                
                $original_file_ext = explode('.', $original_file_name);
                $original_file_ext =  $original_file_ext[count($original_file_ext)-1];
                $micro_time = round(array_sum(explode(' ', microtime())), 3);
                $new_file_name = $file_name_prefix.($idx.'_'.str_replace('.', '', $micro_time)).$file_name_suffix.'.'.$original_file_ext;

                if (!copy($upload_root.$original_file_name, $upload_root.$upload_path.'/'.$new_file_name)) { // 파일 카피 실패
                    $file_row['ret'] = 'nok';
                    $file_row['msg'] = 'file upload fail';
                    continue;
                }

                $file_row['ret'] = 'ok';
                $file_row['msg'] = 'copy success';
                $file_row['new_file'] = $upload_path.'/'.$new_file_name;
            }

            $file_list_arr[$idx] = $file_row;

        }

        return $file_list_arr;
    } // }}}
}

// 앞에 prefix 붙여주기
// purchase => P, product => #
if(!function_exists('add_prefix')) {
    function add_prefix($str, $type='product'){ // {{{
        $prefix = '';
        if($type === 'purchase') $prefix = 'P';
        else if($type === 'product') $prefix = '#';

        if($str && $prefix){
            if(strpos(strtoupper($str), $prefix) !== 0){
                $str = $prefix.$str;
            }
        }
        return $str;
    } // }}}
}

// 앞에 prefix 빼주기
if(!function_exists('remove_prefix')) {
    function remove_prefix($str, $type='product'){ // {{{
        $prefix = '';
        if($type === 'purchase') $prefix = 'P';
        else if($type === 'product') $prefix = '#';

        if($str && $prefix){
            if(strpos(strtoupper($str), $prefix) === 0){
                $str = substr($str, strlen($prefix));
            }
        }
        return $str;
    } // }}}
}

if(!function_exists('number_format_id')) {
    function number_format_id($number){ // {{{
        return number_format($number, 0, ',', '.');
    } // }}}
}

if(!function_exists('unumber_format_id')) {
    function unumber_format_id($str){ // {{{
        return str_replace(',', '.', str_replace('.', '', $str));
    } // }}}
}

if(!function_exists('money_format_id')) {
    function money_format_id($number){ // {{{
        return number_format($number, 0, ',', '.');
    } // }}}
}

if(!function_exists('unmoney_format_id')) {
    function unmoney_format_id($str){ // {{{
        return str_replace(',', '.', str_replace('.', '', $str));
    } // }}}
}

if(!function_exists('translate_status')) {
    function translate_status($status, $field){ // {{{
        $status_text = $status;
        switch ($field) {
            case 'po_status': //결재중(request), 주문확정(order), 배송중(delivery) - PL이 1개이상 배송중일 때, 입고완료(receive) - PL이 ALL 입고완료일 때, 취소(cancel) - PL이 ALL 취소일 때
                switch ($status) {
                    case 'deny':
                        $status_text = lang('결재 반려');
                        break;
                    case 'request':
                        $status_text = lang('결재중');
                        break;
                    case 'order':
                        $status_text = lang('주문확정');
                        break;
                    case 'delivery':
                        $status_text = lang('배송중');
                        break;
                    case 'receive':
                        $status_text = lang('입고완료');
                        break;
                    case 'cancel':
                        $status_text = lang('취소');
                        break;
                }
                break;
            case 'po_line_status': //결재중(request), 주문확정(order), 배송중(delivery), 입고완료(receive), 취소(cancel) - 구매사에서만 취소 가능
                switch ($status) {
                    case 'deny':
                        $status_text = lang('결재 반려');
                        break;
                    case 'request':
                        $status_text = lang('결재중');
                        break;
                    case 'order':
                        $status_text = lang('주문확정');
                        break;
                    case 'delivery':
                        $status_text = lang('배송중');
                        break;
                    case 'receive':
                        $status_text = lang('입고완료');
                        break;
                    case 'cancel':
                        $status_text = lang('취소');
                        break;
                }
                break;
            case 'po_deli_status': //배송중(delivery), 입고완료(receive)
                switch ($status) {
                    case 'delivery':
                        $status_text = lang('배송중');
                        break;
                    case 'receive':
                        $status_text = lang('입고완료');
                        break;
                }
                break;
            case 'appr_status': //request, approval, deny
                switch ($status) {
                    case 'request':
                        $status_text = lang('결재중');
                        break;
                    case 'approval':
                        $status_text = lang('결재 승인');
                        break;
                    case 'deny':
                        $status_text = lang('결재 반려');
                        break;
                }
                break;
            case 'appr_det_status': //ready(결재순서가 아님), request(결재해 주세요), approval(결재완료), deny(반려)
                switch ($status) {
                    case 'ready':
                        $status_text = lang('결재 전');
                        break;
                    case 'request':
                        $status_text = lang('결재 대기중');
                        break;
                    case 'approval':
                        $status_text = lang('결재 승인');
                        break;
                    case 'deny':
                        $status_text = lang('결재 반려');
                        break;
                }
                break;
        }
        return $status_text;
    } // }}}
}
