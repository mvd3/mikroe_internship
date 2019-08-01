<div class="actions col_2">
	<h3><?php echo __('Actions'); ?></h3>
<table>		<tr><td><button class="medium orange"><?php echo $this->Html->link(__('Edit Service Supplier'), array('action' => 'save', $serviceSupplier['ServiceSupplier']['id'])); ?></td></tr></button>
		<tr><td><button class="medium red"><?php echo $this->Form->postLink(__('Delete Service Supplier'), array('action' => 'delete', $serviceSupplier['ServiceSupplier']['id']), array('confirm' => __('Are you sure you want to delete # %s?', $serviceSupplier['ServiceSupplier']['id']))); ?></td></tr></button>
		<tr><td><button class="medium blue"><?php echo $this->Html->link(__('List Service Suppliers'), array('action' => 'index')); ?></td></tr></button>
		<tr><td><button class="medium green"><?php echo $this->Html->link(__('New Service Supplier'), array('action' => 'save')); ?></td></tr></button>
</table></div>
<div class="serviceSuppliers view col_10">
<h2><?php echo __('Service Supplier'); ?></h2>
	<table cellpadding="0" cellspacing="0" class="striped sortable">
<tr>		<td><?php echo __('Code'); ?></td>
		<td>
			<?php echo h($serviceSupplier['Item']['code']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Name'); ?></td>
		<td>
			<?php echo h($serviceSupplier['Item']['name']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Service Status'); ?></td>
		<td>
			<?php echo h($serviceSupplier['ServiceSupplier']['service_status']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Service Rating'); ?></td>
		<td>
			<?php echo h($serviceSupplier['ServiceSupplier']['service_rating']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Created'); ?></td>
		<td>
			<?php echo h($serviceSupplier['ServiceSupplier']['created']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Modified'); ?></td>
		<td>
			<?php echo h($serviceSupplier['ServiceSupplier']['modified']); ?>
			&nbsp;
		</td>
</tr></table>
</div>
