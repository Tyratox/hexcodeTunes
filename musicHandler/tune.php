<?php
require_once('../core/core.php');

if(userIsLoggedIn()){
	if(isset($_GET)&&isset($_GET['id'])){
		if(!empty($_GET['id'])){
			if(is_int(intval($_GET['id']))){
				global $db;
				$array = $db->getAllInformationFrom('tracks', array('id'), array(intval($_GET['id'])));
				if(!empty($array)&&isset($array[0])){
					if(isset($_GET['type'])&&$_GET['type']=="details"){
						echo json_encode($array[0]);
					}else if(isset($_GET['type'])&&$_GET['type']=="download"){
						$array=$array[0];
						$file = MUSIC_DIR.$_GET['id'].".mp3";
						header ("Content-type: octet/stream");
						header ("Content-disposition: attachment; filename=".$array['title'].".mp3");
						header("Content-Length: ".filesize($file));
						readfile($file);
						exit;
					}else if(isset($_POST['type'])&&$_POST['type']=="delete"&&isset($_GET['ajax'])){
						require_once(MUSIC_HANDLER_DIR."tunes.php");
						echo removeMP3($_GET['id']);
					}
				}
			}
		}
	}
}else{
	header("Location: /login.php");
	exit;
}
?>