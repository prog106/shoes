<?
$this->load->view('common/head');
$this->load->view('common/navbar');

if(isset($view_file)) $this->load->view($view_file);
$this->load->view('common/footer');
?>
