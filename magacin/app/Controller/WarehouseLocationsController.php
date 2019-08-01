<?php
App::uses('AppController', 'Controller');
/**
 * WarehouseLocations Controller
 *
 * @property WarehouseLocation $WarehouseLocation
 * @property PaginatorComponent $Paginator
 */
class WarehouseLocationsController extends AppController {

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
		$this->WarehouseLocation->recursive = 0;
		$rawData = $this->Paginator->paginate();
		$this->loadModel('WareLocType');
		$this->loadModel('Warehouse');
		$activeTypes = array();
		$data = array();
		foreach($rawData as $d) {
			$warehouse = $this->Warehouse->getCompleteData($d['WarehouseLocation']['warehouse']);
			$d['WarehouseLocation']['warehouse'] = $warehouse['Warehouse']['name'];
			array_push($data, $d);
			$activeClasses = $this->WareLocType->getClassesForLocation($d['WarehouseLocation']['id']);
			$text = "";
			foreach($activeClasses as $ac) {
				$text .= $ac;
				$text .= ",";
			}
			$text = substr($text, 0, -1);
			array_push($activeTypes, $text);
		}
		$this->set('warehouseLocations', $data);
		$this->set('activeTypes', $activeTypes);
	}

	/**
	 * view method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
		public function view($id = null) {
			if (!$this->WarehouseLocation->exists($id)) {
				throw new NotFoundException(__('Invalid measurement unit'));
			}
			$options = array('conditions' => array('WarehouseLocation.' . $this->WarehouseLocation->primaryKey => $id));
			$this->set('warehouseLocation', $this->WarehouseLocation->find('first', $options));
		}

	/**
	 * save method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	 public function save($id = null) {
		 $this->loadModel('Warehouse');
		 $this->loadModel('WareLocType');
		 $comData = null;
		 $classes = $this->WarehouseLocation->getClasses();
		 $this->set('warehouseOptions', $this->Warehouse->getAllAsArray());
		 $this->set('classes', $classes);
		 foreach($classes as $c) {
			 $this->set($c . 'Default', false);
		 }
		 if ($this->WarehouseLocation->exists($id)) {
			 $comData = $this->WarehouseLocation->getCompleteData($id);
			 $activeClasses = $this->WareLocType->getClassesForLocation($id);
			 $this->set('code', $comData['WarehouseLocation']['code']);
			 $this->set('name', $comData['WarehouseLocation']['name']);
			 $this->set('description', $comData['WarehouseLocation']['description']);
			 $this->set('default', $comData['WarehouseLocation']['default']);
			 $this->set('active', $comData['WarehouseLocation']['active']);
			 $this->set('warehouseDefault', $comData['WarehouseLocation']['warehouse']);
			 foreach($activeClasses as $ac) {
				 $this->set($ac . 'Default', true);
			 }
		 } else {
			 $this->set('code', null);
			 $this->set('name', null);
			 $this->set('description', null);
			 $this->set('default', null);
			 $this->set('active', null);
			 $this->set('warehouseDefault', null);
		 }
		 $saveData = array();
		 if ($this->request->is('post')) {
			 $data = $this->request->data;
			 if (!$this->WarehouseLocation->exists($id)) {
				$this->WarehouseLocation->create();
				$saveData = array(
					'WarehouseLocation' => array(
						'code' => $data['WarehouseLocation']['code'],
						'name' => $data['WarehouseLocation']['name'],
						'warehouse' => $data['WarehouseLocation']['warehouse'],
						'description' => $data['WarehouseLocation']['description'],
						'default' => $data['WarehouseLocation']['default'],
						'active' => $data['WarehouseLocation']['active']
					)
				);
			 } else {
				 $saveData = array(
						'WarehouseLocation' => array(
							'id' => $comData['WarehouseLocation']['id'],
							'code' => $data['WarehouseLocation']['code'],
							'name' => $data['WarehouseLocation']['name'],
							'warehouse' => $data['WarehouseLocation']['warehouse'],
							'description' => $data['WarehouseLocation']['description'],
							'default' => $data['WarehouseLocation']['default'],
							'active' => $data['WarehouseLocation']['active']
						)
					);
			 }
			 if ($this->WarehouseLocation->saveAssociated($saveData, array('validate' => true, 'deep' => true))) {
				 //Saving the type
				 if ($id==null) {
					 	$id = $this->WarehouseLocation->getInsertID();
				 }
				 $revClasses = array_flip($classes);
				 foreach($classes as $class) {
					 if ($data['WarehouseLocation'][$class] && !$this->WareLocType->checkExistence($id, $class)) {
						 $saveData = array(
							 'WareLocType' => array(
								 'location' => $id,
								 'type' => $revClasses[$class]
							 )
						 );
						 $this->WareLocType->saveAssociated($saveData, array('validate' => true, 'deep' => true));
					 } else if (!$data['WarehouseLocation'][$class] && $this->WareLocType->checkExistence($id, $class)) {
						 $this->WareLocType->deleteItem($id, $class);
					 }
				 }
				 $this->Flash->success(__('The warehouse location has been saved.'));
				 return $this->redirect(array('action' => 'index'));
			 } else {
				 $this->Flash->error(__('The warehouse location could not be saved. Please, try again.'));
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
		if (!$this->WarehouseLocation->exists($id)) {
			throw new NotFoundException(__('Invalid warehouse location'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->WarehouseLocation->checkDeletePossibility($id)) {
			if ($this->WarehouseLocation->delete($id)) {
				$this->Flash->success(__('The warehouse location has been deleted.'));
			} else {
				$this->Flash->error(__('The warehouse location could not be deleted. Please, try again.'));
			}
		} else {
			$this->Flash->error(__('This location contains items!'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
