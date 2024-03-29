<?php
	class FriendsController extends AppController { 
	
		public function beforeFilter(){
			parent::beforeFilter();
       		$this->Auth->allow('getFriendsMobile', 'addMobile', 'acceptDeclineMobile');
		}
		
		public $components = array('Get', 'AccessToken');
		
		/*
		 *	Find all friends of the logged in user
		 *		including requests from other people
		 *  	and ignoring the not active 'friendships'
		 *	Friend requests should be of the form where user_id_1 requests to
		 * 		be user_id_2 's friend and accept will be 0
		 *	A friend relationship allows the user_id to match user_id_1 or user_id_2
		 *		depending on if the user requested the friendship or not
		*/
		public function index(){
			
			// get friend requests received
			$requestsRec = $this->Friend->find('all', array(
				'joins' => array(
					array(
						'table' => 'users',
						'alias' => 'User',
						'type'  => 'INNER',
						'conditions' => array(
							'Friend.user_id_1 = User.id'
						)
					)
	            ),
				'conditions' => array(
					'user_id_2' => $this->Auth->user('id'),
					'accept' => '0',
					'active' => '1'
				),
				'fields' => array(
					'Friend.id', 'User.*'
				)
			));
			$this->set('requestsRec', $requestsRec);
			
			// get friend requests sent can be done by the opposite of above
			
			// get friend relationships where the user did NOT initiate the friendship
			$friends0 = $this->Friend->find('all', array(
				'joins' => array(
					array(
						'table' => 'users',
						'alias' => 'User',
						'type'  => 'INNER',
						'conditions' => array(
							'Friend.user_id_1 = User.id'
						)
					)
	            ),
				'conditions' => array(
					'user_id_2' => $this->Auth->user('id'),
					'accept' => '1',
					'active' => '1'
				),
				'fields' => array(
					'User.*'
				)
			));
			// get friend relationships where the user DID initiate the friendship
			$friends1 = $this->Friend->find('all', array(
				'joins' => array(
					array(
						'table' => 'users',
						'alias' => 'User',
						'type'  => 'INNER',
						'conditions' => array(
							'Friend.user_id_2 = User.id'
						)
					)
	            ),
				'conditions' => array(
					'user_id_1' => $this->Auth->user('id'),
					'accept' => '1',
					'active' => '1'
				),
				'fields' => array(
					'User.*'
				)
			));
			
			// join the friend relationships together and find the money for each
			$size = count($friends0) + count($friends1);
			$firstSize = count($friends0);
			for($i = 0; $i < $size; $i++){
				if($i >= $firstSize){
					$friends0[$i] = $friends1[$i - $firstSize];
				}
				// get money for transactions not in any wallet
				$friends0[$i]['money']['owe'] = $this->Get->getOweUserWallet(
					$this->Auth->user('id'), $friends0[$i]['User']['id'], 0);
				$friends0[$i]['money']['owed'] = $this->Get->getOwedUserWallet(
					$this->Auth->user('id'), $friends0[$i]['User']['id'], 0);
				$friends0[$i]['money']['total'] = $friends0[$i]['money']['owed'] - $friends0[$i]['money']['owe'];
				// get money for transactions anywhere
				$friends0[$i]['moneyT']['owe'] = $this->Get->getOweUser(
					$this->Auth->user('id'), $friends0[$i]['User']['id']);
				$friends0[$i]['moneyT']['owed'] = $this->Get->getOwedUser(
					$this->Auth->user('id'), $friends0[$i]['User']['id']);
				$friends0[$i]['moneyT']['total'] = $friends0[$i]['moneyT']['owed'] - $friends0[$i]['moneyT']['owe'];
				
			}
			$this->set('friends', $friends0);
		}	
		
		/*
		 * 	This function sends a friend request to $other_user_id
		 */
		public function add($other_user_id){
			if($other_user_id){
			
				if($this->request->is('post') || $this->request->is('get')){
					$this->Friend->create();
					$this->request->data['Friend']['user_id_1'] = $this->Auth->user('id');
					$this->request->data['Friend']['user_id_2'] = $other_user_id;
					
					if ($this->Friend->save($this->request->data)) {
						 $this->Session->setFlash(__('Sent friend request'));
						 return $this->redirect(array('controller' => 'Friends', 'action' => 'index'));
					}
					$this->Session->setFlash(__('The request was not sent. Please try again'));
				}
				$this->Session->setFlash(__('The request was not post'));
				return $this->redirect(array('controller' => 'Users', 'action' => 'showProfile'));
			}
			
			$this->Session->setFlash(__('Thats not a person :/'));
			return $this->redirect(array('controller' => 'Users', 'action' => 'showProfile'));
		}
		
		/*
		 *	This function accepts a friends request with the given row id
		 */
		public function accept($id){
			if($id){
				$this->Friend->id = $id;
                if(!$this->Friend->exists()){
      				$this->Session->setFlash(__('Unable to find User.'));
      				return $this->redirect(array('controller' => 'Users', 'action' => 'showProfile'));
   				}
   				$vali = $this->Friend->find('first', array(
   					'conditions' => array(
   						'OR' => array(
   							'Friend.user_id_1' => $this->Auth->user('id'),
   							'Friend.user_id_2' => $this->Auth->user('id')
   						),
   						'Friend.id' => $id
   					),
   					'fields' => array(
   						'Friend.id'
   					)
   				));
   				if(!$vali){
   					$this->Session->setFlash(__('You were shady'));
   					return $this->redirect(array('controller' => 'Users', 'action' => 'showProfile'));
   				}
   				
   				if ($this->Friend->saveField('accept', '1')) {
					$this->Session->setFlash(__('Friend added'));
					return $this->redirect(array('controller' => 'Friends', 'action' => 'index'));
				}
				else{
					$this->Session->setFlash(__('Unable to accept friend request'));
					return $this->redirect(array('controller' => 'Friends', 'action' => 'index'));
				}
			}
			$this->Session->setFlash(__('No id provided'));
			return $this->redirect(array('controller' => 'Users', 'action' => 'showProfile'));
		} 
		
		public function getFriendsMobile(){
			$this->layout = 'ajax';
			if($this->request->is('post')){
				$tokenSuccess = $this->AccessToken->checkAccessTokens($this->request->data['public_token'], $this->request->data['private_token'], $this->request->data['timeStamp']);
				
				if($tokenSuccess == "they good"){
					$friends = $this->Friend->find('all', array(
						'conditions' => array(
							'NOT' => array(
								'Friend.id' => $this->request->data['currentFriends']
							),
							'OR' => array(
								'Friend.user_id_1' => $this->request->data['user_id'],
								'Friend.user_id_2' => $this->request->data['user_id']
							)
						),
						'fields' => array(
							'Friend.*'
						)
					));
					$result['result'] = 'success';
					
					if($friends){
						$result['friends'] = $friends;
						$result['empty'] = false;
					}
					else{
						$result['empty'] = true;
					}
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
		
		public function addMobile(){
			$this->layout = 'ajax';
			if($this->request->is('post')){
				$tokenSuccess = $this->AccessToken->checkAccessTokens($this->request->data['public_token'], $this->request->data['private_token'], $this->request->data['timeStamp']);
				
				if($tokenSuccess == "they good"){
					$this->Friend->create();
					$this->request->data['Friend']['user_id_1'] = $this->request->data['user1'];
					$this->request->data['Friend']['user_id_2'] = $this->request->data['user2'];
					$this->request->data['Friend']['accept'] = $this->request->data['accept'];
					$this->request->data['Friend']['active_user'] = '1';
					if ($this->Friend->save($this->request->data, false)) {
						$friend = $this->Friend->find('first', array(
							'conditions' => array(
								'id' => $this->Friend->id
							)
						));
						
						if($friend){
							$result['result'] = 'success';
							$result['friend'] = $friend['Friend'];
							return new CakeResponse(array('body' => json_encode($result)));
						}
						else{
							$result['result'] = 'failure in find';
							return new CakeResponse(array('body' => json_encode($result)));
						}
					}
					else{
						$result['result'] = 'failure in save';
						return new CakeResponse(array('body' => json_encode($result)));
					}
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
		
		public function acceptDeclineMobile(){
			$this->layout = 'ajax';
			if($this->request->is('post')){
				$tokenSuccess = $this->AccessToken->checkAccessTokens($this->request->data['public_token'], $this->request->data['private_token'], $this->request->data['timeStamp']);
				
				if($tokenSuccess == "they good"){
					$curWR = $this->Friend->find('first', array(
						'conditions' => array(
							'user_id_1' => $this->request->data['user1'],
							'user_id_2' => $this->request->data['user2']
						)	
					));
					if(!$curWR){
						$curWR = $this->Friend->find('first', array(
							'conditions' => array(
								'user_id_1' => $this->request->data['user2'],
								'user_id_2' => $this->request->data['user1']
							)	
						));
					}
					$this->Friend->set( $curWR );
					
					if( $this->request->data['accept'] == 1 ){
						if ($this->Friend->saveField('accept', 1)) {
							$result['result'] = 'success';
							return new CakeResponse(array('body' => json_encode($result)));
						}
						else { 
							$result['result'] = 'failure saving';
							return new CakeResponse(array('body' => json_encode($result)));
						}
					}
					else if( $this->request->data['accept'] == 0 ){
						if ($this->Friend->delete($curWR['Friend'])) {
							$result['result'] = 'success';
							return new CakeResponse(array('body' => json_encode($result)));
						}
						else{
							$result['result'] = 'failure deleting';
							return new CakeResponse(array('body' => json_encode($result)));
						}
					}
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