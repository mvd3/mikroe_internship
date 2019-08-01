<div class="actions col_2">
	<h3><?php echo __('Actions'); ?></h3>
<table>		<tr><td><button class="medium orange"><?php echo $this->Html->link(__('Edit Product'), array('action' => 'save', $product['Product']['id'])); ?></td></tr></button>
		<tr><td><button class="medium red"><?php echo $this->Form->postLink(__('Delete Product'), array('action' => 'delete', $product['Product']['id']), array('confirm' => __('Are you sure you want to delete # %s?', $product['Product']['id']))); ?></td></tr></button>
		<tr><td><button class="medium blue"><?php echo $this->Html->link(__('List Products'), array('action' => 'index')); ?></td></tr></button>
		<tr><td><button class="medium green"><?php echo $this->Html->link(__('New Product'), array('action' => 'save')); ?></td></tr></button>
</table></div>
<div class="products view col_10">
<h2><?php echo __('Product'); ?></h2>
<!-- Color the active field -->
<?php
$ansDistributors = 'No';
$colDistributors = 'red';
$ansService = 'No';
$colService = 'red';
if ($product['Product']['for_distributors']) {
	$ansDistributors = 'Yes';
	$colDistributors = 'green';
}
if ($product['Product']['service_production']) {
	$ansService = 'Yes';
	$colService = 'green';
}
?>
	<table cellpadding="0" cellspacing="0" class="striped sortable">
<tr>		<td><?php echo __('Code'); ?></td>
		<td>
			<?php echo h($product['Item']['code']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Name'); ?></td>
		<td>
			<?php echo h($product['Item']['name']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Pid'); ?></td>
		<td>
			<?php echo h($product['Product']['pid']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Hts Number'); ?></td>
		<td>
			<?php echo h($product['Product']['hts_number']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Tax Group'); ?></td>
		<td>
			<?php echo h($product['Product']['tax_group']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Product Eccn'); ?></td>
		<td>
			<?php echo h($product['Product']['product_eccn']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Product Release Date'); ?></td>
		<td>
			<?php echo h($product['Product']['product_release_date']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('For Distributors'); ?></td>
		<td style="color:<?php echo $colDistributors; ?>">
			<?php echo $ansDistributors; ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Product Status'); ?></td>
		<td>
			<?php echo h($product['Product']['product_status']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Service Production'); ?></td>
		<td style="color:<?php echo $colService; ?>">
			<?php echo $ansService; ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Project'); ?></td>
		<td>
			<?php echo h($product['Product']['project']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Created'); ?></td>
		<td>
			<?php echo h($product['Product']['created']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Modified'); ?></td>
		<td>
			<?php echo h($product['Product']['modified']); ?>
			&nbsp;
		</td>
</tr></table>
</div>
