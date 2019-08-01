<?php
App::uses('AppModel', 'Model');
/**
 * MeasurementUnit Model
 *
 */
class MeasurementUnit extends AppModel {
	public $primaryKey = 'id';
/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => array('Name can\'t be empty!')
			),
			'unique' => array(
				'rule' =>array('isUnique'),
				'message' => array('Name must be unique!')
			)
		),
		'symbol' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => array('Symbol can\'t be empty!')
			),
			'unique' => array(
				'rule' =>array('isUnique'),
				'message' => array('Symbol must be unique!')
			)
		)
	);

	//Check if the measurement is still used
	public function checkDeletePosibility($id) {
		$item = ClassRegistry::init('Item');
		$sql = "SELECT count(*) as 'count' FROM `items` WHERE measurement_unit=" . $id;
		$data = $item->query($sql);
		if ($data[0][0]['count']>0) {
			return false;
		}
		return true;
	}

	//Name says everything
	public function getAll() {
		$data = $this->find('all');
		return $data;
	}

	public function getByName($name) {
		$data = $this->find('first', array(
				'conditions' => array('MeasurementUnit.name' => $name)
			)
		);
		return $data;
	}

	/*
		Returns all associated data on model
	*/
		 public function getCompleteData($id) {
	 		$data = $this->find(
	 		 'first', array(
	 			 'conditions' => array(
	 				 'MeasurementUnit.id' => $id
	 			 )
	 		 )
	 	 );
	 		return $data;
	 	}

		/*
			Takes the data from requests and fetches it into an object for save.
			The secon parametar is the id, for the case of editing
		*/
		public function fetchDataForSave($data, $id) {
			$saveData = null;
			if ($this->exists($id)) {
				$comData = $this->getCompleteData($id);
				$saveData = array(
					 'MeasurementUnit' => array(
						 'id' => $comData['MeasurementUnit']['id'],
						 'name' => $data['MeasurementUnit']['name'],
						 'symbol' => $data['MeasurementUnit']['symbol'],
						 'active' => $data['MeasurementUnit']['active'],
					 )
				 );
			} else {
				$saveData = array(
					'MeasurementUnit' => array(
						'name' => $data['MeasurementUnit']['name'],
						'symbol' => $data['MeasurementUnit']['symbol'],
						'active' => $data['MeasurementUnit']['active']
					)
				);
			}
			return $saveData;
		}

		/*
			Prepares field data for the save.cty
			Input parametars are controller pointer and id
		*/
		public function prepareDataForSave($controller, $id) {
			if ($this->exists($id)) {
				$comData = $this->getCompleteData($id);
				$controller->set('name', $comData['MeasurementUnit']['name']);
	 			$controller->set('symbol', $comData['MeasurementUnit']['symbol']);
	 			$controller->set('active', $comData['MeasurementUnit']['active']);
			} else {
				$controller->set('name', null);
				$controller->set('symbol', null);
				$controller->set('active', null);
			}
		}
}
