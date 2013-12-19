<?php
/* display message saved in session if any */
echo $this->Session->flash();
?>
<div class="usersForm">
    <?php echo $this->Form->create('User'); ?>
    <fieldset>
        <legend><?php echo __('Edit User'); ?></legend>
        <?php
        echo $this->Form->input('emailEdit', array('label' => 'email'));
        echo $this->Form->input('firstNameEdit', array('label' => 'First Name'));
        echo $this->Form->input('lastNameEdit', array('label' => 'Last Name'));
        echo $this->Form->input('currentPassword', array('type' => 'password', 'label' => 'Current Password'));
        echo $this->Form->input('passwordEdit', array('type' => 'password', 'label' => 'New Password'));
        echo $this->Form->input('passwordConfirmEdit', array('type' => 'password', 'label' => 'Confirm Password'));
        ?>
    </fieldset>
    <?php echo $this->Form->end(__('Submit')); ?>
</div>