<div class="actions col_2">
	<h3><?php echo __('Actions'); ?></h3>
<table>		<tr><td><button class="medium orange"><?php echo $this->Html->link(__('Edit Semi Product'), array('action' => 'save', $semiProduct['SemiProduct']['id'])); ?></td></tr></button>
		<tr><td><button class="medium red"><?php echo $this->Form->postLink(__('Delete Semi Product'), array('action' => 'delete', $semiProduct['SemiProduct']['id']), array('confirm' => __('Are you sure you want to delete # %s?', $semiProduct['SemiProduct']['id']))); ?></td></tr></button>
		<tr><td><button class="medium blue"><?php echo $this->Html->link(__('List Semi Products'), array('action' => 'index')); ?></td></tr></button>
		<tr><td><button class="medium green"><?php echo $this->Html->link(__('New Semi Product'), array('action' => 'save')); ?></td></tr></button>
</table></div>
<div class="semiProducts view col_10">
<h2><?php echo __('Semi Product'); ?></h2>
<!-- Color the active field -->
<?php
$ans = 'No';
$col = 'red';
if ($semiProduct['SemiProduct']['service_production']) {
	$ans = 'Yes';
	$col = 'green';
}
?>
	<table cellpadding="0" cellspacing="0" class="striped sortable">
<tr>		<td><?php echo __('Code'); ?></td>
		<td>
			<?php echo h($semiProduct['Item']['code']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Name'); ?></td>
		<td>
			<?php echo h($semiProduct['Item']['name']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Semi Product Status'); ?></td>
		<td>
			<?php echo h($semiProduct['SemiProduct']['semi_product_status']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Service Production'); ?></td>
		<td style="color:<?php echo $col; ?>">
			<?php echo $ans; ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Created'); ?></td>
		<td>
			<?php echo h($semiProduct['SemiProduct']['created']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Modified'); ?></td>
		<td>
			<?php echo h($semiProduct['SemiProduct']['modified']); ?>
			&nbsp;
		</td>
</tr></table>
</div>
