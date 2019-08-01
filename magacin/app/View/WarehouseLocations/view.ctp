<div class="actions col_2">
	<h3><?php echo __('Actions'); ?></h3>
<table>		<tr><td><button class="medium orange"><?php echo $this->Html->link(__('Edit Warehouse Location'), array('action' => 'save', $warehouseLocation['WarehouseLocation']['id'])); ?></td></tr></button>
		<tr><td><button class="medium blue"><?php echo $this->Html->link(__('List Warehouse Locations'), array('action' => 'index')); ?></td></tr></button>
		<tr><td><button class="medium green"><?php echo $this->Html->link(__('New Warehouse Location'), array('action' => 'save')); ?></td></tr></button>
</table></div>
<div class="warehouseLocations view col_10">
<h2><?php echo __('Warehouse Location'); ?></h2>
	<table cellpadding="0" cellspacing="0" class="striped sortable">
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
<tr>		<td><?php echo __('Code'); ?></td>
		<td>
			<?php echo h($warehouseLocation['WarehouseLocation']['code']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Name'); ?></td>
		<td>
			<?php echo h($warehouseLocation['WarehouseLocation']['name']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Warehouse'); ?></td>
		<td>
			<?php echo h($warehouseLocation['WarehouseLocation']['warehouse']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Description'); ?></td>
		<td>
			<?php echo h($warehouseLocation['WarehouseLocation']['description']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Default'); ?></td>
		<td style="color:<?php echo $colDefault; ?>">
			<?php echo $ansDefault; ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Active'); ?></td>
	<td style="color:<?php echo $colActive; ?>">
		<?php echo $ansActive; ?>
		&nbsp;
	</td>
</tr><tr>		<td><?php echo __('Created'); ?></td>
		<td>
			<?php echo h($warehouseLocation['WarehouseLocation']['created']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Modified'); ?></td>
		<td>
			<?php echo h($warehouseLocation['WarehouseLocation']['modified']); ?>
			&nbsp;
		</td>
</tr></table>
</div>
