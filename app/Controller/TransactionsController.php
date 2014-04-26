<?php
	class TransactionsController extends AppController { 
		
		public function beforeFilter(){
			parent::beforeFilter();
       		$this->Auth->allow('addRemote', 'getTransactions');
		}
		
		public $components = array('Validate', 'UserData', 'WalletData', 'AccessToken');
		//public $components = array('UserData');
		
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
					$this->request->data['Transaction']['dateTime'] = NULL;
					
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
		
		public function addRemote(){
			$this->layout = 'ajax';
			if($this->request->is('post')){
				$tokenSuccess = $this->AccessToken->checkAccessTokens($this->request->data['public_token'], $this->request->data['private_token'], $this->request->data['timeStamp']);
				
				if($tokenSuccess == "they good"){
					$trasData['Transaction']['wallet_id'] = $this->request->data['walletID'];
					if($this->request->data['owe'] == '1'){
						$trasData['Transaction']['oweUID'] = $this->request->data['userID'];
						$trasData['Transaction']['owedUID'] = $this->request->data['otherUID'];
					}
					else if($this->request->data['owe'] == '0'){
						$trasData['Transaction']['oweUID'] = $this->request->data['otherUID'];
						$trasData['Transaction']['owedUID'] = $this->request->data['userID'];
					}
					$trasData['Transaction']['amount'] = $this->request->data['amount'];
					$trasData['Transaction']['comments'] = $this->request->data['comments'];
					$trasData['Transaction']['dateTime'] = NULL;
					if ($this->Transaction->save($trasData)) {
					
						$trans = $this->Transaction->find('first', array(
							'conditions' => array(
								'transaction_id' => $this->Transaction->id
							)
						));
						
						if($trans){
							$result['result'] = 'success';
							$result['Transaction'] = $trans['Transaction'];
							return new CakeResponse(array('body' => json_encode($result)));
						}
						else{
							$result['result'] = 'failure in find';
							return new CakeResponse(array('body' => json_encode($result)));
						}
					}
					else{
						$result['result'] = 'failure';
						$result['test'] = $trasData;
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
		
		public function getTransactions(){
			$this->layout = 'ajax';
			if($this->request->is('post')){
				$tokenSuccess = $this->AccessToken->checkAccessTokens($this->request->data['public_token'], $this->request->data['private_token'], $this->request->data['timeStamp']);
					
				if($tokenSuccess == "they good"){
					$wallet_ids = $this->request->data['wallets'];
					
					$records;
					$first = true;
					for($i = 0; $i < count($wallet_ids); $i++){
						if( $wallet_ids[$i] == 0 ){
							$tempRecords = $this->Transaction->find('all', array(
								'conditions' => array(
									'NOT' => array(
										'transaction_id' => (array) $this->request->data['currentRecords']
									),
									'wallet_id' => $wallet_ids[$i],
									'OR' => array(
										'oweUID' => $this->request->data['userID'],
										'owedUID' => $this->request->data['userID']
									)
								)
							));
						}
						else{
							$tempRecords = $this->Transaction->find('all', array(
								'conditions' => array(
									'NOT' => array(
										'transaction_id' => (array) $this->request->data['currentRecords']
									),
									'wallet_id' => $wallet_ids[$i]
								)
							));
						}
						
						if($tempRecords){
							if($first){
								$first = false;
								$recordsSize = 0;
							}
							else{
								$recordsSize = count($records);
							}
							for($j = 0; $j < count($tempRecords); $j++){
								$records[$recordsSize + $j] = $tempRecords[$j];
							}
						}
					}
					
					$result['result'] = 'success';
					if(!$first){
						$result['records'] = $records;
						$result['empty'] = false;
					}
					else{
						$result['empty'] = true;
					}
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
	
		public function getTotalUserWallet($wallet_id, $other_user_id){
		
			if(!$this->Validate->validateWalletUser($other_user_id, $wallet_id)){
				$this->Session->setFlash(__('You may not have access to that wallet'));
				return $this->redirect(array('controller' => 'Wallets', 'action' => 'index'));
			}
		
			$this->set('authUserID', $this->Auth->user('id'));
		
			if($wallet_id > -1){
				$amountOwe = $this->Transaction->find('all', array(
					'conditions' => array(
						'wallet_id' => $wallet_id,
						'oweUID' => $this->Auth->user('id'),
						'owedUID' => $other_user_id,
					)
				));
				
				$amountOwed = $this->Transaction->find('all', array(
					'conditions' => array(
						'wallet_id' => $wallet_id,
						'owedUID' => $this->Auth->user('id'),
						'oweUID' => $other_user_id,
					)
				));
				
				$sizeOwe = count($amountOwe);
				for($i = 0; $i < count($amountOwed); $i++){
					$amountOwe[$i + $sizeOwe] = $amountOwed[$i];
				}
				// http://stackoverflow.com/questions/8121241/sort-array-based-on-the-datetime-in-php
				function cmp($a, $b) {
					if ($a['Transaction']['dateTime'] == $b['Transaction']['dateTime']) {
						return 0;
					}
					return ($a['Transaction']['dateTime'] > $b['Transaction']['dateTime']) ? -1 : 1;
				}

				uasort($amountOwe, 'cmp');
				//////////////////////////////
				$wallet['name'] = $this->WalletData->getWalletName($wallet_id);
				$wallet['id'] = $wallet_id;
				$this->set('oUser', $this->UserData->getUserName($other_user_id));
				$this->set('wallet', $wallet);
				$this->set('transaction', $amountOwe);
				
			}
		}
	
		public function getTotalWalletForMe($wallet_id){
		
			if(!$this->Validate->validateWalletUser($this->Auth->user('id'), $wallet_id)){
				$this->Session->setFlash(__('You may not have access to that wallet'));
				return $this->redirect(array('controller' => 'Wallets', 'action' => 'index'));
			}
		
			$this->set('authUserID', $this->Auth->user('id'));
		
			if($wallet_id){
				$amountOwe = $this->Transaction->find('all', array(
					'joins' => array(
						array(
							'table' => 'users',
							'alias' => 'User',
							'type'  => 'INNER',
							'conditions' => array(
								'User.id = Transaction.owedUID'
							)
						)
					),
					'conditions' => array(
						'wallet_id' => $wallet_id,
						'oweUID' => $this->Auth->user('id')
					),
					'fields' => array(
						'Transaction.*', 'User.firstName', 'User.lastName', 'User.username'
					)
				));
				
				$amountOwed = $this->Transaction->find('all', array(
					'joins' => array(
						array(
							'table' => 'users',
							'alias' => 'User',
							'type'  => 'INNER',
							'conditions' => array(
								'User.id = Transaction.oweUID'
							)
						)
					),
					'conditions' => array(
						'wallet_id' => $wallet_id,
						'owedUID' => $this->Auth->user('id')
					),
					'fields' => array(
						'Transaction.*', 'User.firstName', 'User.lastName', 'User.username'
					)
				));
				
				
				$sizeOwe = count($amountOwe);
				for($i = 0; $i < count($amountOwed); $i++){
					$amountOwe[$i + $sizeOwe] = $amountOwed[$i];
				}
				
				// http://stackoverflow.com/questions/8121241/sort-array-based-on-the-datetime-in-php
				function cmp($a, $b) {
					if ($a['Transaction']['dateTime'] == $b['Transaction']['dateTime']) {
						return 0;
					}
					return ($a['Transaction']['dateTime'] > $b['Transaction']['dateTime']) ? -1 : 1;
				}

				uasort($amountOwe, 'cmp');
				//////////////////////////////
				
				$wallet['name'] = $this->WalletData->getWalletName($wallet_id);
				$wallet['id'] = $wallet_id;
				$this->set('wallet', $wallet);
				
				$this->set('transaction', $amountOwe);
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
						'wallet_id' => $wallet_id
					)
				));
				
				// needs optimizing //
				for($i = 0; $i < count($amount); $i++){
					$oweUID = $amount[$i]['Transaction']['oweUID'];
					$amount[$i]['oweUID'] = $this->UserData->getUserName($oweUID);
					
					$owedUID = $amount[$i]['Transaction']['owedUID'];
					$amount[$i]['owedUID'] = $this->UserData->getUserName($owedUID);
				}
				
				// http://stackoverflow.com/questions/8121241/sort-array-based-on-the-datetime-in-php
				function cmp($a, $b) {
					if ($a['Transaction']['dateTime'] == $b['Transaction']['dateTime']) {
						return 0;
					}
					return ($a['Transaction']['dateTime'] > $b['Transaction']['dateTime']) ? -1 : 1;
				}

				uasort($amount, 'cmp');
				//////////////////////////////
				
				$wallet['name'] = $this->WalletData->getWalletName($wallet_id);
				$wallet['id'] = $wallet_id;
				$this->set('wallet', $wallet);
				
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
				$amountOwe = $this->Transaction->find('all', array(
					'conditions' => array(
						'oweUID' => $this->Auth->user('id'),
						'owedUID' => $other_user_id,
					)
				));
				
				$amountOwed = $this->Transaction->find('all', array(
					'conditions' => array(
						'owedUID' => $this->Auth->user('id'),
						'oweUID' => $other_user_id,
					)
				));
				
				$sizeOwe = count($amountOwe);
				for($i = 0; $i < count($amountOwed); $i++){
					$amountOwe[$i + $sizeOwe] = $amountOwed[$i];
				}
				// http://stackoverflow.com/questions/8121241/sort-array-based-on-the-datetime-in-php
				function cmp($a, $b) {
					if ($a['Transaction']['dateTime'] == $b['Transaction']['dateTime']) {
						return 0;
					}
					return ($a['Transaction']['dateTime'] > $b['Transaction']['dateTime']) ? -1 : 1;
				}

				uasort($amountOwe, 'cmp');
				//////////////////////////////
				
				$this->set('oUser', $this->UserData->getUserName($other_user_id));
				
				$this->set('transaction', $amountOwe);
			}
		
		}
	}
?>