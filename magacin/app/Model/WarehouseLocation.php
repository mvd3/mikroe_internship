<?php
App::uses('AppModel', 'Model');
/**
 * WarehouseLocation Model
 *
 */
class WarehouseLocation extends AppModel {

	private $classes = array(
		1 => 'product',
		2 => 'goods',
		3 => 'service_product',
		4 => 'material',
		5 => 'semi_product',
		6 => 'consumable',
		7 => 'inventory'
	);

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'code' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Enter code here',
			),
			'unique' => array(
				'rule' => array('isUnique'),
				'message' => 'Code is already in use'
			)
		),
		'name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Enter name here',
			),
		),
		'default' => array(
			'boolean' => array(
				'rule' => array('boolean'),
			),
		),
		'active' => array(
			'boolean' => array(
				'rule' => array('boolean'),
			),
		),
	);

//Return data for this id
	public function getCompleteData($id) {
	 $data = $this->find(
		'first', array(
			'conditions' => array(
				'WarehouseLocation.id' => $id
			)
		)
	);
	 return $data;
 }

/*
	Returns an array with all the locations.
	Array is in format id => code --- (key) => (value)
*/
 public function getAll() {
	 $rawData = $this->find('all', array(
		 'fields' => 'id, code'
	 )
 	);
	 $keys = array();
	 $values = array();
	 foreach($rawData as $data) {
		 array_push($keys, $data['WarehouseLocation']['id']);
		 array_push($values, $data['WarehouseLocation']['code']);
	 }
	 $result = array_combine($keys, $values);
	 return $result;
 }

/*
	Returns an array with all the data
*/
 public function getAllComplete() {
		 $data = $this->find('all');
	 return $data;
 }

 /*
 	Returns all classes
 */
 	public function getClasses() {
 		return $this->classes;
 	}

/*
	Checks if the location can be deleted
	Returns true in case of delete possibility
*/
	public function checkDeletePossibility($loc) {
		$addrs = ClassRegistry::init('WarehouseAddress');
		$addrItem = ClassRegistry::init('WarehouseAddressItem');
		$data = $addrs->getAllWithLocation($loc);
		if ($data==null) {
			return true;
		}
		foreach($data as $d) {
			$rawData = $addrItem->getAllWithAddress($d['WarehouseAddress']['id']);
			if ($rawData!=null) {
				return false;
			}
		}
		return true;
	}

	//Return data for this id
		public function getLocationsInWarehouse($warehouse) {
		 $data = $this->find(
			'all', array(
				'conditions' => array(
					'WarehouseLocation.warehouse' => $warehouse
				)
			)
		);
		 return $data;
	 }
}
