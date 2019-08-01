<?php
App::uses('AppController', 'Controller');
/**
 * WarehouseAddresses Controller
 *
 * @property WarehouseAddress $WarehouseAddress
 * @property PaginatorComponent $Paginator
 */
class WarehouseAddressesController extends AppController {

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
		$this->WarehouseAddress->recursive = 0;
		$rawData = $this->Paginator->paginate();
		//Switch warehouse location id with code name of the location
		for ($i=0;$i<count($rawData);$i++) {
			$rawData[$i]['WarehouseAddress']['warehouse_location'] = $this->WarehouseLocation->getCompleteData($rawData[$i]['WarehouseAddress']['warehouse_location'])['WarehouseLocation']['code'];
		}
		$this->set('warehouseAddresses', $rawData);
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->WarehouseAddress->exists($id)) {
			throw new NotFoundException(__('Invalid warehouse address'));
		}
		$options = array('conditions' => array('WarehouseAddress.' . $this->WarehouseAddress->primaryKey => $id));
		$this->set('warehouseAddress', $this->WarehouseAddress->find('first', $options));
	}

	/**
	 * save method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	 public function save($id = null) {
		 $this->loadModel('WarehouseLocation');
		 $comData = null;
		 $locationOp = $this->WarehouseLocation->getAll();
		 $this->set('locationOptions', $locationOp);
		 if ($this->WarehouseAddress->exists($id)) {
			 $comData = $this->WarehouseAddress->getCompleteData($id);
			 $this->set('rowDefault', $comData['WarehouseAddress']['row']);
			 $this->set('shelfDefault', $comData['WarehouseAddress']['shelf']);
			 $this->set('bulkheadDefault', $comData['WarehouseAddress']['bulkhead']);
			 $this->set('locationDefault', $comData['WarehouseAddress']['warehouse_location']);
			 $this->set('activeDefault', $comData['WarehouseAddress']['active']);
		 } else {
			 $this->set('rowDefault', null);
			 $this->set('shelfDefault', null);
			 $this->set('bulkheadDefault', null);
			 $this->set('locationDefault', null);
			 $this->set('activeDefault', null);
		 }
		 $saveData = array();
		 if ($this->request->is('post')) {
			 $data = $this->request->data;
			 if (!$this->WarehouseAddress->exists($id)) {
				$this->WarehouseAddress->create();
				$code = $locationOp[$data['WarehouseAddress']['warehouse_location']] . '_' . $data['WarehouseAddress']['row'] . '_' . $data['WarehouseAddress']['shelf'] . '_' . $data['WarehouseAddress']['bulkhead'];
				$saveData = array(
					'WarehouseAddress' => array(
						'code' => $code,
						'row' => $data['WarehouseAddress']['row'],
						'shelf' => $data['WarehouseAddress']['shelf'],
						'bulkhead' => $data['WarehouseAddress']['bulkhead'],
						'warehouse_location' => $data['WarehouseAddress']['warehouse_location'],
						'barcode' => $this->WarehouseAddress->generateBarcode($data['WarehouseAddress']['warehouse_location'], $data['WarehouseAddress']['row'], $data['WarehouseAddress']['shelf'], $data['WarehouseAddress']['bulkhead']),
						'active' => $data['WarehouseAddress']['active']
					)
				);
			 } else {
				 $saveData = array(
						'WarehouseAddress' => array(
							'id' => $comData['WarehouseAddress']['id'],
							'code' => $comData['WarehouseAddress']['code'],
							'row' => $data['WarehouseAddress']['row'],
							'shelf' => $data['WarehouseAddress']['shelf'],
							'bulkhead' => $data['WarehouseAddress']['bulkhead'],
							'warehouse_location' => $data['WarehouseAddress']['warehouse_location'],
							'barcode' => $this->WarehouseAddress->generateBarcode($data['WarehouseAddress']['warehouse_location'], $data['WarehouseAddress']['row'], $data['WarehouseAddress']['shelf'], $data['WarehouseAddress']['bulkhead']),
							'active' => $data['WarehouseAddress']['active']
						)
					);
			 }
			 if ($this->WarehouseAddress->saveAssociated($saveData, array('validate' => true, 'deep' => true))) {
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
		if (!$this->WarehouseAddress->exists($id)) {
			throw new NotFoundException(__('Invalid warehouse address'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->WarehouseAddress->checkDeletePossibility($id)) {
			if ($this->WarehouseAddress->delete($id)) {
				$this->Flash->success(__('The warehouse address has been deleted.'));
			} else {
				$this->Flash->error(__('The warehouse address could not be deleted. Please, try again.'));
			}
		} else {
			$this->Flash->error(__('This address still contains items.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
