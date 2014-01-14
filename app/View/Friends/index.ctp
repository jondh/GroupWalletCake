<?php ?>

<div class="jumbotron">
  <p class="lead">Friends! Yey! </p>
</div>

<h1><?php 
echo $this->Html->link('Find Friends', array('controller' => 'users', 'action' => 'findUser', 0), array('class' => 'btn btn-primary btn-lg')); 
?></h1>


<?php foreach ($friends as $Friend): ?>  
	<div class="row">
	    <div class="panel panel-primary">
	      <div class="panel-heading">
	        <h3 class="panel-title"><?php 
	        	if(($Friend['User']['firstName'] == "") && ($Friend['User']['lastName'] == "")){
	        		echo $Friend['User']['username'];
	        	}
	        	else{
	        		echo $Friend['User']['firstName'] . " " . $Friend['User']['lastName']; 	
	        	}
	        ?> </h3>
	      </div>
	      <div class="panel-body">
	        Money You Owe: <?php echo $Friend['money']['owe']; ?> 
	        Money You Owe Total: <?php echo $Friend['moneyT']['owe']; ?>
			<br>
			Money Owed to You: <?php echo $Friend['money']['owed']; ?> 	
			Money Owed to You Total: <?php echo $Friend['moneyT']['owed']; ?>
			<br>
			Total Outside of Wallets: <?php echo $this->Html->link($Friend['money']['total'],
				array('controller' => 'Transactions', 'action' => 'getTotalUserWallet', 0, $Friend['User']['id'])); ?> 	
			Total: <?php echo $this->Html->link($Friend['moneyT']['total'],
				array('controller' => 'Transactions', 'action' => 'getTotalUser', $Friend['User']['id'])); ?> 
			<br>
			<?php echo $this->Html->link('Add Transaction', 
				array('controller' => 'Transactions', 'action' => 'add', 0, $Friend['User']['id'], 2), array('class' => 'btn btn-primary btn-lg'));?>
	      </div>
	    </div>
	</div>  
<?php unset($Friend); ?>	
<?php endforeach; ?>

