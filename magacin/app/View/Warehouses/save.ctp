<div class="actions col_2">
	<h3><?php echo __('Actions'); ?></h3>
	<table>

		<tr><td><button class="medium blue"><?php echo $this->Html->link(__('List Warehouses'), array('action' => 'index')); ?></button></td></tr></table>
</div>
<div class="warehouses form col_10">
<?php echo $this->Form->create('Warehouse'); ?>
	<fieldset>
		<legend><?php echo __('Edit Warehouse'); ?></legend>
	<?php
		echo $this->Form->input('name', array('default' => $name));
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
