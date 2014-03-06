<?php 
 
App::uses('Component', 'Controller');
App::import('Model', 'User');
 
class AccessTokenComponent extends Component {
 
    public function checkAccessTokens($publicToken = "0", $privateToken = "0", $timeStamp = "0") {
    	if($publicToken != "0" && $privateToken != "0" && $timeStamp != "0"){
    		$UserModel = ClassRegistry::init('User');
    	
			$token = $UserModel->find('first', array(
				'conditions' => array(
					'User.public_access_token' => $publicToken
				),
				'fields' => array(
					'private_access_token'
				)
			));
			
			if($token){
				if($token['User']['private_access_token'] == "" || $token['User']['private_access_token'] == null){
			
					$tokenHash = Security::hash($token['User']['private_access_token'].$timeStamp, 'sha256');
			
					if($tokenHash == $privateToken){
						return "they good";
					}
					else{
						return "private";
					}
				}
				else{
					return "error";
				}
			}
			else{
				return "public";
			}
		}
		else{
			return "bad data";
		}
    }
}    
 
?>
