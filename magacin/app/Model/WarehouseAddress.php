<?php
App::uses('AppModel', 'Model');
App::uses('Hash', 'Utility');
/**
 * WarehouseAddress Model
 *
 */
class WarehouseAddress extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'code' => array(
			'checkCode' => array(
				'rule' => array('isUnique'),
				'message' => 'Code must be unique',
			),
		),
		'row' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Enter row here',
			),
			'allowed' => array(
				'rule' => '/^[a-zA-Z]{1,1}$/i',
				'message' => 'Row must be a letter'
			)
		),
		'shelf' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Enter shelf here',
			),
		),
		'bulkhead' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Enter bulkhead here',
			),
		),
		'warehouse_location' => array(
			'activeCheck' => array(
				'rule' => array('check_location'),
				'message' => array('Warehouse location is not active')
			),
		),
		'active' => array(
			'boolean' => array(
				'rule' => array('boolean'),
			),
		),
		'barcode' => array(
			'unique' => array(
				'rule' => 'isUnique'
			)
		)
	);

/*
	Checks if the location is active
	Used for validation
*/
	public function check_location($check) {
		$id = current($check);
		$loc = ClassRegistry::init('WarehouseLocation');
		if ($loc->getCompleteData($id)['WarehouseLocation']['active']) {
			return true;
		}
		return false;
	}

//Return data for this id
	public function getCompleteData($id) {
	 $data = $this->find(
		'first', array(
			'conditions' => array(
				'WarehouseAddress.id' => $id
			)
		)
	);
	 return $data;
 }

/*
	Returns all data inside an array.
	The data is mapped id => code --- (key) => (value)
*/
 public function getAll() {
	 $rawData = $this->find('all', array(
		 'fields' => 'id, code'
	 )
 	);
	 $keys = array();
	 $values = array();
	 foreach($rawData as $data) {
		 array_push($keys, $data['WarehouseAddress']['id']);
		 array_push($values, $data['WarehouseAddress']['code']);
	 }
	 $result = array_combine($keys, $values);
	 return $result;
 }

/*
	Returns all addresses with the specified location
*/
 public function getAllWithLocation($loc) {
	 $data = $this->find('all', array(
		 'conditions' => array('WarehouseAddress.warehouse_location' => $loc)
	 ));
	 return $data;
 }

/*
	Function for calculating barcode
	You can find the definition for the barcode in the documentatio of the project
*/
 public function generateBarcode($loc, $row, $shelf, $bulkhead) {
	 $barcode = 2912;
	 $loc %= 100;
	 $barcode *= 100;
	 $barcode += $loc;
	 $barcode *= 100;
	 $barcode += ord($row);
	 $barcode *= 100;
	 $shelf %= 100;
	 $barcode += $shelf;
	 $barcode *= 100;
	 $bulkhead %= 100;
	 $barcode += $bulkhead;
	 $check = 0;
	 for ($i=0;$i<6;$i++) {
		 $check += substr($barcode, 2*$i, 1);
	 }
	 for ($i=0;$i<6;$i++) {
		 $check += 3*substr($barcode, 2*$i+1, 1);
	 }
	 $check %= 10;
	 if ($check>5) {
		 $check = 10 - $check;
	 }
	 $barcode *= 10;
	 $barcode += $check;
	 return $barcode;
 }

/*
	Checks the possibility of deleting this address.
	Returns true if it can be deleted
*/
 public function checkDeletePossibility($addr) {
	 $addrItem = ClassRegistry::init('WarehouseAddressItem');
	 $data = $addrItem->getAllWithAddress($addr);
	 if ($data!=null) {
		 return false;
	 }
	 return true;
 }
}
