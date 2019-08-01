<div class="actions col_2">
	<h3><?php echo __('Actions'); ?></h3>
		<button class="medium green"><?php echo $this->Html->link(__('New Measurement Unit'), array('action' => 'save')); ?></button>
</div>
<div class="measurementUnits index col_10">
	<h2><?php echo __('Measurement Units'); ?></h2>
	<table cellpadding="0" cellspacing="0" class="striped sortable">
	<thead>
	<tr>
			<th><?php echo $this->Paginator->sort('name'); ?></th>
			<th><?php echo $this->Paginator->sort('symbol'); ?></th>
			<th><?php echo $this->Paginator->sort('active'); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ($measurementUnits as $measurementUnit): ?>
	<tr>
		<!-- Color the active field -->
		<?php
		$ans = 'No';
		$col = 'red';
		if ($measurementUnit['MeasurementUnit']['active']) {
			$ans = 'Yes';
			$col = 'green';
		}
		?>
		<td><?php echo h($measurementUnit['MeasurementUnit']['name']); ?>&nbsp;</td>
		<td><?php echo h($measurementUnit['MeasurementUnit']['symbol']); ?>&nbsp;</td>
		<td style="color:<?php echo $col; ?>"><?php echo $ans; ?>&nbsp;</td>
		<td class="actions">
			<button class="small blue"><?php echo $this->Html->link(__('View'), array('action' => 'view', $measurementUnit['MeasurementUnit']['id'])); ?></button>
			<button class="small orange"><?php echo $this->Html->link(__('Edit'), array('action' => 'save', $measurementUnit['MeasurementUnit']['id'])); ?></button>
			<button class="small red"><?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $measurementUnit['MeasurementUnit']['id']), array('confirm' => __('Are you sure you want to delete # %s?', $measurementUnit['MeasurementUnit']['id']))); ?></button>
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
		<button class="small pink">
	<?php
		echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
	?>
</button>
<?php
echo $this->Paginator->numbers(array('separator' => ''));
?>
<button class="small pink">
<?php
echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
?>
</button>
	</div>
</div>
