<div class="actions col_2">
	<h3><?php echo __('Actions'); ?></h3>
	<table>
		<tr><td><button class="medium green"><?php echo $this->Html->link(__('New Service Product'), array('action' => 'save')); ?></button></td></tr>
		<tr><td><button class="medium blue"><?php echo $this->Html->link(__('Download as Excel file'), array('action' => 'outputExcel')); ?></button></td></tr>
		<tr><td><button class="medium pink"><?php echo $this->Html->link(__('Download as PDF'), array('action' => 'outputPDF')); ?></button></td></tr>
		<tr><td>
      <?php echo $this->Form->create('uploadFile', array('type' => 'file', 'enctype' => 'multipart/form-data', 'url' => array('controller' => 'ServiceProducts', 'action' => 'inputExcel'))); ?>
	    <?php echo $this->Form->input('file_path', array('type' => 'file', 'label' => '')); ?>
	    <?php echo $this->Form->end('Import from excel'); ?>
		</td></tr>
	</table>
</div>
<div class="serviceProducts index col_10">
	<h2><?php echo __('Service Products'); ?></h2>
	<table cellpadding="0" cellspacing="0" class="striped sortable">
	<thead>
	<tr>
			<th><?php echo $this->Paginator->sort('code'); ?></th>
			<th><?php echo $this->Paginator->sort('name'); ?></th>
			<th><?php echo $this->Paginator->sort('pid'); ?></th>
			<th><?php echo $this->Paginator->sort('hts_number'); ?></th>
			<th><?php echo $this->Paginator->sort('tax_group'); ?></th>
			<th><?php echo $this->Paginator->sort('eccn'); ?></th>
			<th><?php echo $this->Paginator->sort('release_date'); ?></th>
			<th><?php echo $this->Paginator->sort('for_distributors'); ?></th>
			<th><?php echo $this->Paginator->sort('service_status'); ?></th>
			<th><?php echo $this->Paginator->sort('project'); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ($serviceProducts as $serviceProduct): ?>
	<tr>
		<!-- Color the active field -->
		<?php
		$ans = 'No';
		$col = 'red';
		if ($serviceProduct['ServiceProduct']['for_distributors']) {
			$ans = 'Yes';
			$col = 'green';
		}
		?>
		<td><?php echo h($serviceProduct['Item']['code']); ?>&nbsp;</td>
		<td><?php echo h($serviceProduct['Item']['name']); ?>&nbsp;</td>
		<td><?php echo h($serviceProduct['ServiceProduct']['pid']); ?>&nbsp;</td>
		<td><?php echo h($serviceProduct['ServiceProduct']['hts_number']); ?>&nbsp;</td>
		<td><?php echo h($serviceProduct['ServiceProduct']['tax_group']); ?>&nbsp;</td>
		<td><?php echo h($serviceProduct['ServiceProduct']['eccn']); ?>&nbsp;</td>
		<td><?php echo h($serviceProduct['ServiceProduct']['release_date']); ?>&nbsp;</td>
		<td style="color:<?php echo $col; ?>"><?php echo $ans; ?>&nbsp;</td>
		<td><?php echo h($serviceProduct['ServiceProduct']['service_status']); ?>&nbsp;</td>
		<td><?php echo h($serviceProduct['ServiceProduct']['project']); ?>&nbsp;</td>
		<td class="actions">
			<button class="small blue"><?php echo $this->Html->link(__('View'), array('action' => 'view', $serviceProduct['ServiceProduct']['id'])); ?></button>
			<button class="small orange"><?php echo $this->Html->link(__('Edit'), array('action' => 'save', $serviceProduct['ServiceProduct']['id'])); ?></button>
			<button class="small red"><?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $serviceProduct['ServiceProduct']['id']), array('confirm' => __('Are you sure you want to delete # %s?', $serviceProduct['ServiceProduct']['id']))); ?></button>
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
