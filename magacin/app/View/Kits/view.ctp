<div class="actions col_2">
	<h3><?php echo __('Actions'); ?></h3>
<table>		<tr><td><button class="medium orange"><?php echo $this->Html->link(__('Edit Kit'), array('action' => 'save', $kit['Kit']['id'])); ?></td></tr></button>
		<tr><td><button class="medium red"><?php echo $this->Form->postLink(__('Delete Kit'), array('action' => 'delete', $kit['Kit']['id']), array('confirm' => __('Are you sure you want to delete # %s?', $kit['Kit']['id']))); ?></td></tr></button>
		<tr><td><button class="medium blue"><?php echo $this->Html->link(__('List Kits'), array('action' => 'index')); ?></td></tr></button>
		<tr><td><button class="medium green"><?php echo $this->Html->link(__('New Kit'), array('action' => 'save')); ?></td></tr></button>
</table></div>
<div class="kits view col_10">
<h2><?php echo __('Kit'); ?></h2>
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
	<table cellpadding="0" cellspacing="0" class="striped sortable">
<tr>		<td><?php echo __('Code'); ?></td>
		<td>
			<?php echo h($kit['Item']['code']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Name'); ?></td>
		<td>
			<?php echo h($kit['Item']['name']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Pid'); ?></td>
		<td>
			<?php echo h($kit['Kit']['pid']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Hts Number'); ?></td>
		<td>
			<?php echo h($kit['Kit']['hts_number']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Tax Group'); ?></td>
		<td>
			<?php echo h($kit['Kit']['tax_group']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Eccn'); ?></td>
		<td>
			<?php echo h($kit['Kit']['eccn']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Kit Release Date'); ?></td>
		<td>
			<?php echo h($kit['Kit']['kit_release_date']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('For Distributors'); ?></td>
	<td style="color:<?php echo $colDistributors; ?>">
		<?php echo $ansDistributors; ?>
		&nbsp;
	</td>
</tr><tr>		<td><?php echo __('Hide Kid Content'); ?></td>
		<td style="color:<?php echo $colContent; ?>">
			<?php echo $ansContent; ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Kit Status'); ?></td>
		<td>
			<?php echo h($kit['Kit']['kit_status']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Created'); ?></td>
		<td>
			<?php echo h($kit['Kit']['created']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Modified'); ?></td>
		<td>
			<?php echo h($kit['Kit']['modified']); ?>
			&nbsp;
		</td>
</tr></table>
</div>
