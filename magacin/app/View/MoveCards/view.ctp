<div class="actions col_2">
	<h3><?php echo __('Actions'); ?></h3>
	<table>
		<tr><td><button class="medium blue"><?php echo $this->Html->link(__('List Move Cards'), array('action' => 'index')); ?></button></td></tr></table>
</div>
<div class="moveCards form col_10">
<?php echo $this->Form->create('MoveCard'); ?>
	<fieldset>
		<legend><?php echo __('Add Move Card'); ?></legend>
		<table>
			<tr>
				<td><?php echo $this->Form->input('move_from', array('options' => $locationOptions, 'default' => $moveFromDefault)); ?></td>
				<td><?php echo $this->Form->input('move_to', array('options' => $locationOptions, 'default' => $moveToDefault)); ?></td>
				<td><?php echo $this->Form->input('type', array('options' => $typeOptions, 'default' => $typeDefault)); ?></td>
				<td><?php echo $this->Form->input('work_order', array('required' => false, 'default' => $workorderDefault)); ?></td>
			</tr>

</table>
	</fieldset>
	<fieldset>
    <legend><?php echo __('Request');?></legend>
    <table id="request-table">
        <thead>
            <tr>
                <th>Item</th>
                <th>Quantity</th>
                <th>&nbsp;</th>
            </tr>
        </thead>
        <tbody>
					<!-- <script id="item-template" type="text/x-underscore-template"> -->
							<?php // echo $this->element('../MoveCards/items');?>
					<!-- </script> -->
					<?php //$key = isset($key) ? $key : /*'<%= key %>'*/0; ?>
					<?php for($i=0;$i<count($moveCardItems);$i++): ?>
					<tr>
					    <td>
					        <?php echo $this->Form->input("Item.{$i}.item", array(
										'type' => 'select',
					          'options' => $itemOptions,
					          'empty' => '-- Select item --',
										'class' => 'selectItem',
										'default' => $moveCardItems[$i]['MoveCardItem']['item']
					        )); ?>
					    </td>
					    <td>
					        <?php echo $this->Form->input("Item.{$i}.quantity", array('required' => true, 'default' => $moveCardItems[$i]['MoveCardItem']['quantity_demanded'])); ?>
					    </td>
					    <td class="actions">
					        <a href="#" class="remove">Remove item</a>
					    </td>
					</tr>
				<?php endfor; ?>
				</tbody>
        <tfoot>
            <tr>
                <td colspan="2"></td>
                <td class="actions">
                    <a href="#" class="add">Add item</a>
                </td>
            </tr>
        </tfoot>
    </table>
</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
<h1 id="ajax_text"></h1>
</div>

<table style="display: none;">
	<tbody id="row-constructor">
		<tr>
		    <td>
		        <?php echo $this->Form->input("Item.###.item", array(
							'type' => 'select',
							'name' => 'data[Item][###][item]',
		          // 'options' => array(1 => 'A', 2 => 'B'),
		          'empty' => '-- Select item --',
							'class' => 'selectItem',
							'id' => 'selectItemTemplate'
		        )); ?>
		    </td>
		    <td>
		        <?php echo $this->Form->input("Item.###.quantity", array('name' => 'data[Item][###][quantity]', array('required' => true))); ?>
		    </td>
		    <td class="actions">
		        <a href="#" class="remove">Remove item</a>
		    </td>
		</tr>
	</tbody>
</table>

<script type="text/javascript">
function escapeRegExp(str) {
    return str.replace(/([.*+?^=!:${}()|\[\]\/\\])/g, "\\$1");
}

function replaceAll(str, find, replace) {
    return str.replace(new RegExp(escapeRegExp(find), 'g'), replace);
}

	$('.add').on('click', function(e) {
		e.preventDefault();

		var template = $('#row-constructor').html();
		var table = $('#request-table');
		var numberRows = table.find('tbody > tr').length;
		// template = template.replace("/###/", numberRows);
		template = replaceAll(template, '###', numberRows);
		table.find('tbody').append(template);

	});

	$(document).off('click.remove').on('click.remove', '.remove', function(e) {
		e.preventDefault();
		console.log($(this));
		$(this)
				.closest('tr')
				.fadeOut('fast', function() {
						$(this).remove();
				});
	});

	$(document).ready(function() {
		// $.ajax({
	  //   url: '<?php //echo Router::url(array('controller' => 'MoveCards', 'action' => 'getItems')); ?>',
	  //   type: 'get',
		// 	dataType: 'json',
	  //   success: function (data) {
	  //      console.log(data);
		// 		 // $('#ajax_text').html(data);
	  //   }
		// });
		var valueFrom = $('#MoveCardMoveFrom').val();
		var valueTo = $('#MoveCardMoveTo').val();
		postLocationSingle(valueFrom, valueTo);
	});

	function postLocations(from, to) {
		$.ajax({
			type: 'post',
			url: '<?php echo Router::url(array('controller' => 'MoveCards', 'action' => 'takeLocations')); ?>',
			data: {
				from: from,
				to: to
			},
			success: function(data) {
				var selects = $('.selectItem');
				var count = selects['length'];
				var obj = jQuery.parseJSON(data);
				for(var i=0;i<count;i++) {
					$('#Item'+ i + 'Item').children('option').remove();
					$('select:last').children('option').remove();
					$.each(obj, function(key,value) {
						selects[i].append(new Option(value, key));
				});
			}
			}
		});
	}

	function postLocationSingle(from, to) {
		$.ajax({
			type: 'post',
			url: '<?php echo Router::url(array('controller' => 'MoveCards', 'action' => 'takeLocations')); ?>',
			data: {
				from: from,
				to: to
			},
			success: function(data) {
				var selects = $('#selectItemTemplate');
				var count = selects['length'];
				var obj = jQuery.parseJSON(data);
				for(var i=0;i<count;i++) {
					// $('#Item'+ i + 'Item').children('option').remove();
					$('select:last').children('option').remove();
					$.each(obj, function(key,value) {
						selects[i].append(new Option(value, key));
						// $('select:last')append(new Option(value, key));
				});
			}
			}
		});
	}

	$('#MoveCardMoveFrom').on('change', function(e) {
		e.preventDefault();
		var valueFrom = $(this).val();
		var valueTo = $('#MoveCardMoveTo').val();
		postLocations(valueFrom, valueTo);
	});

	$('#MoveCardMoveTo').on('change', function(e) {
		e.preventDefault();
		var valueTo = $(this).val();
		var valueFrom = $('#MoveCardMoveFrom').val();
		postLocations(valueFrom, valueTo);
	});
</script>
