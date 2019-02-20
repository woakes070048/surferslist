<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title><?php echo $title; ?></title>
<base href="<?php echo $base; ?>" />
<link rel="stylesheet" type="text/css" href="catalog/view/root/ui/jquery-ui.min.css" />
<link rel="stylesheet" type="text/css" href="catalog/view/root/font-awesome/css/font-awesome.min.css" />
<style type="text/css">
body {
	padding: 0;
	margin: 0;
	background: #F7F7F7;
	font-family: Helvetica, Arial, sans-serif;
	font-size: 13px;
}
img {
	border: 0;
}
#container {
	font-family: 'Ubuntu', 'Lato', Helvetica, sans-serif;
	padding: 0px 5px;
	height: 495px;
	min-width: 250px;
	background: #fff;
	overflow: hidden;
    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
    box-sizing: border-box;
}
#menu {
	clear: both;
	height: 32px;
	margin: 5px 0;
}
#menu .button, #menu #search-files-button {
	display: inline-block;
	padding: 8px 6px;
	font-size: 14px;
	color: #fff;
	cursor: pointer;
    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
    box-sizing: border-box;
}
#menu .button {
	font-weight: bold;
	float: left;
	background-color: #bababa;
	margin: 0 5px 5px 0;
}

#menu #upload {
	background-color: #ffcc00;
}
#menu #refresh {
	background-color: #76cdd8;
}
#menu #delete {
    background-color: #dc313e;
}
#menu .button i {
    margin-right: 2px;
	font-weight: 700;
}
#menu .button:hover {
	background-color: #545454;
}
#menu #filemanager-search {
	display: block;
	float: right;
	clear: right;
	position: relative;
	height: 32px;
}
#menu #search-files, #menu #search-files-button {
	float: left;
	margin: 0;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
}
#menu #search-files {
	background: #f5f5f5;
	border: 1px solid #ccc;
	height: 32px;
	font-size: 15px;
	padding: 0 0 0 5px;
	margin: 0 0 5px 0;
}
#menu #search-files:active {
	background: #fff;
}
#menu #search-files-button {
	position: absolute;
	top: 0;
	right: 0;
	background-color: #545454;
	padding: 7px 6px 8px;
	font-size: 15px;
	width: 30px;
	height: 32px;
	text-align: center;
}
#my-images {
	background: #f5f5f5;
	border: 1px solid #ccc;
	width: 100%;
	height: 405px;
	padding: 5px;
	overflow-x: auto;
	overflow-y: scroll;
	text-align: center;
	text-align: left;
    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
    box-sizing: border-box;
}
#my-images::-webkit-scrollbar-track {
	background: #dedede;
}
#my-images::-webkit-scrollbar-thumb {
	background: #545454;
	cursor: pointer;
}
#my-images::-webkit-scrollbar {
	width: 10px;
}
#my-images > div {
	display: inline-block;
	text-align: center;
	cursor: pointer;
	margin: 5px;
	padding: 0;
	width: <?php echo $this->config->get('config_image_product_width'); ?>px;
	height: <?php echo $this->config->get('config_image_product_height') + 84; ?>px;
	background: #fff;
	border: 1px solid #a8aaad;
	color: #333;
	position: relative;
	vertical-align: top;
	word-wrap: break-word;
	position: relative;
}
#my-images > div > img {
	margin: 0;
	padding: 0;
	width: <?php echo $this->config->get('config_image_product_width'); ?>px;
	height: <?php echo $this->config->get('config_image_product_height'); ?>px;
}
#my-images > div > .image-details {
	display: inline-block;
	width: 100%;
	height: 82px;
	position: absolute;
	bottom: 0;
	left: 0;
}
#my-images .button-select {
	width: 100%;
	color: #fff;
	background: #a8aaad;
	padding: 9px 12px;
	font-size: 14px;
	font-weight: bold;
	line-height: 14px;
    display: inline-block;
    text-decoration: none;
    border: none;
    cursor: pointer;
    margin: 10px auto 0;
    vertical-align: top;
	position: absolute;
    bottom: 0;
    left: 0;
    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
    box-sizing: border-box;
}
#my-images div.selected {
	border: 1px solid #0d8f91;
	background: #76cdd8;
	color: #fff;
}
#my-images div.selected .button-select {
	background: #0d8f91;
}
#my-images input {
	display: none;
}
#footer {
	height: 40px;
}
#footer p {
	margin: 5px 0;
}
#dialog {
	display: none;
}
.loading-image {
	position: absolute;
    top: 0;
    left: 0;
    bottom: 0;
    right: 0;
	width: 100%;
	height: 100%;
}
.loading-image .fa {
    font-size: 100px;
    color: #ccc;
	position: relative;
	top: 50%;
	margin-top: -50px;
}
.red-text {
    color: #dc313e;
}
.purple-text {
    color: #CF00FF;
}
@media screen and (max-width:470px) {
    .hidden-small {
        display: none !important;
    }
	#my-images, #menu .button, #menu #filemanager-search, #menu #search-files {
		width: 100%;
		max-width: 295px;
		margin-left: auto;
		margin-right: auto;
		float: none;
		display: block;
	}
	#my-images {
		height: 325px;
	}
	#my-images > div {
		display: block;
		margin: 0 auto 20px;
	}
	#menu {
		height:initial;
	}
}
</style>
</head>
<body>
<div id="container">
  <div id="menu">
	  <a id="upload" class="button"><i class="fa fa-upload"></i><?php echo $button_upload; ?></a>
	  <a id="refresh" class="button"><i class="fa fa-refresh"></i><?php echo $button_refresh; ?></a>
	  <?php if (false && $field) { ?>
	  <a id="select" class="button"><i class="fa fa-hand-o-up"></i><?php echo $button_select; ?></a>
	  <?php } ?>
	  <a id="rename" class="button hidden-small"><i class="fa fa-pencil"></i><?php echo $button_rename; ?></a>
	  <a id="delete" class="button hidden-small"><i class="fa fa-trash"></i><?php echo $button_delete; ?></a>
	  <span id="filemanager-search">
		  <input type="text" id="search-files" placeholder="<?php echo $button_search; ?>" />
		  <a id="search-files-button" title="<?php echo $button_search; ?>"><i class="fa fa-search"></i></a>
	  </span>
  </div>
  <div id="my-images"></div>
  <div id="footer">
	<p class="purple-text"><?php echo $text_image_orphaned; ?></p>
	<p class="red-text"><?php echo $text_image_expired; ?></p>
  </div>
</div>
<script type="text/javascript" src="catalog/view/root/javascript/jquery-3.2.1.min.js"></script>
<script type="text/javascript" src="catalog/view/root/ui/jquery-ui.min.js"></script>
<script type="text/javascript" src="catalog/view/root/javascript/mf_ajaxupload.js"></script>
<script type="text/javascript"><!--
var imageDirectory = '<?php echo $dir_member_image; ?>';
var urlImages = '<?php echo $url_member_image; ?>';
var textEditor = <?php echo $text_editor != false ? $text_editor : 'false'; ?>;
var fieldTarget = '<?php echo $field; ?>';
var textLoadingImage = '<?php echo $text_loading_image; ?>';
var textWarning = '<?php echo $text_warning; ?>';
var textSuccessImageUploaded = '<?php echo $success_image_uploaded; ?>';
var textErrorDirectory = '<?php echo $error_directory; ?>';
var textConfirmDelete = '<?php echo $text_confirm_delete; ?>';
var textErrorSelect = '<?php echo $error_file; ?>';
var entryCopy = '<?php echo $entry_copy; ?>';
var entryFolder = '<?php echo $entry_folder; ?>';
var buttonFolder = '<?php echo $button_folder; ?>';
var buttonCopy = '<?php echo $button_copy; ?>';
var buttonSelect = '<?php echo $button_select; ?>';
var buttonApply = '<?php echo $button_apply; ?>';
var buttonUpload = '<?php echo $button_upload; ?>';
var buttonSubmit = '<?php echo $button_submit; ?>';
//--></script>
<script type="text/javascript" src="catalog/view/root/javascript/filemanager.js"></script>
</body>
</html>
