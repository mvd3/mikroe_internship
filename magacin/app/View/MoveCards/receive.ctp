<div class="actions col_2">
	<h3><?php echo __('Actions'); ?></h3>
	<table>
		<tr><td><button class="medium blue"><?php echo $this->Html->link(__('List Move Cards'), array('action' => 'index')); ?></button></td></tr>
  </table>
</div>
<div class="moveCards form col_10">
  <h3><?php echo __('Items') ?></h3>
  <?php echo $this->Form->create('ReceivedItems'); ?>
  <?php for($i=0;$i<count($itemsData);$i++): ?>
    <?php if ($itemsData[$i]['MoveCardItem']['quantity_recieved']>0) { ?>
    <fieldset>
      <legend><?php echo __($itemsData[$i]['Item']['name'] . ' - Received: ' . $itemsData[$i]['MoveCardItem']['quantity_recieved']); ?></legend>
      <table>
        <tr>
          <td><?php echo $this->Form->input("{$i}.address", array('options' => $addressData)); ?></td>
          <td><?php echo $this->Form->input("{$i}.number", array('type' => 'hidden', 'default' => $itemsData[$i]['MoveCardItem']['quantity_recieved'])); ?></td>
          <td><?php echo $this->Form->input("{$i}.item", array('type' => 'hidden', 'default' => $itemsData[$i]['MoveCardItem']['item'])); ?></td>
          <td><?php echo $this->Form->input("{$i}.originAddress", array('type' => 'hidden', 'default' => $itemsData[$i]['MoveCardItem']['address_issued'])); ?></td>
          <td><?php echo $this->Form->input("{$i}.moveCardItemID", array('type' => 'hidden', 'default' => $itemsData[$i]['MoveCardItem']['id'])); ?></td>
        </tr>
      </table>
    </fieldset>
    <?php } ?>
  <?php endfor; ?>
  <?php echo $this->Form->end(__('Submit')); ?>
</div>
