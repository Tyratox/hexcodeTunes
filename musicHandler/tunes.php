<?php

require_once(MUSIC_HANDLER_DIR.'id3.php');

/** Adds a mp3 file to db/folder **/
function addMP3($pathToFile){
	global $db;
	$id3Tags = getID3Tags($pathToFile);
	$cover = getID3CoverBytes($pathToFile);
	
	if($id3Tags==false||$cover==false){return false;}
	$type=$cover[1];
	if($type=="image/png"||$type=="image/jpeg"){
		$mp3_mimes = array('audio/mpeg','audio/mpeg3', 'audio/x-mpeg-3', 'application/octet-stream');
		if(in_array(mime_content_type($pathToFile), $mp3_mimes)){
			if(count($db->getAllInformationFrom('tracks', array('title', 'artist', 'album', 'track_number'), array($id3Tags['title'], $id3Tags['artist'], $id3Tags['album'], $id3Tags['track_number']))<0)){
				if($db->addToDatabase('tracks', array('title', 'artist', 'album', 'track_number'), array($id3Tags['title'], $id3Tags['artist'], $id3Tags['album'], $id3Tags['track_number']))==true){
					$track = $db->getAllInformationFrom('tracks', array('title', 'artist', 'album', 'track_number'), array($id3Tags['title'], $id3Tags['artist'], $id3Tags['album'], $id3Tags['track_number']))[0];
					$fileName=$track['id'].".mp3";
					if(copy($pathToFile, MUSIC_DIR.$fileName)){
						$path=MUSIC_DIR."coverCache/".$track['id'].".png";
						if($type=="image/jpeg"){
							$uuidPath = TMP_DIR.uniqid("cover");/*workaround*/
							file_put_contents($uuidPath, $cover[0]);
							$img = imagecreatefromjpeg($uuidPath);
							imagepng($img, $path);
							imagedestroy($img);
							unlink($uuidPath);
						}else{
							file_put_contents($path, $cover[0]);
						}
						$cover=null;
						require_once(LIB_DIR."colorExtract/colors.inc.php");
						$colors = new GetMostCommonColors();
						$cl = $colors->Get_Color($path, 2, true, true, 80);
						$c1="#".key($cl)."";
						end($cl);
						$c2="#".key($cl);
						$db->updateInDatabase('tracks', array('color1', 'color2'), array($c1, $c2), array('id'), array($track['id']));
						return true;
					}else{
						if(file_exists(MUSIC_DIR.$fileName)){
							unlink(MUSIC_DIR.$fileName);
						}
						$db->removeFromDatabase('tracks', array('id'), array($track['id']));
						return false;
					}
				}
			}	
		}
	}
	return false;
}
function removeMP3($id){
	global $db;
	
	if(file_exists(MUSIC_DIR.$id.".mp3")){
		unlink(MUSIC_DIR.$id.".mp3");
	}
	if(file_exists(MUSIC_DIR."coverCache/".$id.".png")){
		unlink(MUSIC_DIR."coverCache/".$id.".png");
	}
		
	return $db->removeFromDatabase('tracks', array('id'), array($id));
}
?>