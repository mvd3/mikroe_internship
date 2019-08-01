<?php
App::uses('AppModel', 'Model');
App::uses('CakeTime', 'Utility');
/**
 * MoveCard Model
 *
 */
class MoveCard extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'work_order' => array(
			'check' => array(
				'rule' => array('check_work_order', 'type'),
				'message' => 'Work order needed!'
			)
		)
	);

	public $belongsTo = array(
	      'Creator' => array(
	        'className' => 'User',
	        'foreignKey' => 'creator',
	      ),
				'From' => array(
	        'className' => 'WarehouseLocation',
	        'foreignKey' => 'move_from',
	      ),
				'To' => array(
	        'className' => 'WarehouseLocation',
	        'foreignKey' => 'move_to',
	      ),
				'IssuedBy' => array(
	        'className' => 'User',
	        'foreignKey' => 'issued_by',
	      ),
				'RecievedBy' => array(
	        'className' => 'User',
	        'foreignKey' => 'recieved_by',
	      )
	  );

	/*
		Checks if the work order is entered for trebovanje
	*/
	public function check_work_order($check, $query_field) {
		$type = Hash::get($this->data[$this->alias], $query_field);
		if ($type==2) {
			return Validation::notBlank(current($check));
		}
		return true;
	}

/*
	Generates next code based on the type
*/
	public function generateNextCode($type) {
		$types = array(
			'standard' => 'INTPRE',
			'trebovanje' => 'TREMAT'
		);
		$year = CakeTime::format(time(), '%Y');
		$nextID = $this->find('count', array(
			'conditions' => array('MoveCard.type' => $type)
		));
		$nextID++;
		$nextID %= 10000;
		$numeric = $year*10000 + $nextID;
		return $types[$type] . $numeric;
	}

	/*
    Returns all data from the move card
    Input argument is the id
  */
  public function getCompleteData($id) {
    $data = $this->find('first', array(
      'conditions' => array('MoveCard.id' => $id)
    ));
    return $data;
  }

}
