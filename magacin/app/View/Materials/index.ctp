<div class="actions col_2">
	<h3><?php echo __('Actions'); ?></h3>
	<table>
		<tr><td><button class="medium green"><?php echo $this->Html->link(__('New Material'), array('action' => 'save')); ?></button></td></tr>
		<tr><td><button class="medium blue"><?php echo $this->Html->link(__('Download as Excel file'), array('action' => 'outputExcel')); ?></button></td></tr>
		<tr><td><button class="medium pink"><?php echo $this->Html->link(__('Download as PDF'), array('action' => 'outputPDF')); ?></button></td></tr>
		<tr><td>
      <?php echo $this->Form->create('uploadFile', array('type' => 'file', 'enctype' => 'multipart/form-data', 'url' => array('controller' => 'Materials', 'action' => 'inputExcel'))); ?>
	    <?php echo $this->Form->input('file_path', array('type' => 'file', 'label' => '')); ?>
	    <?php echo $this->Form->end('Import from excel'); ?>
		</td></tr>
	</table>
</div>
<div class="materials index col_10">
	<h2><?php echo __('Materials'); ?></h2>
	<table cellpadding="0" cellspacing="0" class="striped sortable">
	<thead>
	<tr>
		<th><?php echo $this->Paginator->sort('code'); ?></th>
		<th><?php echo $this->Paginator->sort('name'); ?></th>
		<th><?php echo $this->Paginator->sort('material_status'); ?></th>
		<th><?php echo $this->Paginator->sort('service_production'); ?></th>
		<th><?php echo $this->Paginator->sort('recommended_rating'); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ($materials as $material): ?>
	<tr>
		<!-- Color the active field -->
		<?php
		$ans = 'No';
		$col = 'red';
		if ($material['Material']['service_production']) {
			$ans = 'Yes';
			$col = 'green';
		}
		?>
		<td><?php echo h($material['Item']['code']); ?>&nbsp;</td>
		<td><?php echo h($material['Item']['name']); ?>&nbsp;</td>
		<td><?php echo h($material['Material']['material_status']); ?>&nbsp;</td>
		<td style="color:<?php echo $col; ?>"><?php echo $ans; ?>&nbsp;</td>
		<td><?php echo h($material['Material']['recommended_rating']); ?>&nbsp;</td>
		<td class="actions">
			<button class="small blue"><?php echo $this->Html->link(__('View'), array('action' => 'view', $material['Material']['id'])); ?></button>
			<button class="small orange"><?php echo $this->Html->link(__('Edit'), array('action' => 'save', $material['Material']['id'])); ?></button>
			<button class="small red"><?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $material['Material']['id']), array('confirm' => __('Are you sure you want to delete # %s?', $material['Material']['id']))); ?></button>
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
