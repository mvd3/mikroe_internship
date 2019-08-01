<div class="actions col_2">
	<h3><?php echo __('Actions'); ?></h3>
	<table>

		<tr><td><button class="medium red"><?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('MoveCard.id')), array('confirm' => __('Are you sure you want to delete # %s?', $this->Form->value('MoveCard.id')))); ?></button></td></tr></li>
		<tr><td><button class="medium blue"><?php echo $this->Html->link(__('List Move Cards'), array('action' => 'index')); ?></button></td></tr></table>
</div>
<div class="moveCards form col_10">
<?php echo $this->Form->create('MoveCard'); ?>
	<fieldset>
		<legend><?php echo __('Edit Move Card'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('code');
		echo $this->Form->input('creator');
		echo $this->Form->input('move_from');
		echo $this->Form->input('move_to');
		echo $this->Form->input('issued_by');
		echo $this->Form->input('status');
		echo $this->Form->input('type');
		echo $this->Form->input('recieved_by');
		echo $this->Form->input('work_order');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
