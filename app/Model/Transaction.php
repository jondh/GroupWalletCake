<?php
class Transaction extends AppModel {
	public $validate = array(
		'amount' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty'),
				'message' => "What's the point?",
				'allowEmpty' => false
			),
			'numeric' => array(
				'rule' => 'moneyFormat',
				'message' => 'Not a valid money format'
			)
		)
		
	);
	
	public function moneyFormat($check){
		$value = array_values($check);
		$value = $value[0];
		
		return preg_match('/^\d+(\.(\d{2})?)?$/', $value);
	}
}
?>