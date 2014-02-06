<?php
App::uses('Component', 'Controller');

class UploadPicComponent extends Component {

	/*
		get extension for $str
	*/
	private function getExtension($str) {
		if(strrpos($str, ".jpg") || strrpos($str, "image/jpg")){
			$ext = "jpg";
		}
		else if(strrpos($str, ".jpeg") || strrpos($str, "image/jpeg")){
			$ext = "jpeg";
		}
		else if(strrpos($str, ".png") || strrpos($str, "image/png")){
			$ext = "png";
		}
		else if(strrpos($str, ".gif") || strrpos($str, "image/gif")){
			$ext = "gif";
		}
		else{
			$ext = "";
		}
		return $ext;
	}
	
	private function saveSmall($width = 0, $height = 0, $file_path = '0') {
		if($width != 0 && $height != 0 && $file_path != '0'){
		
			if($extension=="jpg" || $extension=="jpeg" ){
				$src = imagecreatefromjpeg($file_path);
			}
			else if($extension=="png"){
				$src = imagecreatefrompng($file_path);
			}
			else{
				$src = imagecreatefromgif($file_path);
			}

			list($Iwidth,$Iheight)=getimagesize($file_path);

			$newwidth = $width;
			$newheight = ($Iheight/$Iwidth)*$newwidth;
			if($newheight > $height){
				$newheight = $height;
				$newwidth = ($Iwidth/$Iheight) * $newheight;
			}

			$tmp=imagecreatetruecolor($newwidth,$newheight);

			imagecopyresampled($tmp,$src,0,0,0,0,$newwidth,$newheight,
			 $Iwidth,$Iheight);
	
			$filename = "../../img/users/$user_id/" . $width . "x" . $height . "/profile.jpg";

			if(!file_exists("../../img/users/$user_id/" . $width . "x" . $height . "")){ 
				mkdir("../../img/users/$user_id/" . $width . "x" . $height . "");
			}
	
			if(file_exists($filename)) {
				unlink($filename); //remove the file
			}
			imagejpeg($tmp,$filename,100);

			imagedestroy($src);
			imagedestroy($tmp);
		}
	}
 
 	public function uploadProfilePic($user_id = 0, $upload_loc = -1, $pic = '0', $filess) {
 		
 	//	if($user_id != 0 && $upload_loc != -1 && $pic != '0'){
 
			if(!file_exists("img/users/$user_id")){ 
				mkdir("img/users/$user_id");
			}
/*
			if($upload_loc == 0){ // Upload a file from a file
				// Check allowed extensions
				$allowedExts = array("gif", "jpeg", "jpg", "png");
				$extension = getExtension($filess["name"]);
				if ((($filess["type"] == "image/gif")
				  || ($filess["type"] == "image/jpeg")
				  || ($filess["type"] == "image/jpg")
				  || ($filess["type"] == "image/png"))
				  && ($filess["size"] < 3300000)
				  && in_array($extension, $allowedExts)){
					$invalid = false;
					if($filess["error"] > 0){
						echo "Return Code: " . $filess["error"] . "<br>";
					}
					else{

						$fileName = "profile"; // get rid of whitespace
						$filePath = "../../img/users/$user_id/profile.jpg";
						$uniquePath = "$user/" . $fileName;
	
						// if($extension == "jpg" || $extension == "jpeg"){
							// echo photo_getGPS($filePath);
						// }
	
						if(file_exists($filePath)) {
							unlink($filePath); //remove the file
						}
						// put uploaded file into server
						move_uploaded_file($_FILES['file']['tmp_name'], $filePath);
						
					}
				}
				else{
					$invalid = true;
				}
			}
			*/
			/*
			else if($upload_loc == 1){ // Upload a file from a URL
				$extension = getExtension($pic);
				if(($extension == "") && strrpos($pic, "encrypted")){
					$extension = "jpg";
				}else if($extension == ""){
					$extension = "jpg";
				}
				if($extension == "" || $pic = '0'){
					$invalid = true;
				}
				else{
					$invalid = false;
					// rename the file to a unique name
					$numFiles = count(glob("users/$user/*.*"));
					$fileName = $user . "_" . $numFiles . "." . $extension;
					$filePath = "../../img/users/$user_id/profile.jpg";


					if(copy($pic, $filePath)){
						//echo "Stored in: " . $filePath;
					}	
					else{
						$invalid = true;
					}
				}
				
			}
			*/

			if(!$invalid){
				//saveSmall(175, 175, $filePath);
			}
			else{
				echo 0;
			}
	//	}
		
	}
	
	public function uploadProfilePic1($user_id = 0, $upload_loc = -1, $pic = '0', $filess) {
		echo 99;
	}
}
?>