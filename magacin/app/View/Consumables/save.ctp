<div class="actions col_2">
	<h3><?php echo __('Actions'); ?></h3>
	<table>

		<tr><td><button class="medium blue"><?php echo $this->Html->link(__('List Consumables'), array('action' => 'index')); ?></button></td></tr></table>
</div>
<div class="consumables form col_10">
<?php echo $this->Form->create('Consumable'); ?>
	<fieldset>
		<legend><?php echo __('Add Consumable'); ?></legend>
	<?php
		echo $this->Form->input('name', array('default' => $name));
		echo $this->Form->input('description', array('default' => $description));
		echo $this->Form->input('weight', array('default' => $weight));
		echo $this->Form->input('measurement_unit', array('options' => $unitOptions, 'selected' => $currentUnit));
		echo $this->Form->input('item_type', array('options' => $typeOptions, 'selected' => $currentType));
		echo $this->Form->input('consumable_status', array('options' => $statusOptions, 'selected' => $currentStatus));
		echo $this->Form->input('recommended_rating', array('options' => $recommendationOptions, 'selected' => $currentRecommendation));
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
