<div class="actions col_2">
	<h3><?php echo __('Actions'); ?></h3>
<table>		<tr><td><button class="medium orange"><?php echo $this->Html->link(__('Edit Good'), array('action' => 'save', $good['Good']['id'])); ?></td></tr></button>
		<tr><td><button class="medium red"><?php echo $this->Form->postLink(__('Delete Good'), array('action' => 'delete', $good['Good']['id']), array('confirm' => __('Are you sure you want to delete # %s?', $good['Good']['id']))); ?></td></tr></button>
		<tr><td><button class="medium blue"><?php echo $this->Html->link(__('List Goods'), array('action' => 'index')); ?></td></tr></button>
		<tr><td><button class="medium green"><?php echo $this->Html->link(__('New Good'), array('action' => 'save')); ?></td></tr></button>
</table></div>
<div class="goods view col_10">
<h2><?php echo __('Good'); ?></h2>
<!-- Color the active field -->
<?php
$ans = 'No';
$col = 'red';
if ($good['Good']['for_distributors']) {
	$ans = 'Yes';
	$col = 'green';
}
?>
	<table cellpadding="0" cellspacing="0" class="striped sortable">
<tr>		<td><?php echo __('Code'); ?></td>
		<td>
			<?php echo h($good['Item']['code']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Name'); ?></td>
		<td>
			<?php echo h($good['Item']['name']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Status'); ?></td>
		<td>
			<?php echo h($good['Good']['status']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Pid'); ?></td>
		<td>
			<?php echo h($good['Good']['pid']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Hts Number'); ?></td>
		<td>
			<?php echo h($good['Good']['hts_number']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Tax Group'); ?></td>
		<td>
			<?php echo h($good['Good']['tax_group']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Eccn'); ?></td>
		<td>
			<?php echo h($good['Good']['eccn']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Release Date'); ?></td>
		<td>
			<?php echo h($good['Good']['release_date']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('For Distributors'); ?></td>
		<td style="color:<?php echo $col; ?>">
			<?php echo $ans; ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Created'); ?></td>
		<td>
			<?php echo h($good['Good']['created']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Modified'); ?></td>
		<td>
			<?php echo h($good['Good']['modified']); ?>
			&nbsp;
		</td>
</tr></table>
</div>
