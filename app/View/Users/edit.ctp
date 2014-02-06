<?php
/* display message saved in session if any */
echo $this->Session->flash();
?>
<script type="text/javascript" src="../js/upload_functions.js" ></script>
<div class="usersForm">
    <?php echo $this->Form->create('User'); ?>
    <fieldset>
        <legend><?php echo __('Edit User'); ?></legend>
        <?php
        echo $this->Form->input('emailEdit', array('label' => 'email'));
        echo $this->Form->input('firstNameEdit', array('label' => 'First Name'));
        echo $this->Form->input('lastNameEdit', array('label' => 'Last Name'));
        echo $this->Form->input('currentPassword', array('type' => 'password', 'label' => 'Current Password'));
        echo $this->Form->input('passwordEdit', array('type' => 'password', 'label' => 'New Password'));
        echo $this->Form->input('passwordConfirmEdit', array('type' => 'password', 'label' => 'Confirm Password'));
        ?>
    </fieldset>
    <?php echo $this->Form->end(__('Submit')); ?>
</div>

<div class="progress" id="progress">
  <div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 60%;">
    <span class="sr-only">60% Complete</span>
  </div>
</div>

<script>
	/* This script block controls the drag and drop of photos and links
		onto the page. Uses functions from uploadFunctions.js
	*/
	var doc = document.documentElement;
	var fileIndex = 0;
	var draggedOver = false;
	
	// control when drag starts - show drop area
	doc.ondragover = function(){
		if(!draggedOver){
			$("#dropArea").show();
			draggedOver = true;
		}
		this.className = 'hover';
		return false;
	};
	// control when drag starts - hide drop area //** BUG doesn't fire
	doc.ondragend = function(){
		if(draggedOver){
			$("#dropArea").hide();
			draggedOver = false;
		}
		this.className = '';
		return false;
	};
	// control when something is dropped on page
	doc.ondrop = function(event){
	
		event.preventDefault && event.preventDefault();
		/*
		if(draggedOver){
			$("#dropArea").hide();
			draggedOver = false;
		}
		*/
		// if dropped in drop area
		//if(event.target.id == "dropArea" && fb_user != 0){
			//alert(event.dataTransfer.types);
			this.className = '';
			// if actual files are dropped
			if(event.dataTransfer.types.indexOf("Files") > -1){ 
				//alert("here");
				var files = event.dataTransfer.files;
				
				//addUploadForm(files.length); // set up a upload area for each file
				var tempIndex = fileIndex;
				// upload each file and preview each file
				//for(fileIndex; fileIndex < files.length + tempIndex; fileIndex++){ 
					uploadFile(files[0], document.getElementById('progress'), document.getElementById('progress'), '../uploadProfilePic');
					//previewFile(files[0], document.getElementById('pp'+fileIndex+''));
				//}
			}
			// if a url is dropped
			else{
				// get image src of url and set up upload area
				$("#submitAll").show();
				imageURL = $(event.dataTransfer.getData('text/html')).attr('src');
				addURLform(imageURL);
			}
			return true;
		//}
	};
	
</script>