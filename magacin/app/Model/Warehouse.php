<?php
App::uses('AppModel', 'Model');
/**
 * Warehouse Model
 *
 */
class Warehouse extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Enter a name!',
			),
		),
	);

/*
	Data for the choosen model
*/
	public function getCompleteData($id) {
	 $data = $this->find(
		'first', array(
			'conditions' => array(
				'Warehouse.id' => $id
			)
		)
	);
	 return $data;
 }

 //Returns an array with pairs id => name --- (key) => (value)
 public function getAllAsArray() {
	 $data = $this->find('all');
	 $keys = array();
	 $values = array();
	 foreach($data as $d) {
		 array_push($keys, $d['Warehouse']['id']);
		 array_push($values, $d['Warehouse']['name']);
	 }
	 $result = array_combine($keys, $values);
	 return $result;
 }

 /*
 	Checks if the warehouse can be deleted
 	Returns true in case of delete possibility
 */
 public function checkDeletePossibility($warehouse) {
	 $loc = ClassRegistry::init('WarehouseLocation');
	 $locations = $loc->getLocationsInWarehouse($warehouse);
	 foreach($locations as $location) {
		 if (!$loc->checkDeletePossibility($location['WarehouseLocation']['id'])) {
			 return false;
		 }
	 }
	 return true;
 }

}
