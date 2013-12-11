<?php
/* display message saved in session if any */
echo $this->Session->flash();
?>
<div class="addWalletForm">
    <?php echo $this->Form->create('Wallet'); ?>
    <fieldset>
        <legend><?php echo __('Add Wallet'); ?></legend>
        <?php
        echo $this->Form->input('name');
        echo $this->Form->hidden('date');
        ?>
    </fieldset>
    <?php echo $this->Form->end(__('Submit')); ?>
</div>