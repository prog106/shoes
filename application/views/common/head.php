<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<?
if(!empty($question)) {
?>
    <meta property="og:title" content="<?=convert_text($question['question'], false)?>" />
    <meta property="og:url" content="http://shoes.prog106.indoproc.xyz<?=$this->input->server('REQUEST_URI', true)?>" />
    <meta property="og:image" content="http://shoes.prog106.indoproc.xyz/static/img/komment.png" />
<?
}
?>
	<title>응답하라</title>
    <script src="/static/js/jquery-1.11.3.min.js"></script>
    <script src="/static/js/shoes.util.js"></script>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="/static/css/bootstrap.min.css">

    <!-- Optional theme -->
    <link rel="stylesheet" href="/static/css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="/static/css/bootstrap-datepicker.css">
    <link rel="stylesheet" href="/static/css/jasny-bootstrap.min.css">
    <link rel="stylesheet" href="/static/css/common.css">

    <!-- Latest compiled and minified JavaScript -->
    <script src="/static/js/bootstrap.min.js"></script>
    <script src="/static/js/bootstrap-datepicker.js"></script>
    <script src="/static/js/moment.min.js"></script>
    <script src="/static/js/bootstrap-datetimepicker.min.js"></script>
    <script src="/static/js/jasny-bootstrap.min.js"></script>
    <script src="/static/js/iscroll.js"></script>
    <script src="/static/js/sp-slidemenu.js"></script>
    <script src="/static/js/swiper.jquery.min.js"></script>
</head>
<body role="document">
