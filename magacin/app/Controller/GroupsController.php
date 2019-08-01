<?php
App::uses('AppController', 'Controller');
/**
 * Groups Controller
 *
 * @property Group $Group
 * @property PaginatorComponent $Paginator
 */
class GroupsController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator');

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->Group->recursive = 0;
		$this->set('groups', $this->Paginator->paginate());
		$this->loadModel('User');
		$isAdmin = false;
		if ($this->User->getCompleteData($this->Auth->User('id'))['Group']['name']=='Administrators') {
			$isAdmin = true;
		}
		$this->set('admin', $isAdmin);
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->Group->exists($id)) {
			throw new NotFoundException(__('Invalid group'));
		}
		$options = array('conditions' => array('Group.' . $this->Group->primaryKey => $id));
		$this->set('group', $this->Group->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->Group->create();
			if ($this->Group->save($this->request->data)) {
				$this->Flash->success(__('The group has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The group could not be saved. Please, try again.'));
			}
		}
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->Group->exists($id)) {
			throw new NotFoundException(__('Invalid group'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->Group->save($this->request->data)) {
				$this->Flash->success(__('The group has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The group could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('Group.' . $this->Group->primaryKey => $id));
			$this->request->data = $this->Group->find('first', $options);
		}
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		if (!$this->Group->exists($id)) {
			throw new NotFoundException(__('Invalid group'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->Group->delete($id)) {
			$this->Flash->success(__('The group has been deleted.'));
		} else {
			$this->Flash->error(__('The group could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}

	public function beforeFilter() {
    parent::beforeFilter();

    // For CakePHP 2.1 and up
    // $this->Auth->allow();
	}

	/**
	 * privileges method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function privileges($id) {
		$acos = $this->Acl->Aco->children();
		$acoList = array();
		$aco = null;
		$acoParents = [null];
		$toRemove = 0;
		$curAlias = array();
		$tmp = ['alias' => null, 'depth' => 0, 'check' => false];
		while(count($acos)>0) {
			$check = false;
				for ($i=0;$i<count($acos);$i++) {
					if ($acos[$i]['Aco']['parent_id']==end($acoParents)) {
						array_push($acoParents, $acos[$i]['Aco']['id']);
						$aco = $acos[$i];
						$toRemove = $i;
						$check = true;
						break;
					}
				}
				if ($check) {
					//There is a child
					array_push($curAlias, $aco['Aco']['alias']);
					$alias = '';
					foreach($curAlias as $ca) {
						$alias .= $ca . '/';
					}
					$alias = substr($alias, 0, -1);
					$tmp['alias'] = $alias;
					$tmp['depth'] = count($curAlias)-1;
					$tmp['check'] = $this->Acl->check(array('model' => 'Group', 'foreign_key' => $id), $alias);
					array_push($acoList, $tmp);
					array_splice($acos, $toRemove, 1);
				} else {
					//No more children
					array_pop($curAlias);
					array_pop($acoParents);
				}
		}
		$this->set('acos', $acoList);
		if ($this->request->is('post')) {
			$acos = $this->Acl->Aco->children();
			$data = $this->request->data;
			foreach($acoList as $acoBefore) {
				if ($acoBefore['check']==$data['Privileges'][$acoBefore['alias']]) {
					continue;
				}
				if ($data['Privileges'][$acoBefore['alias']]) {
					$this->Acl->allow(array('model' => 'Group', 'foreign_key' => $id), $acoBefore['alias']);
				} else {
					$this->Acl->deny(array('model' => 'Group', 'foreign_key' => $id), $acoBefore['alias']);
				}
			}
			$this->Flash->success(__('ACL table has been successfuly updated.'));
		}
	}
}
