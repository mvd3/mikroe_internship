<div class="actions col_2">
	<h3><?php echo __('Actions'); ?></h3>
		<button class="medium green"><?php echo $this->Html->link(__('New Warehouse Address'), array('action' => 'save')); ?></button>
</div>
<div class="warehouseAddresses index col_10">
	<h2><?php echo __('Warehouse Addresses'); ?></h2>
	<table cellpadding="0" cellspacing="0" class="striped sortable">
	<thead>
	<tr>
			<th><?php echo $this->Paginator->sort('code'); ?></th>
			<th><?php echo $this->Paginator->sort('row'); ?></th>
			<th><?php echo $this->Paginator->sort('shelf'); ?></th>
			<th><?php echo $this->Paginator->sort('bulkhead'); ?></th>
			<th><?php echo $this->Paginator->sort('warehouse_location'); ?></th>
			<th><?php echo $this->Paginator->sort('barcode'); ?></th>
			<th><?php echo $this->Paginator->sort('active'); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ($warehouseAddresses as $warehouseAddress): ?>
	<tr>
		<!-- Color the active field -->
		<?php
		$ansActive = 'No';
		$colActive = 'red';
		if ($warehouseAddress['WarehouseAddress']['active']) {
			$ansActive = 'Yes';
			$colActive = 'green';
		}
		?>
		<td><?php echo h($warehouseAddress['WarehouseAddress']['code']); ?>&nbsp;</td>
		<td><?php echo h($warehouseAddress['WarehouseAddress']['row']); ?>&nbsp;</td>
		<td><?php echo h($warehouseAddress['WarehouseAddress']['shelf']); ?>&nbsp;</td>
		<td><?php echo h($warehouseAddress['WarehouseAddress']['bulkhead']); ?>&nbsp;</td>
		<td><?php echo h($warehouseAddress['WarehouseAddress']['warehouse_location']); ?>&nbsp;</td>
		<td><?php echo h($warehouseAddress['WarehouseAddress']['barcode']); ?>&nbsp;</td>
		<td style="color:<?php echo $colActive; ?>"><?php echo $ansActive; ?>&nbsp;</td>
		<td class="actions">
			<button class="small blue"><?php echo $this->Html->link(__('View'), array('action' => 'view', $warehouseAddress['WarehouseAddress']['id'])); ?></button>
			<button class="small orange"><?php echo $this->Html->link(__('Edit'), array('action' => 'save', $warehouseAddress['WarehouseAddress']['id'])); ?></button>
			<button class="small red"><?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $warehouseAddress['WarehouseAddress']['id']), array('confirm' => __('Are you sure you want to delete # %s?', $warehouseAddress['WarehouseAddress']['id']))); ?></button>
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
