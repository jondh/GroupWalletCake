<?php ?>

<div class="jumbotron">
  <p class="lead">This is a wallet. In here you can search for and add people to share with or you can choose to leave a wallet. Once someone is added, then how much is owed between you and that person will be displayed. Clicking on the total gives you a log of transactions! </p>
</div>

<h1><?php 
echo $this->Html->link('Add User', array('controller' => 'users', 'action' => 'findUser', $wallet_id), array('class' => 'btn btn-primary btn-lg')); 
echo " ";
echo $this->Html->link('Leave Wallet', array('controller' => 'WalletRelations', 'action' => 'leave', $wallet_id), array('class' => 'btn btn-primary btn-lg')); 
?></h1>


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
			<?php echo $this->Html->link('Add Transaction', 
				array('controller' => 'Transactions', 'action' => 'add', $walletRelation['WalletRelation']['wallet_id'], $walletRelation['WalletRelation']['user_id']), array('class' => 'btn btn-primary btn-lg'));?>
	      </div>
	    </div>
	</div>  	
<?php endforeach; ?>
<?php unset($walletRelation); ?>

<h1><?php echo $this->Html->link('Delete Wallet', array('controller' => 'wallets', 'action' => 'delete', $wallet_id), array('class' => 'btn btn-lg btn-danger')); ?></h1>