<div class="actions col_2">
	<h3><?php echo __('Actions'); ?></h3>
<table>		<tr><td><button class="medium orange"><?php echo $this->Html->link(__('Edit Item Type'), array('action' => 'save', $itemType['ItemType']['id'])); ?></td></tr></button>
		<tr><td><button class="medium red"><?php echo $this->Form->postLink(__('Delete Item Type'), array('action' => 'delete', $itemType['ItemType']['id']), array('confirm' => __('Are you sure you want to delete # %s?', $itemType['ItemType']['id']))); ?></td></tr></button>
		<tr><td><button class="medium blue"><?php echo $this->Html->link(__('List Item Types'), array('action' => 'index')); ?></td></tr></button>
		<tr><td><button class="medium green"><?php echo $this->Html->link(__('New Item Type'), array('action' => 'save')); ?></td></tr></button>
</table></div>
<div class="itemTypes view col_10">
<h2><?php echo __('Item Type'); ?></h2>
<!-- Color the active field -->
<?php
$ansTangible = 'No';
$colTangible = 'red';
$ansActive = 'No';
$colActive = 'red';
if ($itemType['ItemType']['tangible']) {
	$ansTangible = 'Yes';
	$colTangible = 'green';
}
if ($itemType['ItemType']['active']) {
	$ansActive = 'Yes';
	$colActive = 'green';
}
?>
	<table cellpadding="0" cellspacing="0" class="striped sortable">
		<tr><td><?php echo __('Code'); ?></td>
		<td>
			<?php echo h($itemType['ItemType']['code']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Name'); ?></td>
		<td>
			<?php echo h($itemType['ItemType']['name']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Class'); ?></td>
		<td>
			<?php echo h($itemType['ItemType']['class']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Tangible'); ?></td>
		<td style="color:<?php echo $colTangible; ?>">
			<?php echo $ansTangible; ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Active'); ?></td>
		<td style="color:<?php echo $colActive; ?>">
			<?php echo $ansActive; ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Created'); ?></td>
		<td>
			<?php echo h($itemType['ItemType']['created']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><?php echo __('Modified'); ?></td>
		<td>
			<?php echo h($itemType['ItemType']['modified']); ?>
			&nbsp;
		</td>
</tr></table>
</div>
