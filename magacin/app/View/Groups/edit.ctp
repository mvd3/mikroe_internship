<div class="actions col_2">
	<h3><?php echo __('Actions'); ?></h3>
	<table>

		<tr><td><button class="medium red"><?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('Group.id')), array('confirm' => __('Are you sure you want to delete # %s?', $this->Form->value('Group.id')))); ?></button></td></tr></li>
		<tr><td><button class="medium blue"><?php echo $this->Html->link(__('List Groups'), array('action' => 'index')); ?></button></td></tr>
</table>
</div>
<div class="groups form col_10">
<?php echo $this->Form->create('Group'); ?>
	<fieldset>
		<legend><?php echo __('Edit Group'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('name');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
