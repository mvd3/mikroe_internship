<div class="actions col_2">
	<h3><?php echo __('Actions'); ?></h3>
	<table>
		<tr><td><button class="medium blue"><?php echo $this->Html->link(__('List Move Cards'), array('action' => 'index')); ?></button></td></tr>
  </table>
</div>
<div class="moveCards form col_10">
  <h3><?php echo __('Items') ?></h3>
  <?php echo $this->Form->create('SuppliedItems'); ?>
  <?php for($i=0;$i<count($moveCardItems);$i++): ?>
    <?php if ($addressData[$i]!=null) { ?>
    <fieldset>
      <legend><?php echo __($moveCardItems[$i]['Item']['name'] . ' - Required: ' . $moveCardItems[$i]['MoveCardItem']['quantity_demanded']); ?></legend>
      <table>
        <tr>
          <td><?php echo $this->Form->input("{$i}.address", array('options' => $addressData[$i]['Address'])); ?></td>
          <td><?php echo $this->Form->input("{$i}.number", array('type' => 'number', 'max' => $addressData[$i]['Available'], 'min' => 0, 'default' => 0)); ?></td>
        </tr>
      </table>
    </fieldset>
    <?php } ?>
  <?php endfor; ?>
  <?php echo $this->Form->end(__('Submit')); ?>
</div>
