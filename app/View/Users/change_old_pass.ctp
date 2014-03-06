<?php
/* display message saved in session if any */
echo $this->Session->flash();
?>
<div class="usersForm">
    <?php echo $this->Form->create('User'); ?>
    <fieldset>
        <legend><?php echo __('Enter new password (can be the same as old)'); ?></legend>
        <?php
        echo $this->Form->input('password');
        echo $this->Form->input('passwordConfirm', array('type' => 'password'));
        ?>
    </fieldset>
    <?php echo $this->Form->end(__('Submit')); ?>
</div>