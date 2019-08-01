<div class="actions col_2">
	<h3><?php echo __('Actions'); ?></h3>
<table>		<tr><td><button class="medium orange"><?php echo $this->Html->link(__('Edit Material'), array('action' => 'save', $material['Material']['id'])); ?></td></tr></button>
		<tr><td><button class="medium red"><?php echo $this->Form->postLink(__('Delete Material'), array('action' => 'delete', $material['Material']['id']), array('confirm' => __('Are you sure you want to delete # %s?', $material['Material']['id']))); ?></td></tr></button>
		<tr><td><button class="medium blue"><?php echo $this->Html->link(__('List Materials'), array('action' => 'index')); ?></td></tr></button>
		<tr><td><button class="medium green"><?php echo $this->Html->link(__('New Material'), array('action' => 'save')); ?></td></tr></button>
</table></div>
<div class="materials view col_10">
<h2><?php echo __('Material'); ?></h2>
<!-- Color the active field -->
<?php
$ans = 'No';
$col = 'red';
if ($material['Material']['service_production']) {
	$ans = 'Yes';
	$col = 'green';
}
?>
	<table cellpadding="0" cellspacing="0" class="striped sortable">
<tr>		<td><?php echo __('Code'); ?></td>
		<td>
			<?php echo h($material['Item']['code']); ?>
			&nbsp;
		</td>
</tr>
<tr>		<td><?php echo __('Name'); ?></td>
		<td>
			<?php echo h($material['Item']['name']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Material Status'); ?></td>
		<td>
			<?php echo h($material['Material']['material_status']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Service Production'); ?></td>
		<td style="color:<?php echo $col; ?>">
			<?php echo $ans; ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Recommended Rating'); ?></td>
		<td>
			<?php echo h($material['Material']['recommended_rating']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Created'); ?></td>
		<td>
			<?php echo h($material['Material']['created']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Modified'); ?></td>
		<td>
			<?php echo h($material['Material']['modified']); ?>
			&nbsp;
		</td>
</tr></table>
</div>
