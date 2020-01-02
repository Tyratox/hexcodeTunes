<?php
require_once(LIB_DIR.'getid3/getid3.php');
require_once(LIB_DIR.'/getid3/write.php');

function getID3Tags($pathToFile){
	if(!file_exists(realpath($pathToFile))){return false;}
	#load getid3 lib
	$getID3 = new getID3;
	$fileData = $getID3->analyze($pathToFile);
	if(!empty($fileData)&&isset($fileData['tags'])&&is_array($fileData['tags'])){
		getid3_lib::CopyTagsToComments($fileData);
		/*$data = array(
		 "filesize" => $fileData['filesize'],
				"fileformat" => $fileData['fileformat'],
				"channels" => $fileData['audio']['channels'],
				"sample_rate" => $fileData['audio']['sample_rate'],
				"bitrate" => $fileData['audio']['bitrate'],
				"channelmode" => $fileData['audio']['channelmode'],
		);*/
		$tags = array();
		foreach($fileData['tags'] as $tagSet){
			$tags['artist'] = $tagSet['artist'][0];
			$tags['album'] = $tagSet['album'][0];
			$tags['title'] = $tagSet['title'][0];
			$tags['track_number'] = $tagSet['track_number'][0];
		}
		return $tags;
	}
	return false;
}
function getID3CoverBytes($pathToFile){
	if(!file_exists(realpath($pathToFile))){return false;}
	#load getid3 lib
	$getID3 = new getID3;
	$fileData = $getID3->analyze($pathToFile);
	getid3_lib::CopyTagsToComments($fileData);
	
	if(isset($getID3->info['id3v2']['APIC'][0]['data'])){
		$cover = $getID3->info['id3v2']['APIC'][0]['data'];
	}elseif(isset($getID3->info['id3v2']['PIC'][0]['data'])){
		$cover = $getID3->info['id3v2']['PIC'][0]['data'];
	}else {
		$cover = null;
	}
	if($cover!=null){
		if(isset($getID3->info['id3v2']['APIC'][0]['image_mime'])) {
			$mimetype = $getID3->info['id3v2']['APIC'][0]['image_mime'];
			return array($cover, $mimetype);
		}
	}
	return false;
}
function writeID3Tags($filePath, $pngCoverBytes, $artist, $album, $title, $track_number){
$getID3 = new getID3;
		$getID3->setOption(array('encoding'=>'UTF-8'));
		$tagwriter = new getid3_writetags;
		$tagwriter->filename = $filePath;
		$tagwriter->tagformats = array('id3v2.3');
		$tagwriter->overwrite_tags = true;
		$tagwriter->tag_encoding = 'UTF-8';
		$tagwriter->remove_other_tags = true;
		
		$TagData = array(
				'title' => array($title),
				'artist'=> array($artist),
				'album' => array($album),
				'tracknumber' => array(intval($track_number))
		);
		$TagData['attached_picture'][]=array(
			'picturetypeid'=>2,
			'description'=>'cover',
			'mime'=>'image/png',
			'data'=> $pngCoverBytes
		);
		
		$tagwriter->tag_data = $TagData;
		if($tagwriter->WriteTags()){
			if(!empty($tagwriter->warnings)){
				return $tagwriter->warnings;
			}else{
				return true;
			}
		}else{
			return $tagwriter->errors;
		}
}
?>