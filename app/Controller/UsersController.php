<?php
	class UsersController extends AppController { 
	
		public function beforeFilter(){
			parent::beforeFilter();
       		$this->Auth->allow('login','add');
		}
	
		public function index(){
		
		}
	
		public function login(){
			if ($this->request->is('post')) {
        		/* login and redirect to url set in app controller */
           		if ($this->Auth->login()) {
        			return $this->redirect($this->Auth->redirect());
          	  	}
          	  	$this->Session->setFlash(__('Invalid username or password, try again'));
       		}
		}
		
		public function logout() {
         	/* logout and redirect to url set in app controller */
       	 	return $this->redirect($this->Auth->logout());
    	}
		
		public function add(){
			if($this->request->is('post')){
				$this->User->create();
				 if ($this->User->save($this->request->data)) {
               		 $this->Session->setFlash(__('The user has been saved'));
               	 	 if ($this->Auth->login()) {
        			 	return $this->redirect($this->Auth->redirect());
          	  		 }
           		 }
           		 $this->Session->setFlash(__('The user could not be saved. Please, try again'));
			}
		}
		
		public function findUser(){
			
		}
	}
?>