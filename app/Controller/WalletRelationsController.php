<?php
	class WalletRelationsController extends AppController { 
		
		public function beforeFilter(){
			parent::beforeFilter();
       		$this->Auth->allow('getWallets', 'getWalletRelations');
		}
		
		public $components = array('Get', 'AccessToken');
		
		public function index($wallet_id){
		
			$this->request->data['WalletRelation']['wallet_id'] = $wallet_id;
			$this->request->data['WalletRelation']['user_id'] = $this->Auth->user('id');
			$this->WalletRelation->set($this->request->data);
			if (!$this->WalletRelation->validates()){
				$this->Session->setFlash(__($wallet_id));
				return $this->redirect(array('controller' => 'Wallets', 'action' => 'index'));
			}
			
			$this->set('authUserID', $this->Auth->user('id'));
			
			//$this->set('oweAmount', $this->Get->getOweUserWallet());
			echo $this->Get->getOweUserWallet();
		
			$usersInWallet = $this->WalletRelation->find('all', array(
				'joins' => array(
					array(
						'table' => 'users',
						'alias' => 'UserWR',
						'type' => 'LEFT',
						'conditions' => array(
							'UserWR.id = WalletRelation.user_id'
						)
					)
				),
				'conditions' => array(
					'WalletRelation.wallet_id' => $wallet_id,
					'WalletRelation.accept' => '1'
				),
				'fields' => array(
					'WalletRelation.*', 'UserWR.firstName', 'UserWR.lastName'
				)
				)
			);
			
			$othersInWallet;
			$j = 0;
			for($i = 0; $i < count($usersInWallet); $i++){
				if($usersInWallet[$i]['WalletRelation']['user_id'] != $this->Auth->user('id')){
					
					
				$usersInWallet[$i]['money']['owe'] = $this->Get->getOweUserWallet(
					$this->Auth->user('id'), $usersInWallet[$i]['WalletRelation']['user_id'], $wallet_id);
				$usersInWallet[$i]['money']['owed'] = $this->Get->getOwedUserWallet(
					$this->Auth->user('id'), $usersInWallet[$i]['WalletRelation']['user_id'], $wallet_id);
				$usersInWallet[$i]['money']['total'] = $usersInWallet[$i]['money']['owed'] - $usersInWallet[$i]['money']['owe'];
				
					$othersInWallet[$j] = $usersInWallet[$i];
					$j++;
				}
			}
			$this->set('wallet_id', $wallet_id);
			$this->set('wallet_relations', $othersInWallet);
		}	
		
		public function getWallets(){
			$this->layout = 'ajax';
			if($this->request->is('post')){
				$tokenSuccess = $this->AccessToken->checkAccessTokens($this->request->data['public_token'], $this->request->data['private_token'], $this->request->data['timeStamp']);
				
				if($tokenSuccess == "they good"){
					$user_id = $this->request->data['user_id'];
		
					$wallets = $this->WalletRelation->find('all', array(
						'joins' => array(
							array(
								'table' => 'wallets',
								'alias' => 'Wallet',
								'type' => 'INNER',
								'conditions' => array(
									'Wallet.id = WalletRelation.wallet_id'
								)
							),
						),
						'conditions' => array(
							'WalletRelation.user_id' => $user_id,
							'WalletRelation.accept' => '1'
						),
						'fields' => array(
							'Wallet.*'
						)
					));
					$result['result'] = 'success';
					$result['wallets'] = $wallets;
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
		
		public function getWalletRelations(){
			$this->layout = 'ajax';
			if($this->request->is('post')){
				$tokenSuccess = $this->AccessToken->checkAccessTokens($this->request->data['public_token'], $this->request->data['private_token'], $this->request->data['timeStamp']);
				
				if($tokenSuccess == "they good"){
					$walletRelations = $this->WalletRelation->find('all', array(
						'conditions' => array(
							'NOT' => array(
								'WalletRelation.user_id' => $this->request->data['user_id'],
							),
							'WalletRelation.wallet_id' => $this->request->data['wallet_id'],
							'WalletRelation.accept' => '1'
						),
						'fields' => array(
							'WalletRelation.*'
						)
					));
					$result['result'] = 'success';
					$result['walletRelations'] = $walletRelations;
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
	
		public function addUser($wallet_id, $user_id){
			if($user_id && $wallet_id){
			
				if($this->request->is('post') || $this->request->is('get')){
					$this->WalletRelation->create();
					$this->request->data['WalletRelation']['wallet_id'] = $wallet_id;
					$this->request->data['WalletRelation']['user_id'] = $user_id;
					$this->request->data['WalletRelation']['accept'] = '1';
					if ($this->WalletRelation->save($this->request->data)) {
						 $this->Session->setFlash(__('Added to wallet'));
						 return $this->redirect(array('controller' => 'WalletRelations', 'action' => 'index', $wallet_id));
					}
					$this->Session->setFlash(__('The user could not be added. Please, try again'));
				}
				$this->Session->setFlash(__('The request was not post'));
				return $this->redirect(array('controller' => 'WalletRelations', 'action' => 'index', $wallet_id));
			}
			if($wallet_id){
				$this->Session->setFlash(__('There is not a user id'));
				return $this->redirect(array('controller' => 'WalletRelations', 'action' => 'index', $wallet_id));
			}
			$this->Session->setFlash(__('There is not a wallet id'));
			return $this->redirect(array('controller' => 'Wallets', 'action' => 'index'));
			
		}
		
		public function leave($wallet_id){
			if(!$wallet_id){
				$this->Session->setFlash(__('There is not a wallet id'));
				return $this->redirect(array('controller' => 'Wallets', 'action' => 'index'));
			}
			// find money owe / owed in wallet
			$owe = $this->Get->getOweWallet(
				$this->Auth->user('id'), $wallet_id);
			$owed = $this->Get->getOwedWallet(
				$this->Auth->user('id'), $wallet_id);
			
			// find the table id of wallet relation
			$table_id = $this->WalletRelation->find('first', array(
				'conditions' => array(
					'WalletRelation.wallet_id' => $wallet_id,
					'WalletRelation.user_id' => $this->Auth->user('id')
				),
				'fields' => array(
					'WalletRelation.id'
				))
			);
			
			$this->WalletRelation->id = $table_id;
       		
       		if(!$this->WalletRelation->exists()){
       			$this->Session->setFlash(__('Cannot find wallet / Cannot currently leave a wallet you created'));
       		}
       		else if ( (($owe - $owed) > 0.05) || (($owe - $owed) < -0.05)) {
       			$this->Session->setFlash(__('You have to clear the debts before you can leave'));
       		}
       		else if ($this->WalletRelation->saveField('active_user', '0')) {
				$this->Session->setFlash(__('Successfully left wallet'));
				return $this->redirect(array('controller' => 'Wallets', 'action' => 'index'));
			}
			else { 
				$this->Session->setFlash(__('An error occured while deleting the wallet'));
			}
			return $this->redirect(array('action' => 'index', $wallet_id));
		}
	
	}
?>