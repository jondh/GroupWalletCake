<?php
	class WalletRelationsController extends AppController { 
		
		public $components = array('Get');
		
		public function index($wallet_id){
		
			$this->request->data['WalletRelation']['wallet_id'] = $wallet_id;
			$this->request->data['WalletRelation']['user_id'] = $this->Auth->user('id');
			$this->WalletRelation->set($this->request->data);
			if (!$this->WalletRelation->validates()){
				$this->Session->setFlash(__('You may not have access to that wallet'));
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
	
		public function addUser($wallet_id, $user_id){
			if($user_id && $wallet_id){
			
				if($this->request->is('post')){
					$this->WalletRelation->create();
					$this->request->data['WalletRelation']['wallet_id'] = $wallet_id;
					$this->request->data['WalletRelation']['user_id'] = $user_id;
					$this->request->data['WalletRelation']['accept'] = '1';
					// validate data
					$this->WalletRelation->set($this->request->data);
					if ($this->WalletRelation->validates()){
						if ($this->WalletRelation->save($this->request->data)) {
               				 $this->Session->setFlash(__('The user was added to the wallet'));
        					 return $this->redirect(array('controller' => 'WalletRelations', 'action' => 'index', $wallet_id));
           				}
           				$this->Session->setFlash(__('The user could not be added. Please, try again'));
					}
					else{
						$this->Session->setFlash(__('The request did not validate'));
						return $this->redirect(array('controller' => 'WalletRelations', 'action' => 'index', $wallet_id));
					}
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
			
			$owe = $this->Get->getOweWallet(
				$this->Auth->user('id'), $wallet_id);
			$owed = $this->Get->getOwedWallet(
				$this->Auth->user('id'), $wallet_id);
			
			$this->WalletRelation->id = $wallet_id;
       		
       		if(!$this->WalletRelaion->exists()){
       			$this->Session->setFlash(__('Cannot find wallet / Cannot currently leave a wallet you created'));
       		}
       		else if ( (($owe - $owed) > 0.05) || (($owe - $owed) < -0.05)) {
       			$this->Session->setFlash(__('You have to clear the debts before you can leave'));
       		}
       		else if ($this->WalletRelation->saveField('active', '0')) {
				$this->Session->setFlash(__('Successfully left wallet'));
				return $this->redirect(array('controller' => 'Wallets', 'action' => 'index'));
			}
			else { 
				$this->Session->setFlash(__('An error occured when deleting the wallet'));
			}
			return $this->redirect(array('action' => 'index', $wallet_id));
		}
	
	}
?>