<?php
App::uses('AppModel', 'Model');
/**
 * WarehouseAddressItem Model
 *
 */
class WarehouseAddressItem extends AppModel {

/**
 * Use table
 *
 * @var mixed False or table name
 */
	public $useTable = 'warehouse_address_items';

	public $belongsTo = array(
		'Address' => array(
			'className' => 'WarehouseAddress',
      'foreignKey' => 'warehouse_address'
		),
		'Item' => array(
			'className' => 'Item',
      'foreignKey' => 'item'
		)
	);

/*
	Returns all data
*/
	public function getAllWithAddress($address) {
 	 $data = $this->find('all', array(
		 	'conditions' => array('WarehouseAddressItem.warehouse_address' => $address)
	 ));
 	 return $data;
  }

	/*
		Checks if the item is already on this address
		Returns true in the case of existence
	*/
	public function checkExistence($addr, $item) {
		$data = $this->find('all', array(
			'conditions' => array(
				'WarehouseAddressItem.warehouse_address' => $addr,
				'WarehouseAddressItem.item' => $item
			)
		));
		if ($data!=null) {
			return true;
		}
		return false;
	}

	//Return data for this address and item
		public function getCompleteData($addr, $item) {
		 $data = $this->find(
			'first', array(
				'conditions' => array(
					'WarehouseAddressItem.warehouse_address' => $addr,
					'WarehouseAddressItem.item' => $item
				)
			)
		);
		 return $data;
	 }

	 /*
	 	Returns all addresses that contain this item
	 */
	 	public function getAddressesWithItem($item) {
	  	 $data = $this->find('all', array(
	 		 	'conditions' => array('WarehouseAddressItem.item' => $item)
	 	 ));
	  	 return $data;
	   }

		 /*
		 Deletes all items with the given item id
		 */
		 public function deleteItems($item) {
			 $data = $this->find('all', array(
				 'conditions' => array('WarehouseAddressItem.item' => $item),
				 'fields' => 'id'
			 ));
			 for($i=0;$i<count($data);$i++) {
				 $this->delete($data[$i]['WarehouseAddressItem']['id']);
			 }
		 }

}
