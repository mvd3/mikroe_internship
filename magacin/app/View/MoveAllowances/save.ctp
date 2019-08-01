<div class="actions col_2">
	<h3><?php echo __('Actions'); ?></h3>
	<table>
		<tr><td><button class="medium blue"><?php echo $this->Html->link(__('List Move Allowances'), array('action' => 'index')); ?></button></td></tr></table>
</div>
<div class="moveAllowances form col_10">
<?php echo $this->Form->create('MoveAllowance'); ?>
	<fieldset>
		<legend><?php echo __('Edit Move Allowance'); ?></legend>
	<?php
		echo $this->Form->input('operator', array('options' => $operatorOptions));
		echo $this->Form->input('warehouse_location', array('options' => $locationOptions, 'selected' => $locationSelected));
		echo $this->Form->input('allowance', array('default' => $allowanceDefault));
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
