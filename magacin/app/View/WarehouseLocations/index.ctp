<div class="actions col_2">
	<h3><?php echo __('Actions'); ?></h3>
		<button class="medium green"><?php echo $this->Html->link(__('New Warehouse Location'), array('action' => 'save')); ?></button>
</div>
<div class="warehouseLocations index col_10">
	<h2><?php echo __('Warehouse Locations'); ?></h2>
	<table cellpadding="0" cellspacing="0" class="striped sortable">
	<thead>
	<tr>
			<th><?php echo $this->Paginator->sort('code'); ?></th>
			<th><?php echo $this->Paginator->sort('name'); ?></th>
			<th><?php echo $this->Paginator->sort('warehouse'); ?></th>
			<th><?php echo $this->Paginator->sort('default'); ?></th>
			<th><?php echo $this->Paginator->sort('active'); ?></th>
			<th><?php echo $this->Paginator->sort('type'); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ($warehouseLocations as $warehouseLocation): ?>
	<tr>
		<!-- Color the active field -->
		<?php
		$ansDefault = 'No';
		$colDefault = 'red';
		$ansActive = 'No';
		$colActive = 'red';
		if ($warehouseLocation['WarehouseLocation']['default']) {
			$ansDefault = 'Yes';
			$colDefault = 'green';
		}
		if ($warehouseLocation['WarehouseLocation']['active']) {
			$ansActive = 'Yes';
			$colActive = 'green';
		}
		?>
		<td><?php echo h($warehouseLocation['WarehouseLocation']['code']); ?>&nbsp;</td>
		<td><?php echo h($warehouseLocation['WarehouseLocation']['name']); ?>&nbsp;</td>
		<td><?php echo h($warehouseLocation['WarehouseLocation']['warehouse']); ?>&nbsp;</td>
		<td style="color:<?php echo $colDefault; ?>"><?php echo $ansDefault; ?>&nbsp;</td>
		<td style="color:<?php echo $colActive; ?>"><?php echo $ansActive; ?>&nbsp;</td>
		<td><?php echo h($activeTypes[array_search($warehouseLocation, $warehouseLocations)]); ?>&nbsp;</td>
		<td class="actions">
			<button class="small blue"><?php echo $this->Html->link(__('View'), array('action' => 'view', $warehouseLocation['WarehouseLocation']['id'])); ?></button>
			<button class="small orange"><?php echo $this->Html->link(__('Edit'), array('action' => 'save', $warehouseLocation['WarehouseLocation']['id'])); ?></button>
			<button class="small red"><?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $warehouseLocation['WarehouseLocation']['id']), array('confirm' => __('Are you sure you want to delete # %s?', $warehouseLocation['WarehouseLocation']['id']))); ?></button>
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
