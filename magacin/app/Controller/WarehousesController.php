<?php
App::uses('AppController', 'Controller');
/**
 * Warehouses Controller
 *
 * @property Warehouse $Warehouse
 * @property PaginatorComponent $Paginator
 */
class WarehousesController extends AppController {

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
		$this->Warehouse->recursive = 0;
		$this->set('warehouses', $this->Paginator->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->Warehouse->exists($id)) {
			throw new NotFoundException(__('Invalid warehouse'));
		}
		$options = array('conditions' => array('Warehouse.' . $this->Warehouse->primaryKey => $id));
		$this->set('warehouse', $this->Warehouse->find('first', $options));
	}

	/**
	 * save method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	 public function save($id = null) {
		 $comData = null;
		 if ($this->Warehouse->exists($id)) {
			 $comData = $this->Warehouse->getCompleteData($id);
			 $this->set('name', $comData['Warehouse']['name']);
		 } else {
			 $this->set('name', null);
		 }
		 $saveData = array();
		 if ($this->request->is('post')) {
			 $data = $this->request->data;
			 if (!$this->Warehouse->exists($id)) {
				$this->Warehouse->create();
				$saveData = array(
					'Warehouse' => array(
						'name' => $data['Warehouse']['name'],
					)
				);
			 } else {
				 $saveData = array(
						'Warehouse' => array(
							'id' => $comData['Warehouse']['id'],
							'name' => $data['Warehouse']['name'],
						)
					);
			 }
			 if ($this->Warehouse->saveAssociated($saveData, array('validate' => true, 'deep' => true))) {
				 $this->Flash->success(__('The warehouse has been saved.'));
				 return $this->redirect(array('action' => 'index'));
			 } else {
				 $this->Flash->error(__('The warehouse could not be saved. Please, try again.'));
			 }
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
		if (!$this->Warehouse->exists($id)) {
			throw new NotFoundException(__('Invalid warehouse'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->Warehouse->checkDeletePossibility($id)) {
			if ($this->Warehouse->delete($id)) {
				$this->Flash->success(__('The warehouse has been deleted.'));
			} else {
				$this->Flash->error(__('The warehouse could not be deleted. Please, try again.'));
			}
		} else {
			$this->Flash->error(__('This warehouse contains items!'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
