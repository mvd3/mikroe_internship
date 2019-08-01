<div class="actions col_2">
	<h3><?php echo __('Actions'); ?></h3>
<table>		<tr><td><button class="medium orange"><?php echo $this->Html->link(__('Edit Warehouse Address'), array('action' => 'save', $warehouseAddress['WarehouseAddress']['id'])); ?></td></tr></button>
		<tr><td><button class="medium blue"><?php echo $this->Html->link(__('List Warehouse Addresses'), array('action' => 'index')); ?></td></tr></button>
		<tr><td><button class="medium green"><?php echo $this->Html->link(__('New Warehouse Address'), array('action' => 'save')); ?></td></tr></button>
</table></div>
<div class="warehouseAddresses view col_10">
<h2><?php echo __('Warehouse Address'); ?></h2>
	<table cellpadding="0" cellspacing="0" class="striped sortable">
		<!-- Color the active field -->
		<?php
		$ansActive = 'No';
		$colActive = 'red';
		if ($warehouseAddress['WarehouseAddress']['active']) {
			$ansActive = 'Yes';
			$colActive = 'green';
		}
		?>
<tr>		<td><?php echo __('Code'); ?></td>
		<td>
			<?php echo h($warehouseAddress['WarehouseAddress']['code']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Row'); ?></td>
		<td>
			<?php echo h($warehouseAddress['WarehouseAddress']['row']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Shelf'); ?></td>
		<td>
			<?php echo h($warehouseAddress['WarehouseAddress']['shelf']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Bulkhead'); ?></td>
		<td>
			<?php echo h($warehouseAddress['WarehouseAddress']['bulkhead']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Warehouse Location'); ?></td>
		<td>
			<?php echo h($warehouseAddress['WarehouseAddress']['warehouse_location']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Barcode'); ?></td>
		<td>
			<?php echo h($warehouseAddress['WarehouseAddress']['barcode']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Active'); ?></td>
		<td style="color:<?php echo $colActive; ?>">
			<?php echo $ansActive; ?>
			&nbsp;
		</td>
</tr></table>
</div>
