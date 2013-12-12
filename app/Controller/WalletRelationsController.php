<?php
	class WalletRelationsController extends AppController { 
		
		public $components = array('Get');
		
		public function index($wallet_id){
			
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
			
			$this->set('wallet_relations', $othersInWallet);
		}	
	
	}
?>