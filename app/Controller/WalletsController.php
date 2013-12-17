<?php
	class WalletsController extends AppController { 
	
		public $components = array('Get');
	
		public function index(){
			//$this->layout = 'wallets';
			// find wallets that user created
			$selfWallets = $this->Wallet->find('all', array(
				'conditions' => array(
					'Wallet.user_id' => $this->Auth->user('id')
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
							'Wallet.wallet_id = WalletRelations.wallet_id'
						)
					)
				),
       			'conditions' => array(
       				'WalletRelations.user_id' => $this->Auth->user('id')
       			),
       			'fields' => array(
       				'Wallet.*'
       			)
       		));
       		// combine the wallets
       		$j = count($wallets);
       		for($i = 0; $i < count($selfWallets); $i++){
       			$wallets[$j + $i] = $selfWallets[$i]; 
       		}
       		
       		if(count($wallets) != 0){
       			$uniqueWallets;
       			$j = 0;
       		
				for($i = 0; $i < count($wallets); $i++){
					$repeat = false;
					if($i != 0){
						for($k = 0; $k < count($uniqueWallets); $k++){
							if($wallets[$i]['Wallet']['wallet_id'] == $uniqueWallets[$k]['Wallet']['wallet_id']){
								$repeat = true;
								break;
							}
						}
					}
					if($repeat == false){
						$wallets[$i]['money']['owe'] = $this->Get->getOweWallet(
							$this->Auth->user('id'), $wallets[$i]['Wallet']['wallet_id']);
						$wallets[$i]['money']['owed'] = $this->Get->getOwedWallet(
							$this->Auth->user('id'), $wallets[$i]['Wallet']['wallet_id']);
						$wallets[$i]['money']['total'] = $wallets[$i]['money']['owed'] - $wallets[$i]['money']['owe'];
				
						$uniqueWallets[$j] = $wallets[$i];
						$j++;
					}
				}
			}
			$this->set('wallets', $uniqueWallets);
		}
		
		public function add(){
			if($this->request->is('post')){
				$this->Wallet->Create();
				$this->request->data['Wallet']['user_id'] = $this->Auth->user('id');
				if($this->Wallet->save($this->request->data)){
					$this->Session->setFlash(__('Your wallet has been saved'));
					return $this->redirect(array('action' => 'index')); 
				}
				$this->Session->setFlash(__('Unable tho add your wallet'));
			}
		}
	}
?>