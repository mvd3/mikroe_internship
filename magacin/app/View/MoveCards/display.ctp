<div class="actions col_2">
	<h3><?php echo __('Actions'); ?></h3>
	<table>
		<tr><td><button class="medium blue"><?php echo $this->Html->link(__('List Move Cards'), array('action' => 'index')); ?></button></td></tr>
  </table>
</div>
<div class="actions col_10">
	<h3><?php echo __('Move Card'); ?></h3>
  <table cellpadding="0" cellspacing="0" class="striped sortable">
    <tr>
      <td><?php echo __('Code'); ?></td>
      <td>
  			<?php echo h($moveCard['MoveCard']['code']); ?>
  			&nbsp;
  		</td>
    </tr>
    <tr>
      <td><?php echo __('Created'); ?></td>
      <td>
  			<?php echo h($moveCard['MoveCard']['created']); ?>
  			&nbsp;
  		</td>
    </tr>
    <tr>
      <td><?php echo __('Created By'); ?></td>
      <td>
  			<?php echo h($moveCard['Creator']['name']); ?>
  			&nbsp;
  		</td>
    </tr>
    <tr>
      <td><?php echo __('Move from'); ?></td>
      <td>
  			<?php echo h($moveCard['From']['code'] . '-' . $moveCard['From']['name']); ?>
  			&nbsp;
  		</td>
    </tr>
    <tr>
      <td><?php echo __('Move to'); ?></td>
      <td>
  			<?php echo h($moveCard['To']['code'] . '-' . $moveCard['To']['name']); ?>
  			&nbsp;
  		</td>
    </tr>
    <tr>
      <td><?php echo __('Issued By'); ?></td>
      <td>
  			<?php echo h($moveCard['IssuedBy']['name']); ?>
  			&nbsp;
  		</td>
    </tr>
    <tr>
      <td><?php echo __('Status'); ?></td>
      <td>
  			<?php echo h($moveCard['MoveCard']['code']); ?>
  			&nbsp;
  		</td>
    </tr>
    <tr>
      <td><?php echo __('Type'); ?></td>
      <td>
  			<?php echo h($moveCard['MoveCard']['status']); ?>
  			&nbsp;
  		</td>
    </tr>
    <tr>
      <td><?php echo __('Received By'); ?></td>
      <td>
  			<?php echo h($moveCard['RecievedBy']['name']); ?>
  			&nbsp;
  		</td>
    </tr>
    <tr>
      <td><?php echo __('Work order'); ?></td>
      <td>
  			<?php echo h($moveCard['MoveCard']['work_order']); ?>
  			&nbsp;
  		</td>
    </tr>
  </table>
  <h3><?php echo __('Items'); ?></h3>
  <table cellpadding="0" cellspacing="0" class="striped sortable">
    <tr>
      <th>Article</th>
      <th>Unit</th>
      <th>Requested quantity</th>
      <th>Received quantity</th>
      <th>Issued address</th>
      <th>Received address</th>
    </tr>
    <?php for ($i=0;$i<count($items);$i++): ?>
      <tr>
        <td><?php echo h($items[$i]['Item']['name']); ?></td>
        <td><?php echo h($units[$i]['MeasurementUnit']['symbol']); ?></td>
        <td><?php echo h($items[$i]['MoveCardItem']['quantity_demanded']); ?></td>
        <td><?php echo h($items[$i]['MoveCardItem']['quantity_recieved']); ?></td>
        <td><?php echo h($items[$i]['AddressIssued']['code']); ?></td>
        <td><?php echo h($items[$i]['AddressRecieved']['code']); ?></td>
      </tr>
    <?php endfor; ?>
  </table>
</div>
