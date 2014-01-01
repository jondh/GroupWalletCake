<?php ?>
<h1><?php echo $this->Html->link('New Wallet', array('controller' => 'wallets', 'action' => 'add'), array('class' => 'btn btn-primary btn-lg')); ?></h1>

<?php
	if(count($wallets) == 0){
?>
	You are not a part of any wallets. Somebody may add you to their wallet or you may create one here. Wallets are used to keep track of money between people in the wallet.
<?php	
	}
	else{
?>
		<?php foreach ($wallets as $wallet): /* have a delete button followed by an are you sure popup */ ?> 
			<div class="row">
				<div class="panel panel-primary">
				  <div class="panel-heading">
					<h3 class="panel-title"><?php echo $this->Html->link($wallet['Wallet']['name'], 
					array('controller' => 'WalletRelations', 'action' => 'index', $wallet['Wallet']['id'])); ?></h3>
				  </div>
				  <div class="panel-body">
					Money You Owe: <?php echo $wallet['money']['owe']; ?>
					<br>
					Money Owed to you: <?php echo $wallet['money']['owed']; ?> 
					<br>
					Total: <?php echo $this->Html->link($wallet['money']['total'], array('controller' => 'Transactions', 'action' => 'getTotalWallet', $wallet['Wallet']['id'])); ?>
					<br>
					Date Created: <?php echo $wallet['Wallet']['date']; ?>
					<br>
					Created by: <?php echo $wallet['User']['firstName']; ?>
				  </div>
				</div>
			</div>  
			<?php unset($wallet); ?>
		<?php endforeach; 
	}
?>


