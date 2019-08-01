<?php
App::uses('AppModel', 'Model');
/**
 * ItemType Model
 *
 */
class ItemType extends AppModel {

private $classes = array(
	1 => 'product',
	2 => 'kit',
	3 => 'material',
	4 => 'semi_product',
	5 => 'service_product',
	6 => 'service_supplier',
	7 => 'consumable',
	8 => 'inventory',
	9 => 'goods',
	10 => 'other'
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
				'message' => array('Code can\'t be empty!')
			),
			'unique' => array(
				'rule' =>array('isUnique'),
				'message' => array('Code must be unique!')
			)
		),
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
		'tangible' => array(
			'boolean' => array(
				'rule' => array('boolean'),
			)
		),
		'active' => array(
			'boolean' => array(
				'rule' => array('boolean'),
			)
		),
		'class' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => array('Code can\'t be empty!')
			),
			'validValue' => array(
				'rule' => array('inList', array(
					1 => 'product',
					2 => 'kit',
					3 => 'material',
					4 => 'semi_product',
					5 => 'service_product',
					6 => 'service_supplier',
					7 => 'consumable',
					8 => 'inventory',
					9 => 'goods',
					10 => 'other'
				)),
				'message' => array('Enter a valid class')
			)
		)
	);

	//Name says everything
	public function getAll() {
		$data = $this->find('all');
		return $data;
	}

	public function getByName($name) {
		$data = $this->find('first',
		array('conditions' => array(
			'ItemType.name' => $name
			))
		);
		return $data;
	}

/*
	Returns all classes
*/
	public function getClasses() {
		return $this->classes;
	}

	public function checkDeletePosibility($itemType) {
		$item = ClassRegistry::init('Item');
		$data = $item->find('count', array(
			'conditions' => array(
				'Item.item_type' => $itemType
			)
		)
	);
	if ($data==0) {
		return true;
	}
	return false;
	}

	/*
		Get all associated data on this model
	*/
		public function getCompleteData($id) {
			$data = $this->find(
			 'first', array(
				 'conditions' => array(
					 'ItemType.id' => $id
				 )
			 )
		 );
			return $data;
		}

		/*
			Returns an array with all types that belong to the given class
		*/
		public function getTypesByClass($class) {
			$data = $this->find('all', array(
					'conditions' => array('ItemType.class' => $class)
				)
			);
			return $data;
		}

		/*
			Preparing all fields for save.ctp
			Input arguments are the pointer for controller and id
		*/
		public function prepareDataForSave($controller, $id) {
			$controller->set('classOptions', $this->classes);
			if ($this->exists($id)) {
				$comData = $this->getCompleteData($id);
				$controller->set('currentCode', $comData['ItemType']['code']);
 			 $controller->set('currentName', $comData['ItemType']['name']);
 			 $controller->set('currentClass', array_flip($this->classes)[$comData['ItemType']['class']]);
 			 $controller->set('currentTangible', $comData['ItemType']['tangible']);
 			 $controller->set('currentActive', $comData['ItemType']['active']);
			} else {
				$controller->set('currentCode', null);
 			 $controller->set('currentName', null);
 			 $controller->set('currentClass', null);
 			 $controller->set('currentTangible', null);
 			 $controller->set('currentActive', null);
			}
		}

		public function fetchDataForSave($data, $id) {
			$saveData = null;
			if ($this->exists($id)) {
				$comData = $this->getCompleteData($id);
				$saveData = array(
					'ItemType' => array(
						'id' => $comData['ItemType']['id'],
						'code' => $data['ItemType']['code'],
						'name' => $data['ItemType']['name'],
						'class' => $this->classes[$data['ItemType']['class']],
						'tangible' => $data['ItemType']['tangible'],
						'active' => $data['ItemType']['active']
					)
				);
			} else {
				$saveData = array(
					'ItemType' => array(
						'code' => $data['ItemType']['code'],
						'name' => $data['ItemType']['name'],
						'class' => $this->classes[$data['ItemType']['class']],
						'tangible' => $data['ItemType']['tangible'],
						'active' => $data['ItemType']['active']
					)
				);
			}
			return $saveData;
		}
}
