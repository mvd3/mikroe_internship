<div class="actions col_2">
	<h3><?php echo __('Actions'); ?></h3>
	<table>
		<tr><td><button class="medium blue"><?php echo $this->Html->link(__('List Measurement Units'), array('action' => 'index')); ?></button></td></tr>
	</table>
</div>
<div class="measurementUnits form col_10">
<?php echo $this->Form->create('MeasurementUnit'); ?>
	<fieldset>
		<legend><?php echo __('Edit Measurement Unit'); ?></legend>
	<?php
		echo $this->Form->input('name', array('default' => $name));
		echo $this->Form->input('symbol', array('default' => $symbol));
		echo $this->Form->input('active', array('default' => $active));
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
