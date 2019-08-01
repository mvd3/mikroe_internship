<div class="actions col_2">
	<h3><?php echo __('Actions'); ?></h3>
	<table>

		<tr><td><button class="medium blue"><?php echo $this->Html->link(__('List Warehouse Locations'), array('action' => 'index')); ?></button></td></tr></table>
</div>
<div class="warehouseLocations form col_10">
<?php echo $this->Form->create('WarehouseLocation'); ?>
	<fieldset>
		<legend><?php echo __('Edit Warehouse Location'); ?></legend>
	<?php
		echo $this->Form->input('code', array('default' => $code));
		echo $this->Form->input('name', array('default' => $name));
		echo $this->Form->input('warehouse', array('options' => $warehouseOptions, 'selected' => $warehouseDefault));
		echo $this->Form->input('description', array('default' => $description));
		echo $this->Form->input('default', array('default' => $default));
		echo $this->Form->input('active', array('default' => $active));
	?>
	<table>
		<?php foreach ($classes as $class): ?>
				<tr><td>
					<?php echo $this->Form->input($class, array('type' => 'checkbox', 'default' => ${$class . 'Default'})); ?>
				</td></tr>
		<?php endforeach; ?>
	</table>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
