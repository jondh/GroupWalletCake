<?php
	class WalletsController extends AppController { 
	
		public function beforeFilter(){
			parent::beforeFilter();
       		$this->Auth->allow('addMobile');
		}
	
		public $components = array('Get', 'AccessToken', 'Insert');
	
		public function index(){
			//$this->layout = 'wallets';
			// find wallets that user created
			$selfWallets = $this->Wallet->find('all', array(
				'joins' => array(
					array(
						'table' => 'users',
						'alias' => 'User',
						'type' => 'INNER',
						'conditions' => array(
							'Wallet.user_id = User.id'
						)
					)
				),
				'conditions' => array(
					'Wallet.user_id' => $this->Auth->user('id'),
					'Wallet.active' => '1'
				),
       			'fields' => array(
       				'Wallet.*', 'User.firstName', 'User.lastName', 'User.id'
       			)
			));
			// find wallets that user is in
			$wallets = $this->Wallet->find('all', array(
				'joins' => array(
					array(
						'table' => 'wallet_relations',
						'alias' => 'WalletRelations',
						'type' => 'INNER',
						'conditions' => array(
							'Wallet.id = WalletRelations.wallet_id'
						)
					),
					array(
						'table' => 'users',
						'alias' => 'User',
						'type' => 'INNER',
						'conditions' => array(
							'Wallet.user_id = User.id'
						)
					)
				),
       			'conditions' => array(
       				'WalletRelations.user_id' => $this->Auth->user('id'),
       				'Wallet.active' => '1',
       				'WalletRelations.active_user' => '1'
       			),
       			'fields' => array(
       				'Wallet.*', 'User.firstName', 'User.lastName', 'User.id'
       			)
       		));
       		// combine the wallets
       		$j = count($wallets);
       		for($i = 0; $i < count($selfWallets); $i++){
       			$wallets[$j + $i] = $selfWallets[$i]; 
       		}
       		// get the unique wallets (should be done already ?) and find the money owe / owed for each
       		if(count($wallets) != 0){
       			$uniqueWallets;
       			$j = 0;
       		
				for($i = 0; $i < count($wallets); $i++){
					$repeat = false;
					if($i != 0){
						for($k = 0; $k < count($uniqueWallets); $k++){
							if($wallets[$i]['Wallet']['id'] == $uniqueWallets[$k]['Wallet']['id']){
								$repeat = true;
								break;
							}
						}
					}
					if($repeat == false){
						$wallets[$i]['money']['owe'] = $this->Get->getOweWallet(
							$this->Auth->user('id'), $wallets[$i]['Wallet']['id']);
						$wallets[$i]['money']['owed'] = $this->Get->getOwedWallet(
							$this->Auth->user('id'), $wallets[$i]['Wallet']['id']);
						$wallets[$i]['money']['total'] = $wallets[$i]['money']['owed'] - $wallets[$i]['money']['owe'];
						$wallets[$i]['money']['totalEverything'] = $this->Get->getWalletTotal($wallets[$i]['Wallet']['id']);
						
				
						$uniqueWallets[$j] = $wallets[$i];
						$j++;
					}
				}
			}
			$this->set('user', $this->Auth->user);
			$this->set('wallets', $uniqueWallets);
		}
		
		public function add(){
			if($this->request->is('post')){
				$this->Wallet->Create();
				$this->request->data['Wallet']['user_id'] = $this->Auth->user('id');
				if($this->Wallet->save($this->request->data)){
					$this->Session->setFlash(__('Your wallet has been saved'));
					return $this->redirect(array('controller' => 'WalletRelations', 'action' => 'addUser', $this->Wallet->id, $this->Auth->user('id')));
				}
				$this->Session->setFlash(__('Unable tho add your wallet'));
			}
		}
		
		public function addMobile(){
			$this->layout = 'ajax';
			if($this->request->is('post')){
				$tokenSuccess = $this->AccessToken->checkAccessTokens($this->request->data['public_token'], $this->request->data['private_token'], $this->request->data['timeStamp']);
				
				if($tokenSuccess == "they good"){
					
					$walletData['Wallet']['user_id'] = $this->request->data['userID'];
					$walletData['Wallet']['name'] = $this->request->data['name'];
					$walletData['Wallet']['date'] = NULL;
					$walletData['Wallet']['active'] = '1';
					
					$db = ConnectionManager::getDataSource('default');
					if (!$db->isConnected()) {
						$result['result'] = 'failureDBConn';
						return new CakeResponse(array('body' => json_encode($result)));
					} else {
						$db->rawQuery(" BEGIN; 
										INSERT INTO wallets (name, user_id) VALUES ('".$this->request->data['name']."',".$this->request->data['userID']."); 
										INSERT INTO wallet_relations (wallet_id, user_id) VALUES ( (SELECT id FROM wallets WHERE name = '".$this->request->data['name']."' AND user_id = ".$this->request->data['userID']." ) ,".$this->request->data['userID']."); 
										COMMIT;");
						
						$wallet = $this->Wallet->find('first', array(
							'joins' => array(
								array(
									'table' => 'wallet_relations',
									'alias' => 'WalletR',
									'type' => 'INNER',
									'conditions' => array(
										'Wallet.id = WalletR.wallet_id'
									)
								),
							),
							'conditions' => array(
								'Wallet.name' => $this->request->data['name'],
								'Wallet.user_id' => $this->request->data['userID']
							),
							'fields' => array(
								'Wallet.*', 'WalletR.*'
							)
						));
						if($wallet){
							$result['result'] = 'success';
							$result['wallet'] = $wallet['Wallet'];
							$result['relation'] = $wallet['WalletR'];
							return new CakeResponse(array('body' => json_encode($result)));
						}
						$result['result'] = 'failure in insert';
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
		
		public function delete($wallet_id = 0){
			if($wallet_id == 0){
				$this->Session->setFlash(__('Please enter a valid wallet id'));
				return $this->redirect(array('action' => 'index')); 
			}
			// find everyone else in the wallet
			$users = $this->Wallet->find('all', array(
				'joins' => array(
					array(
						'table' => 'wallet_relations',
						'alias' => 'WalletRelations',
						'type' => 'INNER',
						'conditions' => array(
							'Wallet.id = WalletRelations.wallet_id'
						)
					)
				),
       			'conditions' => array(
       				'WalletRelations.wallet_id' => $wallet_id,
       				'WalletRelations.active_user' => '1'
       			),
       			'fields' => array(
       				'WalletRelations.user_id'
       			)
       		));
       		// check if anyone else is in the wallet
       		for($i = 0; $i < count($users); $i++){
       			if($users[$i]['WalletRelations']['user_id'] != $this->Auth->user('id')){
       				$this->Session->setFlash(__('Make sure that everybody is removed / paid off from the wallet before deleting'));
					return $this->redirect(array('action' => 'index')); 
       			}
       		}
       		// check if wallet exists and make the wallet inactive if user is only one in wallet
       		$this->Wallet->id = $wallet_id;
       		
       		if(!$this->Wallet->exists()){
       			$this->Session->setFlash(__('Cannot find wallet'));
       		}
       		else if ($this->Wallet->saveField('active', '0')) {
				$this->Session->setFlash(__('Successfully removed wallet'));
			}
			else { 
				$this->Session->setFlash(__('An error occured when deleting the wallet'));
			}
			return $this->redirect(array('action' => 'index')); 
		}
	}
?>