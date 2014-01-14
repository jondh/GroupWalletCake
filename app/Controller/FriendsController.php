<?php
	class FriendsController extends AppController { 
	
		public $components = array('Get');
		
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
					'User.*'
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
			for($i = 0; $i < $size; $i++){
				if($i >= count($friends0)){
					$friends0[$i] = $friends1[$i - count($friends0)];
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
		
	}
?>