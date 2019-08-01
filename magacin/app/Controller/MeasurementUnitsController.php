<?php
App::uses('AppController', 'Controller');
/**
 * MeasurementUnits Controller
 *
 * @property MeasurementUnit $MeasurementUnit
 * @property PaginatorComponent $Paginator
 */
class MeasurementUnitsController extends AppController {

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
		$this->MeasurementUnit->recursive = 0;
		$this->set('measurementUnits', $this->Paginator->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->MeasurementUnit->exists($id)) {
			throw new NotFoundException(__('Invalid measurement unit'));
		}
		$options = array('conditions' => array('MeasurementUnit.' . $this->MeasurementUnit->primaryKey => $id));
		$this->set('measurementUnit', $this->MeasurementUnit->find('first', $options));
	}

	/**
	 * save method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	 public function save($id = null) {
		 $this->MeasurementUnit->prepareDataForSave($this, $id);
		 $saveData = array();
		 if ($this->request->is('post')) {
			 $data = $this->request->data;
			 if (!$this->MeasurementUnit->exists($id)) {
				 $this->MeasurementUnit->create();
			 }
			 $saveData = $this->MeasurementUnit->fetchDataForSave($data, $id);
			 if ($this->MeasurementUnit->saveAssociated($saveData, array('validate' => true, 'deep' => true))) {
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
		if (!$this->MeasurementUnit->exists($id)) {
			throw new NotFoundException(__('Invalid measurement unit'));
		}
		if (!$this->MeasurementUnit->checkDeletePosibility($id)) {
			$this->Flash->error(__('The measurement is still used!'));
		} else {
			$this->request->allowMethod('post', 'delete');
			if ($this->MeasurementUnit->delete($id)) {
				$this->Flash->success(__('The measurement unit has been deleted.'));
			} else {
				$this->Flash->error(__('The measurement unit could not be deleted. Please, try again.'));
			}
	}
		return $this->redirect(array('action' => 'index'));
	}
}
