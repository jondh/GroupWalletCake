<?php
	class UsersController extends AppController { 
	
	
		public function beforeFilter(){
			parent::beforeFilter();
       		$this->Auth->allow('login','add','loginMobile','loginMobileStatus', 'logoutMobile', 'getUser', 'addMobile', 'facebookMobile', 'getPaginateUsers', 'getUsersMobile');
		}
		
		public $components = array('UploadPic', 'AccessToken');
	
		public function index(){
		
		}
	
		public function login(){
			$this->layout = 'login';
			if ($this->request->is('post')) {
        		/* login and redirect to url set in app controller */
           		if ($this->Auth->login()) {
        			return $this->redirect($this->Auth->redirect());
          	  	}
          	  	
          	  	$ifNoSalt = $this->User->find('first', array(
          	  		'conditions' => array(
          	  			'User.username' => $this->request->data['User']['username']
          	  		)
          	  	));
          	  	
          	  	if($ifNoSalt){
          	  		
          	  		if($ifNoSalt['User']['salt'] == null){
          	  			$this->redirect("http://whereone.com/GroupWalletAlpha/users/loginOldPass");
          	  		}
          	  		$this->Session->setFlash(__($ifNoSalt['User']['salt']));
          	  	}
          	  	else{
          	  		$this->Session->setFlash(__($this->request->data['User']['username']));
          	  	}
       		}
		}
		
		public function loginMobile(){
			$this->layout = 'ajax';
			if ($this->request->is('post')) {
				$this->request->data['User']['username'] = $this->request->data['username'];
				$this->request->data['User']['password'] = $this->request->data['password'];
				if ($this->Auth->login()) {
					$user['User'] = $this->Auth->user();
					$user['Token']['Private'] = Security::generateAuthKey();
					$user['Token']['Public'] = Security::generateAuthKey();
					$this->User->id = $this->Auth->user('id');
					$this->User->set($this->request->data);
					if ($this->User->saveField('public_access_token', $user['Token']['Public'])) {
						if ($this->User->saveField('private_access_token', $user['Token']['Private'])) {
							$user['result'] = 'success';
							return new CakeResponse(array('body' => json_encode($user)));
						}
					}
          	  	}
			/*
        		
        		// Log in user with current password hashing
        		$user = $this->User->find('first', array(
        			'conditions' => array(
        				'User.username' => $this->request->data['username']
        			)
        		));
        		
        		if($user){		
        			if($user['User']['id'] > 0){
						if($user['User']['password'] == Security::hash(Security::hash(Security::hash($this->request->data['password'].$user['User']['salt'])))){
							if($this->Auth->login($user['User'])){
								unset($user['User']['password']);
								unset($user['User']['salt']);
								$this->Auth->user = $user['User'];
								return new CakeResponse(array('body' => json_encode($user)));
							}
						}
					}
				}
			*/
			/*
			* The passwords stored here are hashed another way 
				$this->request->data['password'] = AuthComponent::password($this->request->data['password']);
				return new CakeResponse(array('body' => $this->request->data['password']));
				
				$user = $this->User->find('first', array(
					'conditions' => array(
						'User.username' => $this->request->data['username'],
						'User.password' => $this->request->data['password']
					)
				));
				
				if($user){		
					if ($user['User']['id'] > 0 && $this->Auth->login($user['User'])) {
						unset($user['User']['password']);
						unset($user['User']['salt']);
						$this->Auth->user = $user['User'];
						return new CakeResponse(array('body' => json_encode($user)));
					}
				}
				
				$user = $this->User->find('first', array(
					'conditions' => array(
						'User.email' => $this->request->data['username'],
						'User.password' => $this->request->data['password']
					)
				));
				if($user){
					if ($user['User']['id'] > 0 && $this->Auth->login($user['User'])) {
						unset($user['User']['password']);
						unset($user['User']['salt']);
						$this->Auth->user = $user['User'];
						return new CakeResponse(array('body' => json_encode($user)));
					}
				}
				*/
				$result['result'] = 'not logged in';
				return new CakeResponse(array('body' => json_encode($result)));
				
       		}
       		$result['result'] = 'not post';
       		return new CakeResponse(array('body' => json_encode($result)));
       		
		}
		
		public function logoutMobile(){
			$this->layout = 'ajax';
			if($this->request->is('post')){
				$tokenSuccess = $this->AccessToken->checkAccessTokens($this->request->data['public_token'], $this->request->data['private_token'], $this->request->data['timeStamp']);
				
				if($tokenSuccess == "they good"){
					$this->User->id = $this->request->data['user_id'];
					
					if(!$this->User->exists()){
						return new CakeResponse(array('body' => 'failure'));
					}
					
					$this->User->set($this->request->data);
					if ($this->User->saveField('public_access_token', "")) {
						if ($this->User->saveField('private_access_token', "")) {
							return new CakeResponse(array('body' => 'success'));
						}
					}
				}
				else if($tokenSuccess == "public"){
					$this->User->id = $this->request->data['user_id'];
					
					if(!$this->User->exists()){
						return new CakeResponse(array('body' => 'failure'));
					}
					
					$this->User->set($this->request->data);
					if ($this->User->saveField('public_access_token', "")) {
						if ($this->User->saveField('private_access_token', "")) {
							return new CakeResponse(array('body' => 'bad token'));
						}
					}
				}
				else if($tokenSuccess == "private"){
					$this->User->id = $this->request->data['user_id'];
					
					if(!$this->User->exists()){
						return new CakeResponse(array('body' => 'failure'));
					}
					
					$this->User->set($this->request->data);
					if ($this->User->saveField('public_access_token', "")) {
						if ($this->User->saveField('private_access_token', "")) {
							return new CakeResponse(array('body' => 'bad token'));
						}
					}
				}
				else if($tokenSuccess == "bad data"){
					$this->User->id = $this->request->data['user_id'];
					
					if(!$this->User->exists()){
						return new CakeResponse(array('body' => 'failure'));
					}
					
					$this->User->set($this->request->data);
					if ($this->User->saveField('public_access_token', "")) {
						if ($this->User->saveField('private_access_token', "")) {
							return new CakeResponse(array('body' => 'bad token'));
						}
					}
				}
				else{
					$this->User->id = $this->request->data['user_id'];
					
					if(!$this->User->exists()){
						return new CakeResponse(array('body' => 'failure'));
					}
					
					$this->User->set($this->request->data);
					if ($this->User->saveField('public_access_token', "")) {
						if ($this->User->saveField('private_access_token', "")) {
							return new CakeResponse(array('body' => 'bad token'));
						}
					}
				}
			}
			else return new CakeResponse(array('body' => 'failure'));
		}
		
		public function loginMobileStatus(){
			$this->layout = 'ajax';
			if($this->request->is('post')){
				$tokenSuccess = $this->AccessToken->checkAccessTokens($this->request->data['public_token'], $this->request->data['private_token'], $this->request->data['timeStamp']);
				
				if($tokenSuccess == "they good"){
					$userMatch = $this->User->find('first', array(
						'conditions' => array(
							'User.public_access_token' => $this->request->data['public_token'],
							'User.id' => $this->request->data['user_id']
						)
					));
					
					if($userMatch){
						return new CakeResponse(array('body' => 'success'));
					}
					else{
						return new CakeResponse(array('body' => 'failure'));
					}
				}
				else if($tokenSuccess == "public"){
					return new CakeResponse(array('body' => 'bad token'));
				}
				else if($tokenSuccess == "private"){
					return new CakeResponse(array('body' => 'bad token'));
				}
				else if($tokenSuccess == "bad data"){
					return new CakeResponse(array('body' => 'bad token'));
				}
				else{
					return new CakeResponse(array('body' => 'bad token'));
				}
			}
		}
		
		public function logout() {
         	/* logout and redirect to url set in app controller */
       	 	return $this->redirect($this->Auth->logout());
    	}
		
		public function add(){
			if($this->request->is('post')){
				$this->User->create();
				$this->request->data['User']['salt'] = Security::generateAuthKey();
				 if ($this->User->save($this->request->data)) {
               		 $this->Session->setFlash(__('The user has been saved'));
               	 	 if ($this->Auth->login()) {
        			 	return $this->redirect($this->Auth->redirect());
          	  		 }
           		 }
           		 $this->Session->setFlash(__('The user could not be saved. Please, try again'));
			}
		}
		
		public function addMobile(){
			$this->layout = 'ajax';
			if($this->request->is('post')){
				$this->User->create();
				$this->request->data['User']['username'] = $this->request->data['username'];
				$this->request->data['User']['password'] = $this->request->data['password'];
				$this->request->data['User']['email'] = $this->request->data['email'];
				$this->request->data['User']['firstName'] = $this->request->data['firstName'];
				$this->request->data['User']['lastName'] = $this->request->data['lastName'];
				$this->request->data['User']['salt'] = Security::generateAuthKey();
				if ($this->User->save($this->request->data)) {
					if ($this->Auth->login()) {
						$user['User'] = $this->Auth->user();
						$user['Token']['Private'] = Security::generateAuthKey();
						$user['Token']['Public'] = Security::generateAuthKey();
						$this->User->id = $this->Auth->user('id');
						$this->User->set($this->request->data);
						if ($this->User->saveField('public_access_token', $user['Token']['Public'])) {
							if ($this->User->saveField('private_access_token', $user['Token']['Private'])) {
								$user['result'] = 'success';
								return new CakeResponse(array('body' => json_encode($user)));
							}
						}
					}
				}
				$user['result'] = 'failure';
				$user['errors'] = $this->User->validationErrors;
           		return new CakeResponse(array('body' => json_encode($user)));
			}
		}
		
		public function facebookMobile(){
			$this->layout = 'ajax';
			if($this->request->is('post') ){
			
				$this->Facebook->setAccessToken( $this->request->data['token'] );
				if( $this->request->data['fbID'] == $this->Facebook->getUser() ){
				
					// We have a user ID, so probably a logged in user.
					// If not, we'll get an exception, which we handle below.
					try {
					
						$user_profile = $this->Facebook->api('/me','GET');
						
						$user = $this->User->find('first', array(
							'conditions' => array(
								'fbID' => $this->request->data['fbID']
							)
						));
						
						if($user){
							unset($user['User']['password']);
							unset($user['User']['salt']);
							unset($user['User']['public_access_token']);
							unset($user['User']['private_access_token']);
							$this->User->set($user);
							if($user['User']['firstName'] != $user_profile['first_name']){
								$this->User->saveField('firstName', $user_profile['first_name']);
							}
							if($user['User']['lastName'] != $user_profile['last_name']){
								$this->User->saveField('lastName', $user_profile['last_name']);
							}
							if($user['User']['email'] != $user_profile['email']){
								$this->User->saveField('email', $user_profile['email']);
							}
							$user['Token']['Private'] = Security::generateAuthKey();
							$user['Token']['Public'] = Security::generateAuthKey();
							
							if ($this->User->saveField('public_access_token', $user['Token']['Public'])) {
								if ($this->User->saveField('private_access_token', $user['Token']['Private'])) {
									$user['result'] = 'success';
									return new CakeResponse(array('body' => json_encode($user)));
								}
							}
							$user['result'] = 'failure';
							return new CakeResponse(array('body' => json_encode($user)));
						}
						else if($this->request->data['new'] == 'true'){
							$this->User->create();
							if( array_key_exists('username', $user_profile) ){
								$this->request->data['User']['username'] = $user_profile['username'];
							}
							else{
								$this->request->data['User']['username'] = substr($user_profile['first_name'], 0, 1) . $user_profile['last_name'];
							}
							$this->request->data['User']['password'] = "000000";
							$this->request->data['User']['email'] = $user_profile['email'];
							$this->request->data['User']['firstName'] = $user_profile['first_name'];
							$this->request->data['User']['lastName'] = $user_profile['last_name'];
							$this->request->data['User']['fbID'] = $this->request->data['fbID'];
							$this->request->data['User']['salt'] = Security::generateAuthKey();
							if ($this->User->saveAll($this->request->data)) {
								$user['User'] = $this->request->data['User'];
								$user['Token']['Private'] = Security::generateAuthKey();
								$user['Token']['Public'] = Security::generateAuthKey();
								$userID = $this->User->find('first', array(
									'conditions' => array(
										'fbID' => $this->request->data['fbID']
									),
									'fields' => array(
										'id'
									)
								));
								$user['User']['id'] = $userID['User']['id'];
								$this->User->set($this->request->data);
								if ($this->User->saveField('public_access_token', $user['Token']['Public'])) {
									if ($this->User->saveField('private_access_token', $user['Token']['Private'])) {
										if( $this->User->saveField('password', '111111') ){
											$user['result'] = 'success';
											return new CakeResponse(array('body' => json_encode($user)));
										}
									}
								}
							}
							$user['result'] = 'failure';
							$user['errors'] = $this->User->validationErrors;
							return new CakeResponse(array('body' => json_encode($user)));
						}
						
						$user['result'] = 'none';
						
						return new CakeResponse(array('body' => json_encode( $user )));

					} catch(FacebookApiException $e) {
						// If the user is logged out, you can have a 
						// user ID even though the access token is invalid.
						// In this case, we'll get an exception, so we'll
						// just ask the user to login again here.
						
						$user['result'] = 'faliureToken';
						
						error_log($e->getType());
						error_log($e->getMessage());
						   
					} 
					
				}
				else{ 
					$user['result'] = 'failureToken';
					return new CakeResponse(array('body' => json_encode( $user ))); 
				}
	            
			}
			$user['result'] = 'failure';
			return new CakeResponse(array('body' => json_encode( $user ))); 
		}
		
		public function changeOldPass(){
			if ($this->request->is('post')){
				$this->User->id = $this->Auth->user('id');
                
                if(!$this->User->exists()){
      				$this->Session->setFlash(__('Unable to find User.'));
   				}
                
                $this->User->set($this->request->data);
			
				if($this->request->data['User']['password']){
					$this->request->data['User']['salt'] = Security::generateAuthKey();
					if ($this->User->save($this->request->data)) {
						return $this->redirect($this->Auth->redirect());
					}
					else{
					 	$this->Session->setFlash(__('Something went wrong..'));
					 }
                }
			}
		}
		
		public function edit(){
 
            if ($this->request->is('post')) {
                $this->User->id = $this->Auth->user('id');
                
                if(!$this->User->exists()){
      				$this->Session->setFlash(__('Unable to find User.'));
   				}
                $success = true;
                $this->User->set($this->request->data);
                
                if($this->request->data['User']['firstNameEdit']){
                	if($this->User->Validates(array('fieldList' => array('firstNameEdit')))){
						if ($this->User->saveField('firstName', $this->request->data['User']['firstNameEdit'])) {
							$this->Session->write('Auth.User.firstName', $this->request->data['User']['firstNameEdit']);
							$success = true;
						}else { $success = false; }
					}else { $success = false; }
                }
                
                if($this->request->data['User']['lastNameEdit']){
                	if($this->User->Validates(array('fieldList' => array('lastNameEdit')))){
						if ($this->User->saveField('lastName', $this->request->data['User']['lastNameEdit'])) {
							$this->Session->write('Auth.User.lastName', $this->request->data['User']['lastNameEdit']);
							$success = true;
						}else { $success = false; }
					}else { $success = false; }
                }
                
                if($this->request->data['User']['emailEdit']){
                	if($this->User->Validates(array('fieldList' => array('emailEdit')))){
						if ($this->User->saveField('email', $this->request->data['User']['emailEdit'])) {
							$this->Session->write('Auth.User.email', $this->request->data['User']['emailEdit']);
							$success = true;
						}else { $success = false; }
					}else { $success = false; }
                }
                
                if($this->request->data['User']['passwordEdit']){
                	if($this->User->Validates(array('fieldList' => array('currentPassword', 'passwordEdit', 'passwordConfirmEdit')))){
						if ($this->User->saveField('password', $this->request->data['User']['passwordEdit'])) {
							$success = true;
						}else { $success = false; }
					}else { $success = false; }
                }
                
                if($success){
                    $this->Session->setFlash(__('The user has been updated'));
                    $this->redirect(array('action' => 'showProfile'));
                }else{
                   // $this->Session->setFlash(__('Unable to update your user.'));
                }
            }
		}
		
		public function findUserDrop($wallet_id){
			$this->User->recursive = -1;
			if ( $this->request->is('ajax') ) {
			 	$this->autoRender = false;
    			$this->layout = 'ajax';
    			
    			 $users=$this->User->find('all', array(
	            	'conditions' => array(
	            		'OR' => array(
	            			'User.username LIKE'=>'%'.$_GET['term'].'%',
	            			'User.firstName LIKE'=>'%'.$_GET['term'].'%',
	            			'User.lastName LIKE'=>'%'.$_GET['term'].'%',
	            			'User.email LIKE'=>'%'.$_GET['term'].'%'
	            		)
	            	)
	            ));
	            /*
		         *	Find out if person is already in the wallet  
	             */
	            if($wallet_id > 0){
					$dup=$this->User->find('all', array(
						'joins' => array(
							array(
								'table' => 'wallet_relations',
								'alias' => 'WalletRelation',
								'type'  => 'INNER',
								'conditions' => array(
									'User.id = WalletRelation.user_id'
								)
							)
						),
						'conditions' => array(
							'WalletRelation.wallet_id' => $wallet_id
						),
						'feilds' => 'User.id'
					));
				
			   
				
					for($i = 0; $i < count($users); $i++){
						$users[$i]['User']['inWallet'] = '0';
						for($j = 0; $j < count($dup); $j++){
							if($users[$i]['User']['id'] == $dup[$j]['User']['id']){
								$users[$i]['User']['inWallet'] = '1';
								break;           			
							}
						}
					}
				
					echo json_encode($users);
				}
				/*
				 *	Find out if person is already friend, or if a request has been sent
				 */
				else if($wallet_id == 0){
					/*
					 *	Do a query for all rows in friends table where either user_id_1
					 *	or user_id_2 is the user and active = 1
					 */
					$friends = $this->User->find('all', array(
						'joins' => array(
							array(
								'table' => 'friends',
								'alias' => 'Friend',
								'type' => 'RIGHT',
								'conditions' => array(
									'User.id = Friend.user_id_1',
									'User.id = Friend.user_id_2'
								)
							)
						),
						'conditions' => array(
							'OR' => array(
								'Friend.user_id_1' => $this->Auth->user('id'),
								'Friend.user_id_2' => $this->Auth->user('id')
							),
							'Friend.active' => '1'
						),
						'fields' => array(
							'Friend.user_id_1', 'Friend.user_id_2', 'Friend.accept'
						)
					));
					/*
					 *	Check if searched user is already a friend or if a request has been sent
					 *  	the ['User']['inWallet'] is used to signify this ->
					 *		0 -> not a friend
					 *		1 -> a friend
					 *		2 -> user sent request
					 *		3 -> user received request
					 */
					for($i = 0; $i < count($users); $i++){
						$users[$i]['User']['inWallet'] = '0';
						for($j = 0; $j < count($friends); $j++){
							if($users[$i]['User']['id'] == $friends[$j]['Friend']['user_id_1']){
								if($friends[$j]['Friend']['accept'] == 1){
									$users[$i]['User']['inWallet'] = '1';
								}
								else{
									$users[$i]['User']['inWallet'] = '3';
								}
								break;           			
							}
							else if($users[$i]['User']['id'] == $friends[$j]['Friend']['user_id_2']){
								if($friends[$j]['Friend']['accept'] == 1){
									$users[$i]['User']['inWallet'] = '1';
								}
								else{
									$users[$i]['User']['inWallet'] = '2';
								}
								break;             			
							}
						}
					}
					
					echo json_encode($users);
					
				} // end else if($wallet_id == 0)
	        }
		}
		
		public function findUser($wallet_id = -1){
			if($wallet_id > -1){
				$this->set('wallet_id', $wallet_id);
			}
			else{
				return $this->redirect(array('controller' => 'Wallets', 'action' => 'index'));
			}
		}
		
		public function showProfile($user_id = 0){
			if($user_id == 0){
				$user['User']['id'] = $this->Auth->user('id');
				$user['User']['username'] = $this->Auth->user('username');
				$user['User']['email'] = $this->Auth->user('email');
				$user['User']['firstName'] = $this->Auth->user('firstName');
				$user['User']['lastName'] = $this->Auth->user('lastName');
				$this->set('user', $user);
				$this->set('self', '1');//checks if user is in its own profile
			}
			else{
				$user = $this->User->find('first', array(
					'conditions' => array(
						'User.id' => $user_id
					),
					'fields' => array(
						'User.id', 'User.username', 'User.email', 'User.firstName', 'User.lastName'
					)
				));
				$this->set('user', $user);
				if($user_id == $this->Auth->user('id')){
					$this->set('self', '1');
				}	
				else{
					$this->set('self', '0');
				}
			}
		}
		
		public function uploadProfilePic(){
			$this->layout = 'ajax';
			
			if($this->request->is('post')){
				echo $this->UploadPic->uploadProfilePic($this->Auth->user('id'), $this->request->data['URL'], '1', $_FILES['file']);
				echo $_FILES['file']['name'];
			}
			else{
				echo "no.";
			}
		}
		
		public function getUser(){
			$this->layout = 'ajax';
			if($this->request->is('post')){
				$tokenSuccess = $this->AccessToken->checkAccessTokens($this->request->data['public_token'], $this->request->data['private_token'], $this->request->data['timeStamp']);
				
				if($tokenSuccess == "they good"){
					$user = $this->User->find('first', array(
						'conditions' => array(
							'User.id' => $this->request->data['user_id']
						)
					));
					if($user){
						unset($user['User']['password']);
						unset($user['User']['salt']);
						unset($user['User']['public_access_token']);
						unset($user['User']['private_access_token']);
						$result['empty'] = false;
						$result['user'] = $user['User'];
					}
					else{
						$result['empty'] = true;
					}
					$result['result'] = 'success';
					return new CakeResponse(array('body' => json_encode($result)));
				}
				else if($tokenSuccess == "public"){
					$result['result'] = 'bad token';
					return new CakeResponse(array('body' => json_encode($result)));
				}
				else if($tokenSuccess == "private"){
					$result['result'] = 'bad token';
					return new CakeResponse(array('body' => json_encode($result)));
				}
				else if($tokenSuccess == "bad data"){
					$result['result'] = 'bad token';
					return new CakeResponse(array('body' => json_encode($result)));
				}
				else{
					$result['result'] = 'bad token';
					return new CakeResponse(array('body' => json_encode($result)));
				}	
		
			}
			else{
				$result['result'] = 'not post';
				return new CakeResponse(array('body' => json_encode($result)));
			}
		}
		
		public function getUsersMobile(){
			$this->layout = 'ajax';
			if($this->request->is('post')){
				$tokenSuccess = $this->AccessToken->checkAccessTokens($this->request->data['public_token'], $this->request->data['private_token'], $this->request->data['timeStamp']);
				
				if($tokenSuccess == "they good"){
					$user = $this->User->find('all', array(
						'conditions' => array(
							'User.id' => (array) $this->request->data['userID']
						)
					));
					
					if($user){
						$result['empty'] = false;
						for($i = 0; $i < count($user); $i++){
							unset($user[$i]['User']['password']);
							unset($user[$i]['User']['salt']);
							unset($user[$i]['User']['public_access_token']);
							unset($user[$i]['User']['private_access_token']);
						}
						$result['users'] = $user;
					}
					else{
						$result['empty'] = true;
					}
					$result['result'] = 'success';
					return new CakeResponse(array('body' => json_encode($result)));
				}
				else if($tokenSuccess == "public"){
					$result['result'] = 'bad token';
					return new CakeResponse(array('body' => json_encode($result)));
				}
				else if($tokenSuccess == "private"){
					$result['result'] = 'bad token';
					return new CakeResponse(array('body' => json_encode($result)));
				}
				else if($tokenSuccess == "bad data"){
					$result['result'] = 'bad token';
					return new CakeResponse(array('body' => json_encode($result)));
				}
				else{
					$result['result'] = 'bad token';
					return new CakeResponse(array('body' => json_encode($result)));
				}	
		
			}
			else{
				$result['result'] = 'not post';
				return new CakeResponse(array('body' => json_encode($result)));
			}
		}
		
		public function sendFile() {
			
			$this->response->file('webroot/GroupWalletCake.apk');
			// Return response object to prevent controller from trying to render
			// a view
			return $this->response;
		}
		
		public function pagination() {
			// we prepare our query, the cakephp way!
			$this->paginate = array(
				'conditions' => array('User.id >' => '0'),
				'limit' => 3,
				'order' => array('id' => 'desc')
			);
	
			// we are using the 'User' model
			$users = $this->paginate('User');
	
			// pass the value to our view.ctp
			$this->set('users', $users);
		}
		
		public function getPaginateUsers() {
			$this->layout = 'ajax';
			if($this->request->is('post')){
				$tokenSuccess = $this->AccessToken->checkAccessTokens($this->request->data['public_token'], $this->request->data['private_token'], $this->request->data['timeStamp']);
				
				if($tokenSuccess == "they good"){
					$user = $this->User->find('all', array(
						'conditions' => array(
							'NOT' => array(
								'id' => (array) $this->request->data['usersExclude']
							),
							'OR' => array(
	            			'User.username LIKE'=>'%'.$this->request->data['match'].'%',
	            			'User.firstName LIKE'=>'%'.$this->request->data['match'].'%',
	            			'User.lastName LIKE'=>'%'.$this->request->data['match'].'%',
	            			'User.email LIKE'=>'%'.$this->request->data['match'].'%'
	            			)
						),
						'order' => 'username ASC',
						'offset' => $this->request->data['start'],
						'limit' => $this->request->data['length']
					));
					if($user){
						$result['empty'] = false;
						for($i = 0; $i < count($user); $i++){
							unset($user[$i]['User']['password']);
							unset($user[$i]['User']['salt']);
							unset($user[$i]['User']['public_access_token']);
							unset($user[$i]['User']['private_access_token']);
						}
						$result['users'] = $user;
					}
					else{
						$result['empty'] = true;
					}
					$result['result'] = 'success';
					return new CakeResponse(array('body' => json_encode($result)));
				}
				else if($tokenSuccess == "public"){
					$result['result'] = 'bad token';
					return new CakeResponse(array('body' => json_encode($result)));
				}
				else if($tokenSuccess == "private"){
					$result['result'] = 'bad token';
					return new CakeResponse(array('body' => json_encode($result)));
				}
				else if($tokenSuccess == "bad data"){
					$result['result'] = 'bad token';
					return new CakeResponse(array('body' => json_encode($result)));
				}
				else{
					$result['result'] = 'bad token';
					return new CakeResponse(array('body' => json_encode($result)));
				}	
		
			}
			else{
				$result['result'] = 'not post';
				return new CakeResponse(array('body' => json_encode($result)));
			}
		}
	}
?>