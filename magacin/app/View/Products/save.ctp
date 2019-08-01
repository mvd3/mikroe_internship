<div class="actions col_2">
	<h3><?php echo __('Actions'); ?></h3>
	<table>

		<tr><td><button class="medium blue"><?php echo $this->Html->link(__('List Products'), array('action' => 'index')); ?></button></td></tr></table>
</div>
<div class="products form col_10">
<?php echo $this->Form->create('Product'); ?>
	<fieldset>
		<legend><?php echo __('Edit Product'); ?></legend>
	<?php
		echo $this->Form->input('name', array('default' => $name));
		echo $this->Form->input('description', array('default' => $description));
		echo $this->Form->input('weight', array('default' => $weight));
		echo $this->Form->input('measurement_unit', array('options' => $unitOptions, 'selected' => $currentUnit));
		echo $this->Form->input('item_type', array('options' => $typeOptions, 'selected' => $currentType));
		if ($pidCheck) {
			echo $this->Form->input('pid', array('default' => $pid, 'required' => false));
		}
		echo $this->Form->input('hts_number', array('default' => $hts, 'required' => false));
		echo $this->Form->input('tax_group', array('default' => $tax, 'required' => false));
		echo $this->Form->input('product_eccn', array('default' => $eccn, 'required' => false));
		echo $this->Form->input('product_release_date', array('default' => $releaseDate, 'required' => false));
		echo $this->Form->input('for_distributors', array('default' => $distributors));
		echo $this->Form->input('product_status', array('options' => $statusOptions, 'selected' => $currentStatus));
		echo $this->Form->input('service_production', array('default' => $serviceProduction));
		echo $this->Form->input('project', array('default' => $project));
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
