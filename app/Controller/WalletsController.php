<?php
	class WalletsController extends AppController { 
	
		public function beforeFilter(){
			parent::beforeFilter();
       		$this->Auth->allow('login', 'add' );
		}
	
		public function index(){
			$this->layout = 'wallets';
			$this->set('wallets', $this->Wallet->find('all', array(
       		 'conditions' => array('Wallet.user_id' => $this->Auth->user('id')))));
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