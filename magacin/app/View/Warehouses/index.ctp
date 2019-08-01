<div class="actions col_2">
	<h3><?php echo __('Actions'); ?></h3>
		<button class="medium green"><?php echo $this->Html->link(__('New Warehouse'), array('action' => 'save')); ?></button>
</div>
<div class="warehouses index col_10">
	<h2><?php echo __('Warehouses'); ?></h2>
	<table cellpadding="0" cellspacing="0" class="striped sortable">
	<thead>
	<tr>
			<th><?php echo $this->Paginator->sort('name'); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ($warehouses as $warehouse): ?>
	<tr>
		<td><?php echo h($warehouse['Warehouse']['name']); ?>&nbsp;</td>
		<td class="actions">
			<button class="small orange"><?php echo $this->Html->link(__('Edit'), array('action' => 'save', $warehouse['Warehouse']['id'])); ?></button>
			<button class="small red"><?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $warehouse['Warehouse']['id']), array('confirm' => __('Are you sure you want to delete # %s?', $warehouse['Warehouse']['id']))); ?></button>
		</td>
	</tr>
<?php endforeach; ?>
	</tbody>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter(array(
		'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
	));
	?>	</p>
	<div class="paging">
	<button class="small pink"><?php
		echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
	?></button>
	<?php
		echo $this->Paginator->numbers(array('separator' => ''));
	?>
	<button class="small pink"><?php
		echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
	?></button>
	</div>
</div>
