<div class="actions col_2">
	<h3><?php echo __('Actions'); ?></h3>
		<table>
		<tr><td><button class="medium orange"><?php echo $this->Html->link(__('Edit Measurement Unit'), array('action' => 'save', $measurementUnit['MeasurementUnit']['id'])); ?></button></td></tr>
		<tr><td><button class="medium red"><?php echo $this->Form->postLink(__('Delete Measurement Unit'), array('action' => 'delete', $measurementUnit['MeasurementUnit']['id']), array('confirm' => __('Are you sure you want to delete # %s?', $measurementUnit['MeasurementUnit']['id']))); ?></button></td></tr>
		<tr><td><button class="medium blue"><?php echo $this->Html->link(__('List Measurement Units'), array('action' => 'index')); ?></button></td></tr>
		<tr><td><button class="medium green"><?php echo $this->Html->link(__('New Measurement Unit'), array('action' => 'save')); ?></button></td></tr>
	</table>
</div>
<div class="measurementUnits view col_10">
<h2><?php echo __('Measurement Unit'); ?></h2>
	<table cellpadding="0" cellspacing="0" class="striped sortable">
	<tr>
		<td><?php echo __('Name'); ?></td>
		<td>
			<?php echo h($measurementUnit['MeasurementUnit']['name']); ?>
			&nbsp;
		</td>
	</tr>
	<tr>
		<td><?php echo __('Symbol'); ?></td>
		<td>
			<?php echo h($measurementUnit['MeasurementUnit']['symbol']); ?>
			&nbsp;
		</td>
	</tr>
	<tr>
		<td><?php echo __('Active'); ?></td>
		<!-- Color the active field -->
		<?php
		$ans = 'No';
		$col = 'red';
		if ($measurementUnit['MeasurementUnit']['active']) {
			$ans = 'Yes';
			$col = 'green';
		}
		?>
		<td style="color:<?php echo $col; ?>">
			<?php echo $ans; ?>
			&nbsp;
		</td>
	</tr>
	<tr>
		<td><?php echo __('Created'); ?></td>
		<td>
			<?php echo h($measurementUnit['MeasurementUnit']['created']); ?>
			&nbsp;
		</td>
	</tr>
	<tr>
		<td><?php echo __('Modified'); ?></td>
		<td>
			<?php echo h($measurementUnit['MeasurementUnit']['modified']); ?>
			&nbsp;
		</td>
	</tr>
	<table cellpadding="0" cellspacing="0" class="striped sortable">
</div>
