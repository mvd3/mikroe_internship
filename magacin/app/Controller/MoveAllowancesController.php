<?php
App::uses('AppController', 'Controller');
/**
 * MoveAllowances Controller
 *
 * @property MoveAllowance $MoveAllowance
 * @property PaginatorComponent $Paginator
 */
class MoveAllowancesController extends AppController {

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
		$this->loadModel('WarehouseLocation');
		$this->loadModel('User');
		$this->loadModel('Group');
		$this->MoveAllowance->recursive = 0;
		$rawData = $this->Paginator->paginate();
		$data = array();
		foreach($rawData as $d) {
			$location = $this->WarehouseLocation->getCompleteData($d['MoveAllowance']['warehouse_location']);
			$user = $this->User->getCompleteData($d['MoveAllowance']['operator']);
			$group = $this->Group->getCompleteData($user['User']['group_id']);
			$d['MoveAllowance']['warehouse_location'] = $location['WarehouseLocation']['name'];
			$d['MoveAllowance']['operator'] = $user['User']['name'];
			$d['MoveAllowance']['role'] = $group['Group']['name'];
			array_push($data, $d);
		}
		$this->set('moveAllowances', $data);
	}

/**
 * save method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function save() {
		$this->loadModel('WarehouseLocation');
		$this->loadModel('User');
		$locationOp = $this->WarehouseLocation->getAll();
		$operatorOp = $this->User->getOperators();
		$this->set('operatorOptions', $operatorOp);
		$this->set('locationOptions', $locationOp);
		$this->set('operatorDefault', null);
		$this->set('locationSelected', null);
		$this->set('allowanceDefault', null);
		$saveData = array();
		if ($this->request->is('post')) {
			$data = $this->request->data;
			if (!$this->MoveAllowance->checkExistence($data['MoveAllowance']['operator'], $data['MoveAllowance']['warehouse_location'])) {
				$this->MoveAllowance->create();
				$saveData = array(
					'MoveAllowance' => array(
						'operator' => $data['MoveAllowance']['operator'],
						'warehouse_location' => $data['MoveAllowance']['warehouse_location'],
						'allowance' => $data['MoveAllowance']['allowance']
					)
				);
				if ($this->MoveAllowance->saveAssociated($saveData, array('validate' => true, 'deep' => true))) {
					$this->Flash->success(__('The allowance has been saved.'));
					return $this->redirect(array('action' => 'index'));
				} else {
					$this->Flash->error(__('The allowance could not be saved. Please, try again.'));
				}
			} else {
				$this->Flash->error(__('The allowance is already in the database. Please, try again.'));
			}
		}
	}

	/**
	 * change method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function change($id) {
		$comData = $this->MoveAllowance->getCompleteData($id);
		$comData['MoveAllowance']['allowance'] = !$comData['MoveAllowance']['allowance'];
		if ($this->MoveAllowance->saveAssociated($comData, array('validate' => true, 'deep' => true))) {
			$this->Flash->success(__('The allowance has been changed.'));
			return $this->redirect(array('action' => 'index'));
		} else {
			$this->Flash->error(__('The allowance could not be changed. Please, try again.'));
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
		if (!$this->MoveAllowance->exists($id)) {
			throw new NotFoundException(__('Invalid move allowance'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->MoveAllowance->delete($id)) {
			$this->Flash->success(__('The move allowance has been deleted.'));
		} else {
			$this->Flash->error(__('The move allowance could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
