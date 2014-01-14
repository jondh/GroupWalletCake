<table>
	<tr>
		<th>Amount</th>
		<th>Comments</th>
	</tr>
		<?php foreach ($transaction as $Transactions): ?>  
			<tr>
				<td> <?php 
					if($Transactions['Transaction']['oweUID'] == $authUserID){
				 		echo '-' . $Transactions['Transaction']['amount']; 
				 	}
				 	else{
				 		echo $Transactions['Transaction']['amount'];
				 	} ?>
				</td>
				<td> <?php echo $Transactions['Transaction']['comments']; ?> </td>
			</tr>
			
		<?php endforeach; ?>
		<?php unset($walletRelation); ?>
</table>