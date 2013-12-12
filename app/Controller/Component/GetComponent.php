<?php 
 
App::uses('Component', 'Controller');
App::import('Model', 'Transaction');
 
class GetComponent extends Component {
 
    public function getOweUserWallet($user_id = 0, $other_user_id = 0, $wallet_id = 0) {
    	if($user_id > 0 && $other_user_id > 0 && $wallet_id > 0){
    		$TransactionModel = ClassRegistry::init('Transaction');
    	
			$amount = $TransactionModel->find('all', array(
				'conditions' => array(
					'wallet_id' => $wallet_id,
					'oweUID' => $user_id,
					'owedUID' => $other_user_id
				)
			));
		
			$totalAmount = 0; 
			for($i = 0; $i < count($amount); $i++){
				$totalAmount += $amount[$i]['Transaction']['amount'];
			}
      	  	return $totalAmount;
		}
    }
	
    public function getOwedUserWallet($user_id = 0, $other_user_id = 0, $wallet_id = 0) {
        if($user_id > 0 && $other_user_id > 0 && $wallet_id > 0){
    		$TransactionModel = ClassRegistry::init('Transaction');
    	
			$amount = $TransactionModel->find('all', array(
				'conditions' => array(
					'wallet_id' => $wallet_id,
					'oweUID' => $other_user_id,
					'owedUID' => $user_id
				)
			));
		
			$totalAmount = 0; 
			for($i = 0; $i < count($amount); $i++){
				$totalAmount += $amount[$i]['Transaction']['amount'];
			}
      	  	return $totalAmount;
		}
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
