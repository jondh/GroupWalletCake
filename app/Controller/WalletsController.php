<?php
	class WalletsController extends AppController { 
	
		public $components = array('Get');
	
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