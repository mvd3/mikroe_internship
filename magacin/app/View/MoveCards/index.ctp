<div class="actions col_2">
	<h3><?php echo __('Actions'); ?></h3>
		<?php if($role=='Operators') { ?>
		<button class="medium green"><?php echo $this->Html->link(__('New Move Card'), array('action' => 'add')); ?></button>
	<?php } ?>
	<h3><?php echo h($role); ?></h3>
</div>
<div class="moveCards index col_10">
	<h2><?php echo __('Move Cards'); ?></h2>
	<table cellpadding="0" cellspacing="0" class="striped sortable">
	<thead>
	<tr>
			<th><?php echo $this->Paginator->sort('code'); ?></th>
			<th><?php echo $this->Paginator->sort('creator'); ?></th>
			<th><?php echo $this->Paginator->sort('move_from'); ?></th>
			<th><?php echo $this->Paginator->sort('move_to'); ?></th>
			<th><?php echo $this->Paginator->sort('issued_by'); ?></th>
			<th><?php echo $this->Paginator->sort('status'); ?></th>
			<th><?php echo $this->Paginator->sort('type'); ?></th>
			<th><?php echo $this->Paginator->sort('recieved_by'); ?></th>
			<th><?php echo $this->Paginator->sort('work_order'); ?></th>
			<th><?php echo $this->Paginator->sort('created'); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ($moveCards as $moveCard): ?>
	<tr>
		<td><?php echo h($moveCard['MoveCard']['code']); ?>&nbsp;</td>
		<td><?php echo h($moveCard['Creator']['name']); ?>&nbsp;</td>
		<td><?php echo h($moveCard['From']['code']); ?>&nbsp;</td>
		<td><?php echo h($moveCard['To']['code']); ?>&nbsp;</td>
		<td><?php echo h($moveCard['IssuedBy']['name']); ?>&nbsp;</td>
		<td><?php echo h($moveCard['MoveCard']['status']); ?>&nbsp;</td>
		<td><?php echo h($moveCard['MoveCard']['type']); ?>&nbsp;</td>
		<td><?php echo h($moveCard['RecievedBy']['name']); ?>&nbsp;</td>
		<td><?php echo h($moveCard['MoveCard']['work_order']); ?>&nbsp;</td>
		<td><?php echo h($moveCard['MoveCard']['created']); ?>&nbsp;</td>
		<td class="actions">
			<?php if ($moveCard['MoveCard']['status']==$openStatus && $isOperator) { ?>
			<button class="small blue"><?php echo $this->Html->link(__('Items'), array('action' => 'view', $moveCard['MoveCard']['id'])); ?></button>
			<button class="small orange"><?php echo $this->Html->link(__('Send'), array('action' => 'send', $moveCard['MoveCard']['id'])); ?></button>
			<button class="small red"><?php echo $this->Form->postLink(__('Cancel'), array('action' => 'delete', $moveCard['MoveCard']['id']), array('confirm' => __('Are you sure you want to cancel # %s?', $moveCard['MoveCard']['id']))); ?></button>
			<?php } ?>
			<button class="small blue"><?php echo $this->Html->link(__('Display'), array('action' => 'display', $moveCard['MoveCard']['id'])); ?></button>
			<?php if ($moveCard['MoveCard']['status']==$sentStatus && $preparePass && $isClerk) { ?>
				<button class="small orange"><?php echo $this->Html->link(__('Prepare'), array('action' => 'prepare', $moveCard['MoveCard']['id'])); ?></button>
			<?php } ?>
			<?php if ($moveCard['MoveCard']['status']==$readyStatus && $receivePass && $isClerk) { ?>
				<button class="small orange"><?php echo $this->Html->link(__('Receive'), array('action' => 'receive', $moveCard['MoveCard']['id'])); ?></button>
			<?php } ?>
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
