<div class="action col_12">
	<table>
	<tr><td><?php echo $this->Form->create('Search', array('url' => array('controller' => 'Items', 'action' => 'index', 'op' => 'search'))); ?></td>
	<td><?php echo $this->Form->input('query', array('label' => 'Search term')); ?></td>
	<td><?php echo $this->Form->input('item_type', array('options' => $typesOptions)) ?></td>
	<td><?php echo $this->Form->end(__('Search')); ?></td></tr>
	</table>
</div>
<div class="actions col_2">
	<h3><?php echo __('Actions'); ?></h3>
		<!--<button class="medium green"><?php echo $this->Html->link(__('New Item'), array('action' => 'add')); ?></button>-->
</div>
<div class="items index col_10">
	<h2><?php echo __('Items'); ?></h2>
	<table cellpadding="0" cellspacing="0" class="striped sortable">
	<thead>
	<tr>
			<th><?php echo $this->Paginator->sort('code'); ?></th>
			<th><?php echo $this->Paginator->sort('name'); ?></th>
			<th><?php echo $this->Paginator->sort('description'); ?></th>
			<th><?php echo $this->Paginator->sort('weight'); ?></th>
			<th><?php echo $this->Paginator->sort('measurement_unit'); ?></th>
			<th><?php echo $this->Paginator->sort('item_type'); ?></th>
			<th><?php echo $this->Paginator->sort('deleted'); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ($items as $item): ?>
	<tr>
		<!-- Color the active field -->
		<?php
		$ans = 'No';
		$col = 'red';
		if ($item['Item']['deleted']) {
			$ans = 'Yes';
			$col = 'green';
		}
		?>
		<td><?php echo h($item['Item']['code']); ?>&nbsp;</td>
		<td><?php echo h($item['Item']['name']); ?>&nbsp;</td>
		<td><?php echo h($item['Item']['description']); ?>&nbsp;</td>
		<td><?php echo h($item['Item']['weight']); ?>&nbsp;</td>
		<td><?php echo h($item['MeasurementUnit']['name']); ?>&nbsp;</td>
		<td><?php echo h($item['ItemType']['name']); ?>&nbsp;</td>
		<td style="color:<?php echo $col; ?>"><?php echo $ans; ?>&nbsp;</td>
		<td class="actions">
			<button class="small blue"><?php echo $this->Html->link(__('View'), array('action' => 'view', $item['Item']['id'])); ?></button>
			<!--<button class="small orange"><?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $item['Item']['id'])); ?></button>-->
			<button class="small red"><?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $item['Item']['id']), array('confirm' => __('Are you sure you want to delete # %s?', $item['Item']['id']))); ?></button>
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
