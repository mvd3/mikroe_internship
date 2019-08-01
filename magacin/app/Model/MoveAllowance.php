<?php
App::uses('AppModel', 'Model');
/**
 * MoveAllowance Model
 *
 */
class MoveAllowance extends AppModel {

	public $belongsTo = array(
				'Operator' => array(
					'className' => 'User',
					'foreignKey' => 'operator',
					'dependent' => true
				),
				'WarehouseLocation' => array(
					'className' => 'WarehouseLocation',
					'foreignKey' => 'warehouse_location',
					'dependent' => true
				)
		);

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'operator' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'warehouse_location' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'allowance' => array(
			'boolean' => array(
				'rule' => array('boolean'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

	/*
		Returns all associated data on model
	*/
		 public function getCompleteData($id) {
	 		$data = $this->find(
	 		 'first', array(
	 			 'conditions' => array(
	 				 'MoveAllowance.id' => $id
	 			 )
	 		 )
	 	 );
	 		return $data;
	 	}

/*
	If the operator is alreday in the database, then it will return true
*/
		public function checkExistence($op, $loc) {
			$data = $this->find('first', array(
				'conditions' => array(
					'MoveAllowance.operator' => $op,
					'MoveAllowance.warehouse_location' => $loc
				)
			));
			if ($data!=null) {
				return true;
			}
			return false;
		}

		/*
			Returns all data for this operator
		*/
		public function getAllData($operator) {
			return $this->find('all', array(
				'conditions' => array('MoveAllowance.operator' => $operator)
			));
		}
}
