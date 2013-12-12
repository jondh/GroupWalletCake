<?php
	class WalletRelationsController extends AppController { 
		
		public $components = array('Get');
		
		public function index($wallet_id){
			
			$this->set('authUserID', $this->Auth->user('id'));
			
			$this->set('oweAmount', $this->Get->getOweUserWallet());
			echo $this->Get->getOweUserWallet();
		
			$this->set('wallet_relations', $this->WalletRelation->find('all', array(
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
			));
			
		}	
	
	}
?>