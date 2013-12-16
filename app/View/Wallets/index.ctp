<?php ?>
<h1><?php echo $this->Html->link('New Wallet', array('controller' => 'wallets', 'action' => 'add')); ?></h1>
<h1>Blog posts</h1>

<table>
	<tr>
		<th>ID</th>
		<th>Name</th>
		<th>Owe</th>
		<th>Owed</th>
		<th>Total</th>
		<th>Date</th>
		<th>CreatedBy</th>
	</tr>
		<?php foreach ($wallets as $wallet): ?> 
			<tr>
				<td><?php echo $wallet['Wallet']['wallet_id']; ?></td>
				<td><?php echo $this->Html->link($wallet['Wallet']['name'], 
					array('controller' => 'WalletRelations', 'action' => 'index', $wallet['Wallet']['wallet_id'])); ?></td>
				<td> <?php echo $wallet['money']['owe']; ?> </td>
				<td> <?php echo $wallet['money']['owed']; ?> </td>
				<td> <?php echo $this->Html->link($wallet['money']['total'],
						array('controller' => 'Transactions', 'action' => 'getTotalWallet', $wallet['Wallet']['wallet_id'])); ?> </td>
				<td><?php echo $wallet['Wallet']['date']; ?></td>
				<td><?php echo $wallet['Wallet']['user_id']; ?></td>
			</tr>
		<?php endforeach; ?>
		<?php unset($wallet); ?>
</table>