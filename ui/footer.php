<?php if(!userIsLoggedIn()){header("Location: /login.php");exit;} ?>
			<div id="uploadWindow">
				<h1><?php echo sanitizeOutput(_("Upload")); ?></h1>
				<br/>
				<form enctype="multipart/form-data" action="/ajax/upload.php?ajax=true" method="POST">
					<div class="uploadField">
						<label for="id3TrackTitle"><?php echo sanitizeOutput(_("Track Title")); ?></label>
						<br/>
						<input type="text" id="id3TrackTitle" name="id3TrackTitle" />
					</div>
					
					<div class="uploadField">
						<label for="id3TrackArtist"><?php echo sanitizeOutput(_("Artist Name")); ?></label>
						<br/>
						<input type="text" id="id3TrackArtist" name="id3TrackArtist" />
					</div>
					
					<div class="uploadField">
						<label for="id3TrackAlbum"><?php echo sanitizeOutput(_("Album Name")); ?></label>
						<br/>
						<input type="text" id="id3TrackAlbum" name="id3TrackAlbum" />
					</div>
					
					<div class="uploadField">
						<label for="id3TrackNumber"><?php echo sanitizeOutput(_("Album Track Number")); ?></label>
						<br/>
						<input type="text" id="id3TrackNumber" name="id3TrackNumber" />
					</div>
					
					<div class="uploadField">
						<label for="coverImage"><?php echo sanitizeOutput(_("Upload a new cover. [PNG or JPG!]")); ?></label>
						<input id="coverImage" name="coverImage" type="file" />
					</div>
					
					<input type="hidden" name="uploadTrackTMPFileName" id="uploadTrackTMPFileName" />
					<input type="submit" value="<?php echo sanitizeOutput(_("Upload & Save"));?>" />
				</form>
				<img id="uploadCoverPreview" src="">
			</div>
			<div id="errorWindow">
				<h1>ERROR</h1>
				<span id="errorMessage"></span>
				<div class="close" onclick="closeErrorWindow()">X</div>
			</div>
		</div>
		<script type="text/javascript">
				<?php include(AJAX_DIR.'ajax.js');?>
		</script>
		<div id="contextMenu">
			<h1></h1>
			<ul>
				<li onclick="contextMenuPlay()">Play</li>
				<li onclick="contextMenuDownload()">Download</li>
				<li onclick="contextMenuDelete()">Delete</li>
			</ul>
		</div>
		<div id="audioControl" class="moveOut">
				<div id="cover" style="background-image:url('');"></div>
				
				<div id="rightContainer">
					<div id="audioStats">
						<div id="trackTitle"></div>
						<div id="trackDetails"><span id="trackArtist"></span> - <span id="trackAlbum"></span></div>
					</div>
					<div id="btns">
						<div id="rewind" class="btn">
							<?php include(RES_DIR."controls/rewind.svg");?>
						</div>
						<div id="pause" style="display:none;" class="btn" onclick="pauseMP3()">
							<?php include(RES_DIR."controls/pause.svg");?>
						</div>
						<div id="play" class="btn" onclick="resumeMP3()">
							<?php include(RES_DIR."controls/play.svg");?>
						</div>
						<div id="forward" class="btn">
							<?php include(RES_DIR."controls/forward.svg");?>
						</div>
					</div>
				</div>
				<div id="out" onclick="hideAudioPlayer()"><?php include(RES_DIR."controls/out.svg");?></div>
				<div id="progress"></div>
				<audio id="audioPlayer">
				  <source src="" type="audio/mpeg">
				</audio>
			</div>
			<?php if(isset($_GET['error'])){
				$e = $_GET['error'];
				$msg="Unknown Error";
				if($e==1){
					$msg=sanitizeOutput(_("Please re-upload the file and fill in all fields!"));
				}else if($e==2){
					$msg=sanitizeOutput(_("Please don not try to hack this webapp!"));
				}else if($e==3){
					$msg=sanitizeOutput(_("Please try again!"));
				}
				echo "<script>showError('".$msg."');</script>";
			}?>
			<div id="theLoadingBar"></div>
	</body>
</html>