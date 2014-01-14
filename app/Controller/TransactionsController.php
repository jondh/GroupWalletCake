<?php
	class TransactionsController extends AppController { 
		
		public $components = array('Validate');
		
		public function index(){
		
		}	
		
		public function add($wallet_id = -1, $other_user_id = 0, $redirect = 0){
			
			if($wallet_id > -1 && $other_user_id > 0){
				/* Make sure that the user has access to this wallet */
				if(!$this->Validate->validateWalletUser($other_user_id, $wallet_id)){
					$this->Session->setFlash(__('You may not have access to that wallet'));
					return $this->redirect(array('controller' => 'Wallets', 'action' => 'index'));
				}
				
				if($this->request->is('post')){
					$this->Transaction->create();
					$this->request->data['Transaction']['wallet_id'] = $wallet_id;
					
					if($this->request->data['Transaction']['selection'] == 0){
						$this->request->data['Transaction']['oweUID'] = $this->Auth->user('id');
						$this->request->data['Transaction']['owedUID'] = $other_user_id;
					}
					else if($this->request->data['Transaction']['selection'] == 1){
						$this->request->data['Transaction']['owedUID'] = $this->Auth->user('id');
						$this->request->data['Transaction']['oweUID'] = $other_user_id;
					}
					
					if ($this->Transaction->save($this->request->data)) {
               			$this->Session->setFlash(__('Your transaction has been saved'));
               			if($redirect == 0){
               				return $this->redirect(array('controller' => 'Users', 'action' => 'showProfile', $wallet_id));
               			}
               			else if($redirect == 1){
        					return $this->redirect(array('controller' => 'WalletRelations', 'action' => 'index', $wallet_id));
           				}
           				else if($redirect == 2){
           					return $this->redirect(array('controller' => 'Friends', 'action' => 'index'));
           				}
           			}
           			$this->Session->setFlash(__('The user could not be saved. Please, try again'));
				 }
			 }
			 else{
			 	$this->Session->setFlash(__('You were shady'));
				$this->redirect($prevPage);
			 }
		}
	
		public function getTotalUserWallet($wallet_id, $other_user_id){
		
			if(!$this->Validate->validateWalletUser($other_user_id, $wallet_id)){
				$this->Session->setFlash(__('You may not have access to that wallet'));
				return $this->redirect(array('controller' => 'Wallets', 'action' => 'index'));
			}
		
			$this->set('authUserID', $this->Auth->user('id'));
		
			if($wallet_id > -1){
				$amount = $this->Transaction->find('all', array(
					'conditions' => array(
						'wallet_id' => $wallet_id,
						'OR' => array(
							'oweUID' => $this->Auth->user('id'),
							'owedUID' => $this->Auth->user('id')
						),
						'OR' => array(
							'owedUID' => $other_user_id,
							'oweUID' => $other_user_id
						),
					)
				));

				$this->set('transaction', $amount);
				
			}
		}
	
		public function getTotalWallet($wallet_id){
		
			if(!$this->Validate->validateWalletUser($this->Auth->user('id'), $wallet_id)){
				$this->Session->setFlash(__('You may not have access to that wallet'));
				return $this->redirect(array('controller' => 'Wallets', 'action' => 'index'));
			}
		
			$this->set('authUserID', $this->Auth->user('id'));
		
			if($wallet_id){
				$amount = $this->Transaction->find('all', array(
					'conditions' => array(
						'wallet_id' => $wallet_id,
						'OR' => array(
							'oweUID' => $this->Auth->user('id'),
							'owedUID' => $this->Auth->user('id')
						)
					)
				));
				$this->set('transaction', $amount);
			}
		
		}
		
		public function getTotalUser($other_user_id){
		
			if(!$this->Validate->validateWalletUser($other_user_id, 0)){
				$this->Session->setFlash(__('You may not have access to that wallet'));
				return $this->redirect(array('controller' => 'Wallets', 'action' => 'index'));
			}
		
			$this->set('authUserID', $this->Auth->user('id'));
		
			if($other_user_id){
				$amount = $this->Transaction->find('all', array(
					'conditions' => array(
						'OR' => array(
							'oweUID' => $this->Auth->user('id'),
							'owedUID' => $this->Auth->user('id')
						),
						'OR' => array(
							'owedUID' => $other_user_id,
							'oweUID' => $other_user_id
						)
					)
				));
				$this->set('transaction', $amount);
			}
		
		}
	}
?>