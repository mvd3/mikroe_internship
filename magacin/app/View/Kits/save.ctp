<div class="actions col_2">
	<h3><?php echo __('Actions'); ?></h3>
	<table>

		<tr><td><button class="medium blue"><?php echo $this->Html->link(__('List Kits'), array('action' => 'index')); ?></button></td></tr></table>
</div>
<div class="kits form col_10">
<?php echo $this->Form->create('Kit'); ?>
	<fieldset>
		<legend><?php echo __('Edit Kit'); ?></legend>
	<?php
		echo $this->Form->input('name', array('default' => $name));
		echo $this->Form->input('description', array('default' => $description));
		echo $this->Form->input('weight', array('default' => $weight));
		echo $this->Form->input('measurement_unit', array('options' => $unitOptions, 'selected' => $currentUnit));
		echo $this->Form->input('item_type', array('options' => $typeOptions, 'selected' => $currentType));
		if ($pidCheck) {
			echo $this->Form->input('pid', array('default' => $pid, 'required' => false));
		}
		echo $this->Form->input('hts_number', array('default' => $currentHts, 'required' => false));
		echo $this->Form->input('tax_group', array('default' => $currentTax, 'required' => false));
		echo $this->Form->input('eccn', array('default' => $currentEccn, 'required' => false));
		echo $this->Form->input('kit_release_date', array('default' => $currentDate, 'required' => false));
		echo $this->Form->input('for_distributors', array('default' => $currentDistributors));
		echo $this->Form->input('hide_kid_content', array('default' => $currentContent));
		echo $this->Form->input('kit_status', array('options' => $statusOptions, 'selected' => $currentStatus));
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
