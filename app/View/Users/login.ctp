<div class="usersForm">
<?php echo $this->Session->flash('auth'); ?>
<?php echo $this->Form->create('User'); ?>
	
<?php echo $this->Html->link('Create New', array('controller' => 'users', 'action' => 'add'), array('class' => 'btn btn-primary btn-sm')); ?>
	<fieldset>
        <legend><?php echo __('Please enter your username and password'); ?></legend>
        <?php echo $this->Form->input('username', array('label' => 'Username or Email'));
        echo $this->Form->input('password');
    ?>
    </fieldset>
<?php echo $this->Form->end(__('Login'));?>
</div>