/*
	CREATED BY: Jonathan Harrison, Trent Jones, and Daryl Schmitt
	LAST UPDATED: 7/8/2013
	PROJECT 3 
	CSCE 315 SUMMER 2013
*/

//////////DRAG and DROP///////////////
/*
	This function uploads a file to the server, updates a
	progress bar element, and connects the file to the uploadForm 
*/
function uploadFile(file, progress, uploadForm, URLhandler){
	alert(URLhandler);
	var formData = new FormData();
	formData.append('file', file);
	formData.append('URL', 0);
	formData.append('formID', -1);
	
	var xhr = new XMLHttpRequest();
	xhr.open('POST', "http://whereone.com/GroupWalletCake/Users/uploadProfilePic");
	// When file is loaded
	xhr.onload = function(){
		progress.value = progress.innerHTML = 100;
		
		if(xhr.status === 200){ // if request was successful
			$(uploadForm).show();
			$("#submitAll").show();
			var responseID = xhr.responseText; // responseText = photoID of uploaded file
			//if(responseID!=0 && !isNaN(responseID)){
			//	attachUploadForm(uploadForm, responseID);
			//}
			//else{
				alert("something: " + responseID);
			//}
			
		}
		else{
			alert("ERROR: " + xhr.status);
		}
	};
	// monitor upload progress in upload bar
	xhr.upload.onprogress = function(event){
		if(event.lengthComputable){
			var percent = (event.loaded / event.total * 100 | 0);
			progress.value = progress.innerHTML = percent;
		}
	}
	
	xhr.send(formData);
	return false;
}

/*
	This function puts a preview of the file into the container Element
*/
function previewFile(file, container){
	var reader = new FileReader();
	reader.onload = function(event){
		var image = new Image();
		image.src = event.target.result;
		image.width = 185;
		image.height = 185;
		
		container.appendChild(image);
	}
	
	reader.readAsDataURL(file);
}

//////////////UPLOAD to SERVER/////////////////
/*
	This function sets up the upload divs for the number of files
*/
function addUploadForm(num){
	var i = uploadIndex;
	for(i; i < num + uploadIndex; i++){
		if(i%4 == 0){
			$("#uploadPrev").append('<div class="uploadRow" id="ur'+Math.floor(i/4)+'" style="top:'+Math.floor(i/4)*300+';"></div>');
		}
		var d = "#ur"+Math.floor(i/4);
		$(d).append('<div class="uploadForm">\
						<div class="photoArea" id="pp'+i+'">\
						</div>\
						<progress class="progressBar" id="pBar'+i+'" min="0" max="100" value="0">0</progress>\
						<form class="uploadDataForm" id="uForm'+i+'" action="addPicInfo.php" method="post" hidden="true">\
						</form>\
					</div>');
	}	
	uploadIndex = i;
}

/*
	This function add the upload div for a image url
*/
function addURLform(inputURL){
	if(uploadIndex%4 == 0){
		$("#uploadPrev").append('<div class="uploadRow" id="ur'+Math.floor(uploadIndex/4)+'" style="top:'+Math.floor(uploadIndex/4)*300+';"></div>');
	}
	var d = "#ur"+Math.floor(uploadIndex/4);
	var pID = "pp" + uploadIndex;
	$(d).append('<div class="uploadForm">\
					<div class="photoArea" id="'+pID+'">\
					<img src="'+inputURL+'">\
					</div>\
					<form class="uploadDataForm" id="uForm'+uploadIndex+'" action="addPicInfo.php" method="post" hidden="true">\
					</form>\
				</div>');
	$.post("upload_pic.php", { picURL: inputURL, formID: uploadIndex, URL: 1 }, function(data){
		if(data.indexOf("Warning") == -1){
			var picInfo_ = JSON.parse(data);
			var uploadForm = document.getElementById('uForm'+picInfo_[1]+'');
			$(uploadForm).show();
			attachUploadForm(uploadForm, picInfo_[0]);
		}
		else{
			alert(data);
		}
	});
	uploadIndex++;
}

/*
	This function puts the photo data form into the upload div
	and also attached the photoID to the form
*/
function attachUploadForm(formElement, picID_){
	formElement.innerHTML = '<input id="captionArea" type="text" placeholder="caption"  name="caption"><br>\
						<input type="hidden" name="picID" value="'+picID_+'">\
						<textarea id="tagArea" name="tags" cols="21" rows="3"></textarea>';
}