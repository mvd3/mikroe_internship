<div class="actions col_2">
	<h3><?php echo __('Actions'); ?></h3>
		<button class="medium green"><?php echo $this->Html->link(__('New Move Allowance'), array('action' => 'save')); ?></button>
</div>
<div class="moveAllowances index col_10">
	<h2><?php echo __('Move Allowances'); ?></h2>
	<table cellpadding="0" cellspacing="0" class="striped sortable">
	<thead>
	<tr>
			<th><?php echo $this->Paginator->sort('operator'); ?></th>
			<th><?php echo $this->Paginator->sort('role'); ?></th>
			<th><?php echo $this->Paginator->sort('warehouse_location'); ?></th>
			<th><?php echo $this->Paginator->sort('allowance'); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ($moveAllowances as $moveAllowance): ?>
	<tr>
		<!-- Color the active field -->
		<?php
		$ansAllowance = 'No';
		$colAllowance = 'red';
		if ($moveAllowance['MoveAllowance']['allowance']) {
			$ansAllowance = 'Yes';
			$colAllowance = 'green';
		}
		?>
		<td><?php echo h($moveAllowance['MoveAllowance']['operator']); ?>&nbsp;</td>
		<td><?php echo h($moveAllowance['MoveAllowance']['role']); ?>&nbsp;</td>
		<td><?php echo h($moveAllowance['MoveAllowance']['warehouse_location']); ?>&nbsp;</td>
		<td style="color:<?php echo $colAllowance; ?>"><?php echo $ansAllowance; ?>&nbsp;</td>
		<td class="actions">
			<button class="small orange"><?php echo $this->Html->link(__('Switch'), array('action' => 'change', $moveAllowance['MoveAllowance']['id'])); ?></button>
			<button class="small red"><?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $moveAllowance['MoveAllowance']['id']), array('confirm' => __('Are you sure you want to delete # %s?', $moveAllowance['MoveAllowance']['id']))); ?></button>
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
