<?php 
 
App::uses('Component', 'Controller');
App::import('Model', 'User');

class UserDataComponent extends Component {
	
	/*
	 *	From the user id, it returns the user's first, last and user name
	 */
	public function getUserName($user_id = 0) {
    	if($user_id > 0){
    		$UserModel = ClassRegistry::init('User');
    	
			$userResult = $UserModel->find('first', array(
				'conditions' => array(
					'id' => $user_id,
				),
				'fields' => array(
					'User.username', 'User.firstName', 'User.lastName'
				)
			));
			
			$user = $userResult['User'];
			
      	  	return $user;
		}
    }
}    
 
?>