<?php
require_once('../core/core.php');
if(!userIsLoggedIn()){die();}
clearUploadCache();
if(isset($_GET['ajax'])&&($_GET['ajax']=="true" || $_GET['ajax']==true)){
	if(isset($_POST['ajaxUpload'])&&!empty($_POST['ajaxUpload'])){
		$fileData = base64_decode(explode(",", $_POST['ajaxUpload'])[1]);
		$tmpName = uniqid("");
		file_put_contents(TMP_DIR.$tmpName, $fileData);
		$fileData=null;
		
		require_once(MUSIC_HANDLER_DIR."id3.php");
		$id3 = getID3Tags(TMP_DIR.$tmpName);
		$cover = getID3CoverBytes(TMP_DIR.$tmpName);
		
		echo json_encode(array_merge($id3, array("tmpName" => $tmpName, "cover" => "data:".$cover[1].";base64,".base64_encode($cover[0]))));
		exit;
	}else if(isset($_POST['uploadTrackTMPFileName'])&&!empty($_POST['uploadTrackTMPFileName'])&&realpath(TMP_DIR.$_POST['uploadTrackTMPFileName'])!=false){
		$path = realpath(TMP_DIR.$_POST['uploadTrackTMPFileName']);
		if(strpos($path, TMP_DIR)!==false){
			if(isset($_POST['id3TrackTitle'])&&!empty($_POST['id3TrackTitle'])){
				if(isset($_POST['id3TrackArtist'])&&!empty($_POST['id3TrackArtist'])){
					if(isset($_POST['id3TrackAlbum'])&&!empty($_POST['id3TrackAlbum'])){
						if(isset($_POST['id3TrackNumber'])&&!empty($_POST['id3TrackNumber'])){
							require_once(MUSIC_HANDLER_DIR."tunes.php");
							if(isset($_FILES['coverImage'])&&!empty($_FILES['coverImage'])&&!empty($_FILES['coverImage']['tmp_name'])){
								$r = writeID3Tags($path, file_get_contents($_FILES['coverImage']['tmp_name']), $_POST['id3TrackArtist'], $_POST['id3TrackAlbum'], $_POST['id3TrackTitle'], $_POST['id3TrackNumber']);
							}else{
								$r = writeID3Tags($path, getID3CoverBytes($path)[0], $_POST['id3TrackArtist'], $_POST['id3TrackAlbum'], $_POST['id3TrackTitle'], $_POST['id3TrackNumber']);
							}
							if($r==true){
								if(addMP3($path)){
									unlink($path);
									header("Location: /");
									die();
								}else{
									header("Location: /?error=100");
									die();
								}
							}else{
								print_r($r);//unknown error
								die();
							}
						}
					}
				}
			}
			unlink($path);
			header("Location: /?error=1");//fields
		}else{
			header("Location: /?error=2");//dir traversal
		}
	}else{
		header("Location: /?error=3");//something went wrong
	}
}
function clearUploadCache(){
	$time  = time();
	
	if($handle = opendir(TMP_DIR)){
		while(false !== ($file = readdir($handle))){
			$file=TMP_DIR.$file;
			if(is_file($file)){
				if (($time - filemtime($file)) >= (60*30)){
					unlink($file);
				}
			}
		}
		closedir($handle);
	}
}
?>