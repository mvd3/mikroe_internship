<div class="actions col_2">
	<h3><?php echo __('Actions'); ?></h3>
	<table>

		<tr><td><button class="medium blue"><?php echo $this->Html->link(__('List Item Types'), array('action' => 'index')); ?></button></td></tr></table>
</div>
<div class="itemTypes form col_10">
<?php echo $this->Form->create('ItemType'); ?>
	<fieldset>
		<legend><?php echo __('Edit Item Type'); ?></legend>
	<?php
		echo $this->Form->input('code', array('default' => $currentCode));
		echo $this->Form->input('name', array('default' => $currentName));
		echo $this->Form->input('class', array('options' => $classOptions, 'selected' => $currentClass));
		echo $this->Form->input('tangible', array('default' => $currentTangible));
		echo $this->Form->input('active', array('default' => $currentActive));
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
