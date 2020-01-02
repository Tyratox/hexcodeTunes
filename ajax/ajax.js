function requestPage(url, callBack){
	startLoading();
	if(url.indexOf("?")==-1){
		$.get( url+"?ajax=true", function( data ) {
		    callBack(data);
		    stopLoading();
		  });
	}else{
		$.get( url+"&ajax=true", function( data ) {
		    callBack(data);
		    stopLoading();
		  });
	}
}
function loadPage(url, callback){
	requestPage(url, function(data){$("#page").html(data);
		callback();
		//Callback
	});
}
function refreshPage(){
	loadPage("/", function(){
		$(".trackTitle").bind("contextmenu", function(e){
			contextMenu(e);
		});
	});
	
}
$(window).on('hashchange', function(){
	checkHash();
});
$(document).ready(function(){
	checkHash();
});
function checkHash(){
	var hash = (window.location.hash);
	if(typeof hash !== 'undefined' && hash != null && hash!=""){
		hash = hash.substring(1);
		if(hash==""||hash==" "){
			loadPage("/", function(){});
		}
		if(hash.indexOf("page:")==0){
			loadPage(hash.split(":")[1], function(){});
		}
	}
}
function setLoadingBar(widthInPercent){
	$("#theLoadingBar").css("width", widthInPercent+"%");
}
function startLoading(){
	$("html").css("cursor", "progress");
}

function stopLoading(){
	$("html").css("cursor", "auto");
}
$(document).on('submit', 'form', function(e) {
	var form = $(e['currentTarget']);
	if(form.attr("ajax")!="true"){
		return;
	}
	e.preventDefault();
    var form = $(e['currentTarget']);
    if(form.attr("warning")=="true"){
    	alertify.confirm(form.attr("message"), function (e) {
    	    if (e) {
    	    	_pData(form);
    	    } else {
    	        
    	    }
    	});
    }else{
    	_pData(form);
    }
});
function _pData(form){
	postData(form.attr("action"), form, function(form){
    	if(window.location.hash=="#page:"+form.attr("callBackUrl")){
    		window.location.hash = "grades";
    	}
    	window.location.hash = "page:"+form.attr("callBackUrl");
    });
}
function postData(urlToPost, form, callBack){
	startLoading();
	form.find("input").attr("disabled", true);
    form.find(".checkboxContainer").addClass("disabled");
    var postData = {};
    form.find("input").each(function(index){
    	if($(this).attr("type")!="submit"){
    		if($(this).attr("type")=="checkbox"){
    			postData[$(this).attr("name")] = $(this).is(':checked');
			}else{
				postData[$(this).attr("name")] = $(this).val();
			}
    	} 
    });
    form.find("textarea").each(function(index){
    	postData[$(this).attr("name")] = $(this).val();
    });
    form.find("select").each(function(index){
    	if($(this).val()==null){return false;}
    	postData[$(this).attr("name")] = $(this).val();
    });
    $.post( urlToPost, postData,function( data ) {
    	if(data == "true" || data == true){
    		alertify.success(successText);
    		callBack(form);
    	}else{
    		alertify.error(data);
    	}
    	form.find("input").attr("disabled", false);
        form.find(".checkboxContainer").removeClass("disabled");
        stopLoading();
    });
}
function hasAttr(element, attr){
	var attr = element.attr(attr);
	if (typeof attr !== typeof undefined && attr !== false) {
		return true;
	}
	return false;
}
var curTimeInterval;
function enCurTimeInterval(){
	curTimeInterval = setInterval(function(){
		var curTime = document.getElementById("audioPlayer").currentTime;
		var durTime = document.getElementById("audioPlayer").duration;
		$("#progress").css("width", (100/(durTime/curTime)) + "%");
	}, 1000);
}
function disCurTimeInterval(){
	clearInterval(curTimeInterval);
}
function playMP3(databaseID){
	showAudioPlayer();
	$("#audioPlayer source").attr("src", "/music/"+databaseID+".mp3");
	$("#cover").css("background-image", "url('/music/coverCache/"+databaseID+".png')");
	requestPage("/musicHandler/tune.php?type=details&id="+databaseID, function(data){
		var json = jQuery.parseJSON(data);
		$("#trackTitle").text(json['title']);
		$("#trackArtist").text(json['artist']);
		$("#trackAlbum").text(json['album']);
		
		document.getElementById("audioPlayer").load();
		$("#play").css("display", "none");
		$("#pause").css("display", "block");
		enCurTimeInterval();
		document.getElementById("audioPlayer").play();
	});
}
function pauseMP3(){
	disCurTimeInterval();
	$("#play").css("display", "block");
	$("#pause").css("display", "none");
	document.getElementById("audioPlayer").pause();
}
function resumeMP3(){
	$("#play").css("display", "none");
	$("#pause").css("display", "block");
	document.getElementById("audioPlayer").play();
	enCurTimeInterval();
}
function showAudioPlayer(){
	$("#audioControl").removeClass("moveOut");
}
function hideAudioPlayer(){
	pauseMP3();
	$("#audioControl").addClass("moveOut");
}
function hideWindow(){
	window.location.hash = "#";
}
//file upload
if(window.File && window.FileReader && window.FileList && window.Blob){
	//everything will work
}else{
	alert("The File APIs are not fully supported in this browser. You can't upload to this website!");
}

function handleFileSelect(evt) {
	evt.stopPropagation();
	evt.preventDefault();

	startLoading();
	var files = evt.dataTransfer.files;
	var output = [];
	
	var reader = new FileReader();
	reader.onload = function(e) {
		var postData = {};
		postData["ajaxUpload"] = reader.result;
		$.ajax({
			xhr: function(){
				var xhr = new window.XMLHttpRequest();
			    //Upload progress
			    xhr.upload.addEventListener("progress", function(e){
			    	if(e.lengthComputable){
			    		var percentLoaded = Math.round((e.loaded / e.total) * 100);
			    		setLoadingBar(percentLoaded);
			    	}
			    }, false);
			    //Download progress
			    xhr.addEventListener("progress", function(e){
			    	if(e.lengthComputable){
			    		var percentLoaded = Math.round((e.loaded / e.total) * 100);
			    		setLoadingBar(percentLoaded);
			    	}
			    }, false);
			    return xhr;
			  },
			  type: 'POST',
			  url: "/ajax/upload.php?ajax=true",
			  data: postData,
			  success: function(data){
				  var json = jQuery.parseJSON(data);
				  $("#id3TrackTitle").val(json['title']);
				  $("#id3TrackArtist").val(json['artist']);
				  $("#id3TrackAlbum").val(json['album']);
				  $("#id3TrackNumber").val(json['track_number']);
				  $("#uploadTrackTMPFileName").val(json['tmpName']);
				  $("#uploadCoverPreview").attr("src", json['cover']);
				  $("#uploadWindow").css("display", "block");
				  
				  setLoadingBar(0);
			  }
		});
	}
	reader.onprogress = function(e){
		if(e.lengthComputable){
			var percentLoaded = Math.round((e.loaded / e.total) * 100);
			// Increase the progress bar length.
			setLoadingBar(percentLoaded);
		}
	};
	reader.onloadend = function(event) {
		setLoadingBar(100);
	};
	
	for(var i = 0; i<files.length; i++){
		var f = files[i];
		if(f.type.match("audio.*")){
			reader.readAsDataURL(f);
			break;
		}else{
			console.log("Invalid File Type: " + f.type);
		}
    }
	stopLoading();
}
function handleDragOver(evt) {
	evt.stopPropagation();
	evt.preventDefault();
	
	evt.dataTransfer.dropEffect = 'copy'; 
}
document.addEventListener('dragover', handleDragOver, false);
document.addEventListener('drop', handleFileSelect, false);

function showError(text){
	$("#errorMessage").text(text);
	$("#errorWindow").css("display", "block");
}
function closeErrorWindow(){
	$("#errorWindow").css("display", "none");
}

var contextElement = null;
var contextElementID = 0;

//context menu
$(".trackTitle").bind("contextmenu", function(e){
	contextMenu(e);
});

function contextMenu(e){
	e.preventDefault();
	contextElement=e.currentTarget;
	contextElementID = parseInt($(contextElement).parent().attr("onclick").split("(")[1].split(")")[0]);
	
	var x = e.pageX;
	var y = e.pageY;
//	$("#contextMenu h1").text($(contextElement).text());
	$("#contextMenu").css("left",(x+10)+"px").css("top",(y-10)+"px").css("display", "block");
}

$(document).bind("click", function(event) {
    $("#contextMenu").css("display", "none");
    closeErrorWindow();
    hideWindow();
});
function contextMenuPlay(){
	playMP3(contextElementID);
}
function contextMenuDownload(){
	window.open("/musicHandler/tune.php?type=download&id="+contextElementID,'_blank');
}
function contextMenuDelete(){
	var postData = {};
	postData["type"] = "delete";
	$.post("/musicHandler/tune.php?id="+contextElementID+"&ajax=true", postData,function(data) {
    	if(data == "true" || data == true){
    		refreshPage();
    	}else{
    		console.log("ERROR: " + data);
    	}
        stopLoading();
    });
}
//key hooks
$(document).keyup(function(e) {
	if(e.keyCode == 27){//esc
		$("#contextMenu").css("display", "none");
		closeErrorWindow();
		hideWindow();
	}
});