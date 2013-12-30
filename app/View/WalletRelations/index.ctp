<h1><?php echo $this->Html->link('Add User', array('controller' => 'users', 'action' => 'findUser', $wallet_id), array('class' => 'btn btn-primary btn-lg')); ?></h1>


<?php foreach ($wallet_relations as $walletRelation): ?>  
	<div class="row">
	    <div class="panel panel-primary">
	      <div class="panel-heading">
	        <h3 class="panel-title"><?php echo $walletRelation['UserWR']['firstName'] . " " . $walletRelation['UserWR']['lastName']; ?> </h3>
	      </div>
	      <div class="panel-body">
	        Money You Owe: <?php echo $walletRelation['money']['owe']; ?> 
			<br>
			Money Owed to you: <?php echo $walletRelation['money']['owed']; ?> 
			<br>
			Total: <?php echo $this->Html->link($walletRelation['money']['total'],
				array('controller' => 'Transactions', 'action' => 'getTotalUserWallet', $walletRelation['WalletRelation']['wallet_id'], $walletRelation['WalletRelation']['user_id'])); ?> 
			<br>
			Activity: <?php echo $walletRelation['WalletRelation']['activity']; ?> 
			<br>
			<?php echo $this->Html->link('Add Transaction', 
				array('controller' => 'Transactions', 'action' => 'add', $walletRelation['WalletRelation']['wallet_id'], $walletRelation['WalletRelation']['user_id']), array('class' => 'btn btn-primary btn-lg'));?>
	      </div>
	    </div>
	</div>  	
<?php endforeach; ?>
<?php unset($walletRelation); ?>

<h1><?php echo $this->Html->link('Delete Wallet', array('controller' => 'wallets', 'action' => 'delete', $wallet_id), array('class' => 'btn btn-lg btn-danger')); ?></h1>