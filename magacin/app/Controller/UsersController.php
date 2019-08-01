<?php
App::uses('AppController', 'Controller');
/**
 * Users Controller
 *
 * @property User $User
 * @property PaginatorComponent $Paginator
 */
class UsersController extends AppController {

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
		$this->User->recursive = 0;
		$this->set('users', $this->Paginator->paginate());
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
		if (!$this->User->exists($id)) {
			throw new NotFoundException(__('Invalid user'));
		}
		$options = array('conditions' => array('User.' . $this->User->primaryKey => $id));
		$this->set('user', $this->User->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->User->create();
			if ($this->User->save($this->request->data)) {
				$this->Flash->success(__('The user has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The user could not be saved. Please, try again.'));
			}
		}
		$groups = $this->User->Group->find('list');
		$this->set(compact('groups'));
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->User->exists($id)) {
			throw new NotFoundException(__('Invalid user'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->User->save($this->request->data)) {
				$this->Flash->success(__('The user has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The user could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('User.' . $this->User->primaryKey => $id));
			$this->request->data = $this->User->find('first', $options);
		}
		$groups = $this->User->Group->find('list');
		$this->set(compact('groups'));
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		if (!$this->User->exists($id)) {
			throw new NotFoundException(__('Invalid user'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->User->delete($id)) {
			$this->Flash->success(__('The user has been deleted.'));
		} else {
			$this->Flash->error(__('The user could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}

	/*
		Function for login
	*/
	public function login() {
		if ($this->request->is('post')) {
			if ($this->Session->read('Auth.User')) {
        $this->Session->setFlash('You are logged in!', 'Flash/success');
        return $this->redirect('/');
    }
			if ($this->Auth->login()) {
            return $this->redirect($this->Auth->redirectUrl());
        }
        $this->Session->setFlash(__('Your username or password was incorrect.', 'Flash/error'));
		}
	}

/*
	Returns if someone is logged in
*/
	public function checkLogin() {
			if ($this->Session->read('Auth.User')) {
				return true;
			}
			return false;
	}

	/*
		Function for logout
	*/
	public function logout() {
		$this->Session->setFlash('Good-Bye', 'Flash/success');
		$this->redirect($this->Auth->logout());
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
					$tmp['check'] = $this->Acl->check(array('model' => 'User', 'foreign_key' => $id), $alias);
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
					$this->Acl->allow(array('model' => 'User', 'foreign_key' => $id), $acoBefore['alias']);
				} else {
					$this->Acl->deny(array('model' => 'User', 'foreign_key' => $id), $acoBefore['alias']);
				}
			}
			$this->Flash->success(__('ACL table has been successfuly updated.'));
		}
	}
}
