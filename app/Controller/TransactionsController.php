<?php
	class TransactionsController extends AppController { 
		
		public function index(){
		
		}	
		
		public function add($wallet_id, $other_user_id){
			if($wallet_id && $other_user_id){
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
        				 return $this->redirect(array('controller' => 'WalletRelations', 'action' => 'index', $wallet_id));
           			 }
           			 $this->Session->setFlash(__('The user could not be saved. Please, try again'));
				 }
			 }
			 else{
			 	$this->Session->setFlash(__('You were shady'));
				return $this->redirect(array('controller' => 'wallets', 'action' => 'index', $wallet_id));
			 }
		}
	
		public function getTotalUserWallet($wallet_id, $other_user_id){
		
			$this->set('authUserID', $this->Auth->user('id'));
		
			if($wallet_id){
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
	
	
	
	}
?>