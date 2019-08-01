<!--
<?php
	$item = ClassRegistry::init('ItemType');
	$schema = $item->schema();
	var_dump($schema);
	foreach ($schema as $s) {
		if ($s['type']=='boolean') {

			echo array_search($s, $schema);
		}
	}
	// echo $item->schema();
	 ?>
 -->
<div class="actions col_2">
	<h3><?php echo __('Actions'); ?></h3>
		<button class="medium green"><?php echo $this->Html->link(__('New Item Type'), array('action' => 'save')); ?></button>
</div>
<div class="itemTypes index col_10">
	<h2><?php echo __('Item Types'); ?></h2>
	<table cellpadding="0" cellspacing="0" class="striped sortable">
	<thead>
	<tr>
			<th><?php echo $this->Paginator->sort('code'); ?></th>
			<th><?php echo $this->Paginator->sort('name'); ?></th>
			<th><?php echo $this->Paginator->sort('class'); ?></th>
			<th><?php echo $this->Paginator->sort('tangible'); ?></th>
			<th><?php echo $this->Paginator->sort('active'); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ($itemTypes as $itemType): ?>
	<tr>
		<!-- Color the active field -->
		<?php
		$ansTangible = 'No';
		$colTangible = 'red';
		$ansActive = 'No';
		$colActive = 'red';
		if ($itemType['ItemType']['tangible']) {
			$ansTangible = 'Yes';
			$colTangible = 'green';
		}
		if ($itemType['ItemType']['active']) {
			$ansActive = 'Yes';
			$colActive = 'green';
		}
		?>
		<td><?php echo h($itemType['ItemType']['code']); ?>&nbsp;</td>
		<td><?php echo h($itemType['ItemType']['name']); ?>&nbsp;</td>
		<td><?php echo h($itemType['ItemType']['class']); ?>&nbsp;</td>
		<td style="color:<?php echo $colTangible; ?>"><?php echo $ansTangible; ?>&nbsp;</td>
		<td style="color:<?php echo $colActive; ?>"><?php echo $ansActive; ?>&nbsp;</td>
		<td class="actions">
			<button class="small blue"><?php echo $this->Html->link(__('View'), array('action' => 'view', $itemType['ItemType']['id'])); ?></button>
			<button class="small orange"><?php echo $this->Html->link(__('Edit'), array('action' => 'save', $itemType['ItemType']['id'])); ?></button>
			<button class="small red"><?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $itemType['ItemType']['id']), array('confirm' => __('Are you sure you want to delete # %s?', $itemType['ItemType']['id']))); ?></button>
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
