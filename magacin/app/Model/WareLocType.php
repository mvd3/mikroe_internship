<?php
App::uses('AppModel', 'Model');
/**
 * WareLocType Model
 *
 */
class WareLocType extends AppModel {

/**
 * Use table
 *
 * @var mixed False or table name
 */
	public $useTable = 'ware_loc_type';

	//Name says everything
	public function getAll() {
		$data = $this->find('all');
		return $data;
	}

	/*
		Check by location id and item type if it already exists in the table.
		Returns true in case of existence
		Input $item must be 'name_of_the_input', not a numeric value
	*/
	public function checkExistence($loc, $item) {
		$data = $this->find('first',
			array(
				'conditions' => array(
					'AND' => array(
						'WareLocType.location' => $loc,
						'WareLocType.type' => $item
					)
				)
			)
		);
		if ($data!=null) {
			return true;
		}
		return false;
	}

/*
	Deletes an item based on the location and item type
	Input $item must be 'name_of_the_input', not a numeric value
*/
	public function deleteItem($loc, $item) {
		$data = $this->find('first',
			array(
				'conditions' => array(
					'AND' => array(
						'WareLocType.location' => $loc,
						'WareLocType.type' => $item
					)
				)
			)
		);
		$id = $data['WareLocType']['id'];
		$this->delete($id);
	}

/*
	Returns all active classes (item types) for this location
*/
	public function getClassesForLocation($loc) {
		$data = $this->find('all', array(
				'conditions' => array('WareLocType.location' => $loc)
			)
		);
		$result = array();
		foreach($data as $d) {
			array_push($result, $d['WareLocType']['type']);
		}
		return $result;
	}
}
