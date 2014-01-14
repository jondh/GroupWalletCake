<?php 
 
App::uses('Component', 'Controller');
App::import('Model', 'WalletRelation');
 
class ValidateComponent extends Component {
 
    public function validateWalletUser($user_id = 0, $wallet_id = -1) {
    	if($user_id > 0 && $wallet_id > -1){
    		$WalletRelationModel = ClassRegistry::init('WalletRelation');
    	
    		$thisdata['WalletRelation']['wallet_id'] = $wallet_id;
			$thisdata['WalletRelation']['user_id'] = $user_id;
			$WalletRelationModel->set($thisdata);
			if ($WalletRelationModel->validates()){
				return true;
			}
		}
		return false;
    }
}    
 
?>
