<?php

if(!userIsLoggedIn()){header("Location: /login.php");exit;}

global $db;

$ajax = isset($_GET['ajax']) ? true : false;
if(!$ajax){
	require_once(UI_DIR.'header.php');
}
/*Generate cover "list"*/

$tracks=$db->getAllInformationFromTable('tracks');
if(!empty($tracks)){
	$albums = array();
	foreach($tracks as $track){
		$albums[$track['album']][] = $track;
	}
	krsort($albums);
	$albums=array_reverse($albums);
	foreach($albums as $key => $album){
		//sort by track id
		usort($album, 'compareByTrackNumber');
		$id = preg_replace('/\s+/', '', $key);
		echo '<a href="#'.$id.'"><div id="'.$id.'" class="album">';
		echo '<img src="/music/coverCache/'.$album[0]['id'].'.png" class="cover" /><div class="border"></div>';
	
		echo '<div class="albumWindow" style="background-color:'.$album[0]['color1'].';color:'.$album[0]['color2'].'">';
		echo '<img src="/music/coverCache/'.$album[0]['id'].'.png" class="coverBig" />';
		echo '<p class="albumTechDetails">'.count($album)." "._("Track(s)").'</p>';
		echo '<div class="albumDetails">';
		echo '<p class="albumName">'.$key.'</p><p class="albumArtist">'.$album[0]['artist'].'</p>';
		echo '<ul class="trackList">';
		foreach($album as $track){
			if(intval($track['track_number'])<10){
				$track['track_number'] = "0".$track['track_number'];
			}
			$track['track_number'].=".";
			echo '<li class="track" onclick="playMP3('.$track['id'].')">
				<span class="trackNumber">'.$track['track_number'].'</span>
				<span class="trackTitle">'.$track['title'].'</span>
			</li>';
		}
		echo '</ul></div></div><div class="albumDesc">'./*'<span class="albumName">'.$key.'</span> - <span class="albumArtist">'.$album[0]['artist'].'</span>*/'</div></div></a>';
	}	
}

if(!$ajax){
	require_once(UI_DIR.'footer.php');
}

/** Sorts an array by title **/
function compareByTitle($a, $b) {
	return strcmp($a["title"], $b["title"]);
}
/** Sorts an array by track number **/
function compareByTrackNumber($a, $b) {
	return $a['track_number'] > $b['track_number'];
}
?>