<table>
	<tr>
		<th>Amount</th>
		<th>Comments</th>
		<th>Date (CST)</th>
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
				<td> <?php 
						echo $this->Time->format($Transactions['Transaction']['dateTime'], '%B %e, %Y %l:%M %p');
					?>
				</td>
			</tr>
			
		<?php endforeach; ?>
		<?php unset($walletRelation); ?>
</table>