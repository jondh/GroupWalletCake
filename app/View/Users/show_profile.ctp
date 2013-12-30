<h1><?php echo $user['User']['username']; ?></h1>
<?php 
if($self == '1')
{
?>
	<div class="row">
	    <div class="panel panel-primary">
	      <div class="panel-heading">
	        <h3 class="panel-title"><img src="http://lorempixel.com/140/140/people/140x140/" class="img-rounded"> <?php echo $user['User']['firstName']; ?>  <?php echo $user['User']['lastName']; ?></h3>
	      </div>
	      <div class="panel-body">
	      </div>
	    </div>
	</div>  
<?php
echo $this->Html->link('Edit Profile', array('controller' => 'users', 'action' => 'edit'), array('class' => 'btn btn-lg btn-default')); 
}
else
{
?>
<div class="row">
    <div class="panel panel-primary">
      <div class="panel-heading">
        <h3 class="panel-title"><img src="http://lorempixel.com/140/140/people/140x140/" class="img-rounded"> <?php echo $user['User']['firstName']; ?>  <?php echo $user['User']['lastName']; ?></h3>
      </div>
      <div class="panel-body">
      </div>
    </div>
</div>  
<?php
}
?>

