<div class="actions col_2">
	<h3><?php echo __('Actions'); ?></h3>
	<table>
		<tr><td><button class="medium green"><?php echo $this->Html->link(__('New Kit'), array('action' => 'save')); ?></button></td></tr>
		<tr><td><button class="medium blue"><?php echo $this->Html->link(__('Download as Excel file'), array('action' => 'outputExcel')); ?></button></td></tr>
		<tr><td><button class="medium pink"><?php echo $this->Html->link(__('Download as PDF'), array('action' => 'outputPDF')); ?></button></td></tr>
		<tr><td>
      <?php echo $this->Form->create('uploadFile', array('type' => 'file', 'enctype' => 'multipart/form-data', 'url' => array('controller' => 'Kits', 'action' => 'inputExcel'))); ?>
	    <?php echo $this->Form->input('file_path', array('type' => 'file', 'label' => '')); ?>
	    <?php echo $this->Form->end('Import from excel'); ?>
		</td></tr>
	</table>
</div>
<div class="kits index col_10">
	<h2><?php echo __('Kits'); ?></h2>
	<table cellpadding="0" cellspacing="0" class="striped sortable">
	<thead>
	<tr>
			<th><?php echo $this->Paginator->sort('code'); ?></th>
			<th><?php echo $this->Paginator->sort('name'); ?></th>
			<th><?php echo $this->Paginator->sort('pid'); ?></th>
			<th><?php echo $this->Paginator->sort('hts_number'); ?></th>
			<th><?php echo $this->Paginator->sort('tax_group'); ?></th>
			<th><?php echo $this->Paginator->sort('eccn'); ?></th>
			<th><?php echo $this->Paginator->sort('kit_release_date'); ?></th>
			<th><?php echo $this->Paginator->sort('for_distributors'); ?></th>
			<th><?php echo $this->Paginator->sort('hide_kid_content'); ?></th>
			<th><?php echo $this->Paginator->sort('kit_status'); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ($kits as $kit): ?>
	<tr>
		<!-- Color the active field -->
		<?php
		$ansDistributors = 'No';
		$colDistributors = 'red';
		$ansContent = 'No';
		$colContent = 'red';
		if ($kit['Kit']['for_distributors']) {
			$ansDistributors = 'Yes';
			$colDistributors = 'green';
		}
		if ($kit['Kit']['hide_kid_content']) {
			$ansContent = 'Yes';
			$colContent = 'green';
		}
		?>
		<td><?php echo h($kit['Item']['code']); ?>&nbsp;</td>
		<td><?php echo h($kit['Item']['name']); ?>&nbsp;</td>
		<td><?php echo h($kit['Kit']['pid']); ?>&nbsp;</td>
		<td><?php echo h($kit['Kit']['hts_number']); ?>&nbsp;</td>
		<td><?php echo h($kit['Kit']['tax_group']); ?>&nbsp;</td>
		<td><?php echo h($kit['Kit']['eccn']); ?>&nbsp;</td>
		<td><?php echo h($kit['Kit']['kit_release_date']); ?>&nbsp;</td>
		<td style="color:<?php echo $colDistributors; ?>"><?php echo $ansDistributors; ?>&nbsp;</td>
		<td style="color:<?php echo $colContent; ?>"><?php echo $ansContent; ?>&nbsp;</td>
		<td><?php echo h($kit['Kit']['kit_status']); ?>&nbsp;</td>
		<td class="actions">
			<button class="small blue"><?php echo $this->Html->link(__('View'), array('action' => 'view', $kit['Kit']['id'])); ?></button>
			<button class="small orange"><?php echo $this->Html->link(__('Edit'), array('action' => 'save', $kit['Kit']['id'])); ?></button>
			<button class="small red"><?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $kit['Kit']['id']), array('confirm' => __('Are you sure you want to delete # %s?', $kit['Kit']['id']))); ?></button>
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
