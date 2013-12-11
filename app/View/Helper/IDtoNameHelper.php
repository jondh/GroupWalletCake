<?php
	/* /app/View/Helper/IDtoNameHelper.php */
	App::uses('AppHelper', 'View/Helper');

	class IDtoNameHelper extends AppHelper {
	    public function user($userID) {
	        return 'Bill Smith';
	    }
	}
?>