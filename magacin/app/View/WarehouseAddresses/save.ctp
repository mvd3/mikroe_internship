<div class="actions col_2">
	<h3><?php echo __('Actions'); ?></h3>
	<table>
		<tr><td><button class="medium red"><?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('WarehouseAddress.id')), array('confirm' => __('Are you sure you want to delete # %s?', $this->Form->value('WarehouseAddress.id')))); ?></button></td></tr></li>
		<tr><td><button class="medium blue"><?php echo $this->Html->link(__('List Warehouse Addresses'), array('action' => 'index')); ?></button></td></tr></table>
</div>
<div class="warehouseAddresses form col_10">
<?php echo $this->Form->create('WarehouseAddress'); ?>
	<fieldset>
		<legend><?php echo __('Edit Warehouse Address'); ?></legend>
	<?php
		echo $this->Form->input('row', array('default' => $rowDefault));
		echo $this->Form->input('shelf', array('default' => $shelfDefault));
		echo $this->Form->input('bulkhead', array('default' => $bulkheadDefault));
		echo $this->Form->input('warehouse_location', array('options' => $locationOptions, 'selected' => $locationDefault));
		echo $this->Form->input('active', array('default' => $activeDefault));
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
