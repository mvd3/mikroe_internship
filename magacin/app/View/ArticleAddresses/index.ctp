<div class="actions col_2">
  <h3><?php echo __('Navigation'); ?></h3>
  <?php echo $this->Form->create(false, array('url' => array('controller' => 'ArticleAddresses', 'action' => 'index'))) ?>
  <fieldset>
		<legend><?php echo __('Select address'); ?></legend>
	<?php
		echo $this->Form->input('warehouse_address', array('options' => $addressOptions));
	?>
	</fieldset>
  <?php echo $this->Form->end(__('Select')) ?>
  <hr>
  <?php if ($addressData != null) { ?>
  <button class="medium green"><?php echo $this->Html->link(__('Add items'), array('action' => 'add', $addressData['WarehouseAddress']['id'])); ?></button>
  <?php } ?>
</div>
<div class="actions col_10">
  <h3><?php echo __('Address'); ?></h3>
  <table cellpadding="0" cellspacing="0" class="striped sortable">
    <tr>
      <th>Code</th>
      <th>Row</th>
      <th>Shelf</th>
      <th>Bulkhead</th>
    </tr>
    <tr>
      <td><?php echo h($addressData['WarehouseAddress']['code']); ?></td>
      <td><?php echo h($addressData['WarehouseAddress']['row']); ?></td>
      <td><?php echo h($addressData['WarehouseAddress']['shelf']); ?></td>
      <td><?php echo h($addressData['WarehouseAddress']['bulkhead']); ?></td>
    </tr>
  </table>
  <h3><?php echo __('Items'); ?></h3>
  <table cellpadding="0" cellspacing="0" class="striped sortable">
    <tr>
      <th>Code</th>
      <th>Name</th>
      <th>Quantity</th>
      <th>Reserved</th>
    </tr>
    <?php if ($selectedData!=null) { ?>
    <?php foreach ($selectedData as $data): ?>
      <tr>
        <td><?php echo h($data['itemCode']); ?></td>
        <td><?php echo h($data['name']); ?></td>
        <td><?php echo h($data['quantity']); ?></td>
        <td><?php echo h($data['reserved']); ?></td>
      </tr>
    <?php endforeach; ?>
  <?php } ?>
  </table>
</div>
