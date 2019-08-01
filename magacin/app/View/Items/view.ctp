<div class="actions col_2">
	<h3><?php echo __('Actions'); ?></h3>
<table>		<!--<tr><td><button class="medium orange"><?php echo $this->Html->link(__('Edit Item'), array('action' => 'edit', $item['Item']['id'])); ?></td></tr></button>-->
		<tr><td><button class="medium red"><?php echo $this->Form->postLink(__('Delete Item'), array('action' => 'delete', $item['Item']['id']), array('confirm' => __('Are you sure you want to delete # %s?', $item['Item']['id']))); ?></td></tr></button>
		<tr><td><button class="medium blue"><?php echo $this->Html->link(__('List Items'), array('action' => 'index')); ?></td></tr></button>
		<!--<tr><td><button class="medium green"><?php echo $this->Html->link(__('New Item'), array('action' => 'add')); ?></td></tr></button>-->
</table></div>
<div class="items view col_10">
<h2><?php echo __('Item'); ?></h2>
	<table cellpadding="0" cellspacing="0" class="striped sortable">
<tr>		<td><?php echo __('Id'); ?></td>
		<td>
			<?php echo h($item['Item']['id']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Code'); ?></td>
		<td>
			<?php echo h($item['Item']['code']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Name'); ?></td>
		<td>
			<?php echo h($item['Item']['name']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Description'); ?></td>
		<td>
			<?php echo h($item['Item']['description']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Weight'); ?></td>
		<td>
			<?php echo h($item['Item']['weight']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Measurement Unit'); ?></td>
		<td>
			<?php echo h($item['Item']['measurement_unit']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Item Type'); ?></td>
		<td>
			<?php echo h($item['Item']['item_type']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Deleted'); ?></td>
		<td>
			<?php echo h($item['Item']['deleted']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Created'); ?></td>
		<td>
			<?php echo h($item['Item']['created']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Modified'); ?></td>
		<td>
			<?php echo h($item['Item']['modified']); ?>
			&nbsp;
		</td>
</tr></table>
</div>
