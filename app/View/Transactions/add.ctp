<?php
/* display message saved in session if any */
echo $this->Session->flash();
?>
<div class="transactionAddForm">
    <?php echo $this->Form->create('Transaction'); ?>
    <fieldset>
        <legend><?php echo __('Add Transaction'); ?></legend>
        <?php
        echo $this->Form->radio('selection', array('owe', 'owed'));
		echo $this->Form->input('amount');
		echo $this->Form->input('comments');
        ?>
    </fieldset>
    <?php echo $this->Form->end(__('Submit')); ?>
</div>