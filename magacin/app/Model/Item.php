<?php
App::uses('AppModel', 'Model');
/**
 * Item Model
 *
 */
class Item extends AppModel {

	public $belongsTo = array(
		'MeasurementUnit' => array(
			'className' => 'MeasurementUnit',
			'foreignKey' => 'measurement_unit'
		),
		'ItemType' => array(
			'className' => 'ItemType',
			'foreignKey' => 'item_type'
		)
	);

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
				'on' => 'create',
				'message' => array('Name must be unique!')
			)
		),
		'measurement_unit' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Enter a valid measurement_unit!',
			),
		),
		'item_type' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Enter a valid item type!',
			),
		),
		'deleted' => array(
			'boolean' => array(
				'rule' => array('boolean'),
				'message' => 'How did you manage to screw this?',
			),
		),
	);

/*
Name says everything
*/
	public function isTangible($itemTypeID) {
		$data = $this->find(
			'first', array(
				'conditions' => array(
					'ItemType.id' => $itemTypeID
				)
			)
		);
			$res = $data['ItemType']['tangible'];
		return $res;
	}

/*
	Returns all item types from database
*/
	public function getItemTypes() {
		$itemType = ClassRegistry::init('ItemType');
		$rawData = $itemType->find('all', array(
				'fields' => 'id, name'
			)
		);
		$cnt = 0;
		$data = array();
		foreach($rawData as $raw) {
			$data[$raw['ItemType']['id']] = $raw['ItemType']['name'];
			$cnt++;
		}
		return $data;
	}

	/*
		Checks if the code already exists in the database.
		If that is the case returns true, else false.
	*/
		public function checkCodeExistence($code) {
			$result = $this->find('first', array(
					'fields' => 'COUNT(*) as num',
					'conditions' => array(
						'Item.code' => $code
					)
				)
			);
			if ($result[0]['num']>0) {
				return true;
			} else {
				return false;
			}
		}

/*
	Generate code for new item
*/
	public function generateCode($itemTypeID) {
		$itemType = ClassRegistry::init('ItemType');
		$result = $itemType->find('first', array(
			'conditions' => array(
				'ItemType.id' => $itemTypeID
			)
		)
	);
	$prefix = $result['ItemType']['code'];
	$result = $this->find('first', array(
			'fields' => 'COUNT(*) as num',
			'conditions' => array(
				'Item.item_type' => $itemTypeID
			)
		)
	);
	$num = $result[0]['num']+1;
	$code = $prefix . '-' . $num;
	while ($this->checkCodeExistence($code)) {
		$code = $prefix . '-' . $num++;
	}
	return $code;
	}

	//Return data for this id
		public function getCompleteData($id) {
		 $data = $this->find(
			'first', array(
				'conditions' => array(
					'Item.id' => $id
				)
			)
		);
		 return $data;
	 }

	 /*
		 Returns an array with all items that belong to the given type
	 */
	 public function getItemsByType($type) {
		 $data = $this->find('all', array(
				 'conditions' => array('Item.item_type' => $type)
			 )
		 );
		 return $data;
	 }

	 /*
	 	Sets custom options for paginator.
		Used when searching items
	 */
	 public function paginatorSearchOptions($paginator, $data) {
		 $cond = array(
			 'AND' => array(
				 'Item.item_type' => $data['Search']['item_type'],
				 'OR' => array(
					 'Item.code LIKE' => '%' . $data['Search']['query'] . '%',
					 'Item.name LIKE' => '%' . $data['Search']['query'] . '%',
					 'Item.description LIKE' => '%' . $data['Search']['query'] . '%'
				 )
			 )
		 );
		 $paginator->settings = array(
			 'conditions' => $cond,
			 'imit' => 20
		 );
	 }

	 /*
	 	Deletes items in all the other tables where a foreign key constraint exists.
		Input argument is id.
	 */
	 public function deleteConnections($id) {
		 $iter1 = ['Material', 'SemiProduct', 'Product', 'Good', 'Kit', 'Consumable', 'ServiceSupplier', 'ServiceProduct', 'Inventory'];
		 $iter2 = ['MoveCardItem', 'WarehouseAddressItem'];
		 foreach($iter1 as $model) {
			 $md = ClassRegistry::init($model);
			 $md->deleteAll(array($model . '.item' => $id), false, false);
		 }
		 foreach($iter2 as $model) {
			 $md = ClassRegistry::init($model);
			 $md->deleteItems($id);
		 }
	 }
}
