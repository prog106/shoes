<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Welcome to CodeIgniter</title>

	<style type="text/css">

	::selection { background-color: #E13300; color: white; }
	::-moz-selection { background-color: #E13300; color: white; }

	body {
		background-color: #fff;
		margin: 40px;
		font: 13px/20px normal Helvetica, Arial, sans-serif;
		color: #4F5155;
	}

	a {
		color: #003399;
		background-color: transparent;
		font-weight: normal;
	}

	h1 {
		color: #444;
		background-color: transparent;
		border-bottom: 1px solid #D0D0D0;
		font-size: 19px;
		font-weight: normal;
		margin: 0 0 14px 0;
		padding: 14px 15px 10px 15px;
	}

	code {
		font-family: Consolas, Monaco, Courier New, Courier, monospace;
		font-size: 12px;
		background-color: #f9f9f9;
		border: 1px solid #D0D0D0;
		color: #002166;
		display: block;
		margin: 14px 0 14px 0;
		padding: 12px 10px 12px 10px;
	}

	#body {
		margin: 0 15px 0 15px;
	}

	p.footer {
		text-align: right;
		font-size: 11px;
		border-top: 1px solid #D0D0D0;
		line-height: 32px;
		padding: 0 10px 0 10px;
		margin: 20px 0 0 0;
	}

	#container {
		margin: 10px;
		border: 1px solid #D0D0D0;
		box-shadow: 0 0 8px #D0D0D0;
	}
	</style>
    <link rel="stylesheet" href="/static/css/jquery.mobile-1.4.5.min.css">
    <link rel="stylesheet" href="/static/css/uploadify.css">
    <script src="/static/js/jquery-1.11.3.min.js"></script>
    <script src="/static/js/jquery.mobile-1.4.5.min.js"></script>
    <script src="/static/js/jquery.uploadify.min.js"></script>
</head>
<body>

<div data-role="page" id="page">
    <style>
    #preview {
        width: 80%; max-width: 300px;
    }
    #preview img {
        width: 100%;
    }
    .hiddenfile {
        width: 0px;
        height: 0px;
        overflow: hidden;
    }
    </style>
    <div data-role="header">
        <h3>Header</h3>
    </div>
    <div data-role="content">
        <button id="chooseFile">Choose file</button>
        <div class="hiddenfile">
            <input type="file"  data-clear-btn="false" name="image" accept="image/*"><!-- capture -->
        </div>
            <input type="file"  name="image" accept="image/*"><!-- capture -->
        <div id="preview">
        </div>
        <ul id="info" data-role="listview" data-inset="true">
        </ul>
    </div>
</div>

<script>
$('#page').on('pageinit', function(){
    $("#chooseFile").click(function(e){
        e.preventDefault();
        $("input[type=file]").trigger("click");
    });
    $("input[type=file]").change(function(){
        var file = $("input[type=file]")[0].files[0];            
        $("#preview").empty();
        displayAsImage3(file, "preview");
        
        $info = $("#info");
        $info.empty();
        if (file && file.name) {
            $info.append("<li>name:<span>" + file.name + "</span></li>");
        }
        if (file && file.type) {
            $info.append("<li>type:<span>" + file.type + "</span></li>");
        }
        if (file && file.size) {
            $info.append("<li>size:<span>" + file.size + " bytes</span></li>");
        }
        if (file && file.lastModifiedDate) {
            $info.append("<li>lastModifiedDate:<span>" + file.lastModifiedDate + " bytes</span></li>");
        }
        $info.listview("refresh");
    });
});

function displayAsImage3(file, containerid) {
    if (typeof FileReader !== "undefined") {
        var container = document.getElementById(containerid),
            img = document.createElement("img"),
            reader;
        container.appendChild(img);
        reader = new FileReader();
        reader.onload = (function (theImg) {
            return function (evt) {
                theImg.src = evt.target.result;
            };
        }(img));
        reader.readAsDataURL(file);
    }
}

</script>
</body>
</html>
