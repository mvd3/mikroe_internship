<div class="actions col_2">
  <h3><?php echo __('Navigation'); ?></h3>
  <?php echo $this->Form->create(false, array('url' => array('controller' => 'WarehouseArticles', 'action' => 'index'))) ?>
  <fieldset>
		<legend><?php echo __('Select address'); ?></legend>
	<?php
echo $this->Form->input('warehouse_location', array('options' => $locationOptions));
	?>
	</fieldset>
  <?php echo $this->Form->end(__('Select')) ?>
</div>
<div class="actions col_10">
  <h3><?php echo __('Location'); ?></h3>
  <table cellpadding="0" cellspacing="0" class="striped sortable">
    <tr>
      <th>Code</th>
      <th>Name</th>
    </tr>
    <tr>
      <td><?php echo h($location['WarehouseLocation']['code']); ?></td>
      <td><?php echo h($location['WarehouseLocation']['name']); ?></td>
    </tr>
  </table>
  <h3><?php echo __('Items'); ?></h3>
  <table cellpadding="0" cellspacing="0" class="striped sortable">
    <tr>
      <th>Code</th>
      <th>Name</th>
      <th>Total</th>
      <th>Free</th>
      <th>Reserved</th>
    </tr>
    <?php if ($selectedData!=null) { ?>
    <?php foreach ($selectedData as $data): ?>
      <tr>
        <td><?php echo h($data['code']); ?></td>
        <td><?php echo h($data['name']); ?></td>
        <td><?php echo h($data['quantity']); ?></td>
        <td><?php echo h($data['quantity']-$data['reserved']); ?></td>
        <td><?php echo h($data['reserved']); ?></td>
      </tr>
    <?php endforeach; ?>
  <?php } ?>
  </table>
</div>
