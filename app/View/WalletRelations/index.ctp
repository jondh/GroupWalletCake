<?php echo $oweAmount; ?>

<table>
	<tr>
		<th>ID</th>
		<th>WalletID</th>
		<th>User</th>
		<th>Add Transaction</th>
		<th>Activity</th>
	</tr>
		<?php foreach ($wallet_relations as $walletRelation): ?> 
			<?php if($walletRelation['WalletRelation']['user_id'] != $authUserID){
			echo "<tr>
				<td>"; echo $walletRelation['WalletRelation']['wallet_relation_id']; echo"</td>
				<td>"; echo $walletRelation['WalletRelation']['wallet_id']; echo"</td>
				<td>";
					echo $walletRelation['UserWR']['firstName'] . " " . $walletRelation['UserWR']['lastName'];
				echo "</td>
				<td>";
					echo $this->Html->link('Add', 
						array('controller' => 'Transactions', 'action' => 'add', $walletRelation['WalletRelation']['wallet_id'], $walletRelation['WalletRelation']['user_id']));
				echo"</td>
				<td>"; echo $walletRelation['WalletRelation']['activity']; echo"</td>
			</tr>";
			}	
			?>
		<?php endforeach; ?>
		<?php unset($walletRelation); ?>
</table>