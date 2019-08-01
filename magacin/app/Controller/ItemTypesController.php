<?php
App::uses('AppController', 'Controller');
/**
 * ItemTypes Controller
 *
 * @property ItemType $ItemType
 * @property PaginatorComponent $Paginator
 */
class ItemTypesController extends AppController {

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
		$this->ItemType->recursive = 0;
		$this->set('itemTypes', $this->Paginator->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->ItemType->exists($id)) {
			throw new NotFoundException(__('Invalid item type'));
		}
		$options = array('conditions' => array('ItemType.' . $this->ItemType->primaryKey => $id));
		$this->set('itemType', $this->ItemType->find('first', $options));
	}

	/**
	 * save method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	 public function save($id = null) {
		 $this->ItemType->prepareDataForSave($this, $id);
		 $saveData = array();
		 if ($this->request->is('post')) {
			 $data = $this->request->data;
			 if (!$this->ItemType->exists($id)) {
				$this->ItemType->create();
			 }
			 $saveData = $this->ItemType->fetchDataForSave($data, $id);
			 if ($this->ItemType->saveAssociated($saveData, array('validate' => true, 'deep' => true))) {
				 $this->Flash->success(__('The consumable has been saved.'));
				 return $this->redirect(array('action' => 'index'));
			 } else {
				 $this->Flash->error(__('The consumable could not be saved. Please, try again.'));
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
		if (!$this->ItemType->exists($id)) {
			throw new NotFoundException(__('Invalid item type'));
		}
		$this->request->allowMethod('post', 'delete');
		if (!$this->ItemType->checkDeletePosibility($id)) {
			$this->Flash->error(__('The item type is still used.'));
			return $this->redirect(array('action' => 'index'));
		}
		if ($this->ItemType->delete($id)) {
			$this->Flash->success(__('The item type has been deleted.'));
		} else {
			$this->Flash->error(__('The item type could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
