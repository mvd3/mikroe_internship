<?php
App::uses('AppModel', 'Model');
App::uses('AuthComponent', 'Controller/Component');
/**
 * User Model
 *
 * @property Group $Group
 */
class User extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'username' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'password' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'group_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

	// The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Group' => array(
			'className' => 'Group',
			'foreignKey' => 'group_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	//Hashing the password
	public function beforeSave($options = array()) {
        $this->data['User']['password'] = AuthComponent::password(
          $this->data['User']['password']
        );
        return true;
    }

		public $actsAs = array('Acl' => array('type' => 'requester'));

		public function parentNode() {
        if (!$this->id && empty($this->data)) {
            return null;
        }
        if (isset($this->data['User']['group_id'])) {
            $groupId = $this->data['User']['group_id'];
        } else {
            $groupId = $this->field('group_id');
        }
        if (!$groupId) {
            return null;
        }
        return array('Group' => array('id' => $groupId));
    }

		/*
			Returns an array with all the operators
			Array is in form id => name
		*/
		public function getOperators() {
			$group = ClassRegistry::init('Group');
			$groupOperators = $group->getIdByName('Operators');
			$groupClerks = $group->getIdByName('Warehouse Clerks');
			$rawData = $this->find('all', array(
				'fields' => 'id, name',
				'conditions' => array('OR' => array(array('User.group_id' => $groupOperators), array('User.group_id' => $groupClerks)))
			));
			$keys = array();
			$values = array();
			if ($rawData!=null) {
				foreach($rawData as $data) {
					array_push($keys, $data['User']['id']);
					array_push($values, $data['User']['name']);
				}
			}
			return array_combine($keys, $values);
		}

		/*
			Returns all data from the user
			Input argument is the id
		*/
		public function getCompleteData($id) {
			$data = $this->find('first', array(
				'conditions' => array('User.id' => $id)
			));
			return $data;
		}

		/*
			Input argument is user id.
			Returns the role of the user in string format
		*/
		public function getRole($id) {
			$group = ClassRegistry::init('Group');
			$data = $this->find('first', array(
				'fields' => 'group_id',
				'conditions' => array('User.id' => $id)
			));
			return $group->getCompleteData($data['User']['group_id'])['Group']['name'];
		}
}
