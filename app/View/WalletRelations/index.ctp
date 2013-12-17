<?php echo $this->Html->link('Add User', array('controller' => 'users', 'action' => 'findUser', $wallet_id)); ?>

<table>
	<tr>
		<!--<th>ID</th>-->
		<!--<th>WalletID</th>-->
		<th>User</th>
		<th>Owe</th>
		<th>Owed</th>
		<th>Total</th>
		<th>Add Transaction</th>
		<th>Activity</th>
	</tr>
		<?php foreach ($wallet_relations as $walletRelation): ?>  
				<!--<td> <?//php echo $walletRelation['WalletRelation']['wallet_relation_id']; ?> </td>-->
				<!--<td> <?//php echo $walletRelation['WalletRelation']['wallet_id']; ?> </td>-->
				<td> <?php echo $walletRelation['UserWR']['firstName'] . " " . $walletRelation['UserWR']['lastName']; ?> </td>
				<td> <?php echo $walletRelation['money']['owe']; ?> </td>
				<td> <?php echo $walletRelation['money']['owed']; ?> </td>
				<td> <?php echo $this->Html->link($walletRelation['money']['total'],
						array('controller' => 'Transactions', 'action' => 'getTotalUserWallet', $walletRelation['WalletRelation']['wallet_id'], $walletRelation['WalletRelation']['user_id'])); ?> </td>
				<td> 
					<?php echo $this->Html->link('Add', 
						array('controller' => 'Transactions', 'action' => 'add', $walletRelation['WalletRelation']['wallet_id'], $walletRelation['WalletRelation']['user_id']), array('class' => 'btn btn-primary btn-lg'));?>
				</td>
				<td> <?php echo $walletRelation['WalletRelation']['activity']; ?> </td>
			</tr>
			
		<?php endforeach; ?>
		<?php unset($walletRelation); ?>

</table>