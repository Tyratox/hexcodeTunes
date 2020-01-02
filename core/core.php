<?php
define("ROOT_DIR", explode("core", dirname(__FILE__))[0]);
define("CORE_DIR", ROOT_DIR . 'core/');
define("ADMIN_DIR", ROOT_DIR . 'admin/');
define("AJAX_DIR", ROOT_DIR . 'ajax/');
define("MUSIC_DIR", ROOT_DIR . 'music/');
define("MUSIC_HANDLER_DIR", ROOT_DIR . 'musicHandler/');
define("LIB_DIR", ROOT_DIR . 'libs/');
define("UI_DIR", ROOT_DIR . 'ui/');
define("RES_DIR", ROOT_DIR . 'res/');
define("TMP_DIR", ROOT_DIR . 'tmp/');

if(session_status() != PHP_SESSION_ACTIVE) {
	session_start();
}
/** Sanitize $_POST and $_GET **/
$_POST = sanitizeInput($_POST);
$_GET = sanitizeInput($_GET);
/** Sanitizes User Input **/
function sanitizeInput($input){
	if(is_array($input)){
		foreach($input as $key => $data){
			$input[$key] = sanitizeInput($data);
		}
		return $input;
	}else{
		return strip_tags($input);
	}
}
/** get styles **/
function getStyles(){
	return "<style>".file_get_contents(UI_DIR."styles/style.css")."</style>";	
}
/** Sanitizes Database Output **/
function sanitizeOutput($output){
	if(is_array($output)){
		foreach($output as $key => $data){
			$output[$key] = sanitizeOutput($data);
		}
		return $output;
	}else{
		if(preg_match("/(document.cookie|<script|onclick|javascript:)/i", $output)){/*Prevent XSS*/
			return "";
		}
		return htmlentities($output);
	}
}
/** Gets current page url **/
function curPageURL(){
	$pageURL = 'https://'.$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	return $pageURL;
}
require_once(CORE_DIR.'db.php');
require_once(CORE_DIR.'auth.php');
require_once(CORE_DIR.'lang.php');
?>