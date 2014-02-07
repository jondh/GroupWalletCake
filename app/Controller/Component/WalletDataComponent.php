<?php 
 
App::uses('Component', 'Controller');
App::import('Model', 'Wallet');

class WalletDataComponent extends Component {
	
	/*
	 *	From the wallet id, this function returns the wallet name
	 */
	public function getWalletName($wallet_id = -1) {
    	if($wallet_id > -1){
    		$WalletModel = ClassRegistry::init('Wallet');
    	
			$walletResult = $WalletModel->find('first', array(
				'conditions' => array(
					'id' => $wallet_id,
				),
				'fields' => array(
					'Wallet.name'
				)
			));
			
			$wallet = $walletResult['Wallet']['name'];
			
      	  	return $wallet;
		}
    }
}    
 
?>