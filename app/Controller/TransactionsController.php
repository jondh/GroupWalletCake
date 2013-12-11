<?php
	class TransactionsController extends AppController { 
		
		public function index(){
		
		}	
		
		public function add($wallet_id, $other_id){
			if($this->request->is('post')){
				$this->Transaction->create();
				 if ($this->Transaction->save($this->request->data)) {
               		 $this->Session->setFlash(__('The user has been saved'));
        			 return $this->redirect(array('controller' => 'WalletRelations', 'action' => 'index', $wallet_id);
           		 }
           		 $this->Session->setFlash(__('The user could not be saved. Please, try again'));
			}
		}
	
	}
?>