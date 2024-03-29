<?php
class WalletRelation extends AppModel {

	var $validate = array(
        "wallet_idUnique"=>array(
        	"unique"=>array(
            	"rule"=>array(
            		"checkUnique", array("wallet_id", "user_id")
            	),
                "message"=>"Person already in wallet"
            )
        ),
        "wallet_id"=>array(
        	"belongsToUser"=>array(
            	"rule"=>array(
            		"checkBelongsTo", array ("wallet_id", "user_id")
            	)
            )
        )
        
       
    );

	function checkUnique($data, $fields) {
    	if (!is_array($fields)) {
        	$fields = array($fields);
        }
        foreach($fields as $key) {
            $tmp[$key] = $this->data[$this->name][$key];
        }
        if (isset($this->data[$this->name][$this->primaryKey])) {
            $tmp[$this->primaryKey] = 
 				"<>".$this->data[$this->name][$this->primaryKey];
        }
        return $this->isUnique($tmp, false);
    }
 
 	function checkBelongsTo($wallet_id, $user_id) {
 		if($this->data['WalletRelation']['wallet_id'] == 0){
 		
			$friends = $this->find('first', array(
				'joins' =>array(
					array(
						'table' => 'friends',
						'alias' => 'Friend',
						'type' => 'RIGHT',
						'conditions' => array(
							'WalletRelation.user_id = Friend.user_id_1'
						)
					)
				),
				'conditions' => array(
					'OR' => array(
						'AND' => array(
							'Friend.user_id_1' => $this->data['WalletRelation']['user_id'],
							'Friend.user_id_2' => CakeSession::read("Auth.User.id")
						),
						'AND' => array(
							'Friend.user_id_1' => CakeSession::read("Auth.User.id"),
							'Friend.user_id_2' => $this->data['WalletRelation']['user_id']
						)
					)
				),
				'fields' => array(
					'Friend.id'
				)
			));
		
			if(!empty($friends)){
				return true;
			}
			else{
				return false;
			}
 		}
 	
 		$createdWallets = $this->find('first', array(
        	'joins' => array(
				array(
					'table' => 'wallets',
					'alias' => 'Wallet',
					'type' => 'RIGHT',
					'conditions' => array(
						'WalletRelation.user_id = Wallet.user_id'
					)
				)
			),
            'conditions' => array(
                'Wallet.user_id' => CakeSession::read("Auth.User.id"),
                'Wallet.id' => $wallet_id
            ),
            'fields' => array(
            	'Wallet.user_id', 'Wallet.id'
            )
        ));
        
        if(!empty($createdWallets)){
        	return true;
        }
        
        $joinedWallets = $this->find('first', array(
        	'conditions' => array(
        		'WalletRelation.user_id' => CakeSession::read("Auth.User.id"),
                'WalletRelation.wallet_id' => $wallet_id
        	),
        	'fields' => array(
            	'WalletRelation.user_id', 'WalletRelation.wallet_id'
            )
        ));
        
        if(!empty($joinedWallets)){
        	return true;
        }
        
 		return false;
 	} 
 
}
?>