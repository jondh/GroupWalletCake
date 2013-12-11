
<h1><?php echo $this->Html->link('New Wallet', array('controller' => 'wallets', 'action' => 'add')); ?></h1>
<h1>Blog posts</h1>

<table>
	<tr>
		<th>ID</th>
		<th>Created</th>
	</tr>
		<?php foreach ($wallets as $wallet): ?> 
			<tr>
				<td><?php echo $wallet['Wallet']['wallet_id']; ?></td>
				<td><?php echo $this->Html->link($wallet['Wallet']['name'], 
					array('controller' => 'WalletRelations', 'action' => 'index', $wallet['Wallet']['wallet_id'])); ?></td>
				<td><?php echo $wallet['Wallet']['date']; ?></td>
				<td><?php echo $wallet['Wallet']['user_id']; ?></td>
			</tr>
		<?php endforeach; ?>
		<?php unset($wallet); ?>
</table>