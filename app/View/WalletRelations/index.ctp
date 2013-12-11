
<table>
	<tr>
		<th>ID</th>
		<th>WalletID</th>
		<th>User</th>
		<th>Activity</th>
	</tr>
		<?php foreach ($wallet_relations as $walletRelation): ?> 
			<tr>
				<td><?php echo $walletRelation['WalletRelation']['wallet_relation_id']; ?></td>
				<td><?php echo $walletRelation['WalletRelation']['wallet_id']; ?></td>
				<td>
					<?php echo $walletRelation['UserWR']['firstName']; ?>
					<?php echo $walletRelation['UserWR']['lastName']; ?>
				</td>
				<td><?php echo $walletRelation['WalletRelation']['activity']; ?></td>
			</tr>
		<?php endforeach; ?>
		<?php unset($walletRelation); ?>
</table>