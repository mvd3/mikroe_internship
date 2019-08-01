<div class="actions col_2">
	<h3><?php echo __('Actions'); ?></h3>
	<table>
		<tr><td><button class="medium blue"><?php echo $this->Html->link(__('List Article Addresses'), array('action' => 'index')); ?></button></td></tr></table>
</div>
<div class="articleAddresses form col_10">
<?php echo $this->Form->create(false, array('url' => array('controller' => 'ArticleAddresses', 'action' => 'add', $addrID))); ?>
	<fieldset>
		<legend><?php echo __('Add Items'); ?></legend>
	<?php
		echo $this->Form->input('item', array('options' => $itemOptions));
		echo $this->Form->input('quantity');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
