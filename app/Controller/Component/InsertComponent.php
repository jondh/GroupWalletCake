<?php 
 
App::uses('Component', 'Controller');
App::import('Model', 'WalletRelation');
 
class InsertComponent extends Component {
 
    public function insertWalletRelation($user_id = 0, $wallet_id = -1) {
    	if($user_id > 0 && $wallet_id > -1){
    		$WalletRelationModel = ClassRegistry::init('WalletRelation');
    	
			$thisdata['WalletRelation']['wallet_id'] = $wallet_id;
			$thisdata['WalletRelation']['user_id'] = $user_id;
			$thisdata['WalletRelation']['accept'] = '1';
			if ($WalletRelationModel->save($thisdata)) {
				return true;
			}
			else return false;
		}
		else{
			return false;
		}
    }
}    
 
?>
