<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @ description : load view with common body +(header, left, bottom)
 * @ author : Yejun Kim <serendip@inkomaro.com>
 */
if(!function_exists('load_view')) {
	function load_view($view_file, $data = array()){
		$CI =& get_instance();
		$data['view_file'] = $view_file;
        $CI->load->view('common/body',$data);
        if((ENVIRONMENT === 'development' || ENVIRONMENT === 'dqa') && stripos($CI->input->server('QUERY_STRING'), 'VIEW_PROFILER') !== false)
            $CI->output->enable_profiler(true);
	}
}
