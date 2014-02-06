<?php
	class UsersController extends AppController { 
	
	
		public function beforeFilter(){
			parent::beforeFilter();
       		$this->Auth->allow('login','add');
		}
		
		public $components = array('UploadPic');
	
		public function index(){
		
		}
	
		public function login(){
			$this->layout = 'login';
			if ($this->request->is('post')) {
        		/* login and redirect to url set in app controller */
           		if ($this->Auth->login()) {
        			return $this->redirect($this->Auth->redirect());
          	  	}
          	  	$this->Session->setFlash(__('Invalid username or password, try again'));
       		}
		}
		
		public function logout() {
         	/* logout and redirect to url set in app controller */
       	 	return $this->redirect($this->Auth->logout());
    	}
		
		public function add(){
			if($this->request->is('post')){
				$this->User->create();
				 if ($this->User->save($this->request->data)) {
               		 $this->Session->setFlash(__('The user has been saved'));
               	 	 if ($this->Auth->login()) {
        			 	return $this->redirect($this->Auth->redirect());
          	  		 }
           		 }
           		 $this->Session->setFlash(__('The user could not be saved. Please, try again'));
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
	}
?>