<?php 
 
App::uses('Component', 'Controller');
App::import('Model', 'Transaction');
 
class GetComponent extends Component {
 
    public function getOweUserWallet() {
		$amount = $this->Transaction->find('all', array(
			'conditions' => array(
				'wallet_id' => 1,
				'oweUID' => $this->Auth->user('id')
			)
		));
		
		$totalAmount = 0; 
		for($i = 0; $i < count($amount); $i++){
			$totalAmount += $amount[$i]['Transaction']['amount'];
		}
        return $totalAmount;
    }
	
    public function getOwedUserWallet() {
        return 'gey nbot oerf';
    }
	
    public function getOweUser() {
        return 'get user owe';
    }
	
    public function getOwedUser() {
        return 'get user owed';
    }
	
    public function getOweWallet() {
        return 'get wallet owe';
    }
	
    public function getOwedWallet() {
        return 'get wallet owed';
    }
	
    public function getOwe() {
        return 'get total owe';
    }
	
    public function getOwed() {
        return 'get total owed';
    }
}    
 
?>
