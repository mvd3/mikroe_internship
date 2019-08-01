<div class="actions col_2">
	<h3><?php echo __('Actions'); ?></h3>
<table>		<tr><td><button class="medium orange"><?php echo $this->Html->link(__('Edit Consumable'), array('action' => 'save', $consumable['Consumable']['id'])); ?></td></tr></button>
		<tr><td><button class="medium red"><?php echo $this->Form->postLink(__('Delete Consumable'), array('action' => 'delete', $consumable['Consumable']['id']), array('confirm' => __('Are you sure you want to delete # %s?', $consumable['Consumable']['id']))); ?></td></tr></button>
		<tr><td><button class="medium blue"><?php echo $this->Html->link(__('List Consumables'), array('action' => 'index')); ?></td></tr></button>
		<tr><td><button class="medium green"><?php echo $this->Html->link(__('New Consumable'), array('action' => 'save')); ?></td></tr></button>
</table></div>
<div class="consumables view col_10">
<h2><?php echo __('Consumable'); ?></h2>
	<table cellpadding="0" cellspacing="0" class="striped sortable">
<tr>		<td><?php echo __('Code'); ?></td>
		<td>
			<?php echo h($consumable['Item']['code']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Name'); ?></td>
		<td>
			<?php echo h($consumable['Item']['name']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Consumable Status'); ?></td>
		<td>
			<?php echo h($consumable['Consumable']['consumable_status']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Recommended Rating'); ?></td>
		<td>
			<?php echo h($consumable['Consumable']['recommended_rating']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Created'); ?></td>
		<td>
			<?php echo h($consumable['Consumable']['created']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Modified'); ?></td>
		<td>
			<?php echo h($consumable['Consumable']['modified']); ?>
			&nbsp;
		</td>
</tr></table>
</div>
