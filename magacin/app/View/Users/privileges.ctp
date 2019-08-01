<div class="actions col_2">
	<h3><?php echo __('Actions'); ?></h3>
	<table>
		<tr><td><button class="medium blue"><?php echo $this->Html->link(__('List Users'), array('action' => 'index')); ?></button></td></tr>
</table>
</div>
<div class="users form col_10">
<?php echo $this->Form->create('Privileges'); ?>
	<fieldset>
		<legend><?php echo __('Edit Privileges'); ?></legend>
	<?php foreach($acos as $aco): ?>
		&nbsp;<div style="padding-left:<?php echo $aco['depth']*5 ?>em"><?php echo $this->Form->input($aco['alias'], array('type' => 'checkbox', 'default' => $aco['check'])) ?></div>
	<?php endforeach; ?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
