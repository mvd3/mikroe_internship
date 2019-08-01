<div class="actions col_2">
	<h3><?php echo __('Actions'); ?></h3>
<table>		<tr><td><button class="medium orange"><?php echo $this->Html->link(__('Edit Service Product'), array('action' => 'save', $serviceProduct['ServiceProduct']['id'])); ?></td></tr></button>
		<tr><td><button class="medium red"><?php echo $this->Form->postLink(__('Delete Service Product'), array('action' => 'delete', $serviceProduct['ServiceProduct']['id']), array('confirm' => __('Are you sure you want to delete # %s?', $serviceProduct['ServiceProduct']['id']))); ?></td></tr></button>
		<tr><td><button class="medium blue"><?php echo $this->Html->link(__('List Service Products'), array('action' => 'index')); ?></td></tr></button>
		<tr><td><button class="medium green"><?php echo $this->Html->link(__('New Service Product'), array('action' => 'save')); ?></td></tr></button>
</table></div>
<div class="serviceProducts view col_10">
<h2><?php echo __('Service Product'); ?></h2>
<!-- Color the active field -->
<?php
$ans = 'No';
$col = 'red';
if ($serviceProduct['ServiceProduct']['for_distributors']) {
	$ans = 'Yes';
	$col = 'green';
}
?>
	<table cellpadding="0" cellspacing="0" class="striped sortable">
<tr>		<td><?php echo __('Code'); ?></td>
		<td>
			<?php echo h($serviceProduct['Item']['code']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Name'); ?></td>
		<td>
			<?php echo h($serviceProduct['Item']['name']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Pid'); ?></td>
		<td>
			<?php echo h($serviceProduct['ServiceProduct']['pid']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Hts Number'); ?></td>
		<td>
			<?php echo h($serviceProduct['ServiceProduct']['hts_number']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Tax Group'); ?></td>
		<td>
			<?php echo h($serviceProduct['ServiceProduct']['tax_group']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Eccn'); ?></td>
		<td>
			<?php echo h($serviceProduct['ServiceProduct']['eccn']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Release Date'); ?></td>
		<td>
			<?php echo h($serviceProduct['ServiceProduct']['release_date']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('For Distributors'); ?></td>
		<td style="color:<?php echo $col; ?>">
			<?php echo $ans; ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Service Status'); ?></td>
		<td>
			<?php echo h($serviceProduct['ServiceProduct']['service_status']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Project'); ?></td>
		<td>
			<?php echo h($serviceProduct['ServiceProduct']['project']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Created'); ?></td>
		<td>
			<?php echo h($serviceProduct['ServiceProduct']['created']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Modified'); ?></td>
		<td>
			<?php echo h($serviceProduct['ServiceProduct']['modified']); ?>
			&nbsp;
		</td>
</tr></table>
</div>
