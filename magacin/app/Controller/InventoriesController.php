<?php
App::uses('AppController', 'Controller');
/**
 * Inventories Controller
 *
 * @property Inventory $Inventory
 * @property PaginatorComponent $Paginator
 */
class InventoriesController extends AppController {

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
		$this->Inventory->recursive = 0;
		$this->set('inventories', $this->Paginator->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->Inventory->exists($id)) {
			throw new NotFoundException(__('Invalid inventory'));
		}
		$options = array('conditions' => array('Inventory.' . $this->Inventory->primaryKey => $id));
		$this->set('inventory', $this->Inventory->find('first', $options));
	}

	/**
	 * save method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	 public function save($id = null) {
		 $statusOp = $this->Inventory->prepareStatus();
		 $ratingOp = $this->Inventory->prepareRecommendation();
		 $this->set('unitOptions', $this->Inventory->prepareMeasurementUnits($id));
		 $this->set('statusOptions', $statusOp);
		 $this->set('typeOptions', $this->Inventory->prepareTypes($id));
		 $this->set('recommendationOptions', $ratingOp);
		 $comData = null;
		 if ($this->Inventory->exists($id)) {
			 $comData = $this->Inventory->getCompleteData($id);
			 $this->set('name', $comData['Item']['name']);
			 $this->set('description', $comData['Item']['description']);
			 $this->set('weight', $comData['Item']['weight']);
			 $this->set('currentUnit', $comData['Item']['measurement_unit']);
			 $this->set('currentType', $comData['Item']['item_type']);
			 $this->set('type', $comData['Inventory']['type']);
			 $this->set('currentStatus', array_flip($statusOp)[$comData['Inventory']['status']]);
			 $this->set('currentRecommendation', array_flip($ratingOp)[$comData['Inventory']['recommended rating']]);
		 } else {
			 $this->set('name', null);
			 $this->set('description', null);
			 $this->set('weight', null);
			 $this->set('currentUnit', null);
			 $this->set('currentType', null);
			 $this->set('type', null);
			 $this->set('currentStatus', null);
			 $this->set('currentRecommendation', null);
		 }
		 $saveData = array();
		 if ($this->request->is('post')) {
			 $data = $this->request->data;
			 $this->loadModel('Item');
			 if (!$this->Inventory->exists($id)) {
				$this->Inventory->create();
				$saveData = array(
					'Item' => array(
						'code' => $this->Item->generateCode($data['Inventory']['item_type']),
						'name' => $data['Inventory']['name'],
						'description' => $data['Inventory']['description'],
						'weight' => $data['Inventory']['weight'],
						'measurement_unit' => $data['Inventory']['measurement_unit'],
						'item_type' => $data['Inventory']['item_type']
					),
					'Inventory' => array(
						'type' => $data['Inventory']['type'],
						'status' => $data['Inventory']['status'],
						'recommended rating' => $data['Inventory']['recommended rating'],
					)
				);
			 } else {
				 $code = null;
				 if ($data['Inventory']['item_type']!=$comData['Item']['item_type']) {
					 $code = $this->Item->generateCode($data['Inventory']['item_type']);
				 } else {
					 $code = $comData['Item']['code'];
				 }
				 $saveData = array(
						'Item' => array(
							'id' => $comData['Item']['id'],
							'code' => $code,
							'name' => $data['Inventory']['name'],
							'description' => $data['Inventory']['description'],
							'weight' => $data['Inventory']['weight'],
							'measurement_unit' => $data['Inventory']['measurement_unit'],
							'item_type' => $data['Inventory']['item_type']
						),
						'Inventory' => array(
							'id' => $comData['Inventory']['id'],
							'item' => $comData['Inventory']['item'],
							'type' => $data['Inventory']['type'],
							'consumable_status' => $data['Inventory']['status'],
							'recommended rating' => $data['Inventory']['recommended rating'],
						)
					);
			 }
			 if ($this->Inventory->saveAssociated($saveData, array('validate' => true, 'deep' => true))) {
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
		if (!$this->Inventory->exists($id)) {
			throw new NotFoundException(__('Invalid inventory'));
		}
		$this->request->allowMethod('post', 'delete');
		$data = $this->Inventory->find(
			'first', array(
				'conditions' => array(
					'Inventory.id' => $id
				)
			)
		);
		if ($this->Inventory->delete($id)) {
			$this->loadModel('Item');
			$this->loadModel('MoveCardItem');
			$this->loadModel('WarehouseAddressItem');
			$this->MoveCardItem->deleteItems($data['Item']['id']);
			$this->WarehouseAddressItem->deleteItems($data['Item']['id']);
			$this->Item->deleteAll(array('Item.id' => $data['Item']['id']), false, false);
			$this->Flash->success(__('The inventory has been deleted.'));
		} else {
			$this->Flash->error(__('The inventory could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}

	/*
		Exports data to excel file
	*/
	public function outputExcel() {
		$fileName = 'inventories.xlsx';
		$this->layout = 'xls'; //this will use the no layout
		$this->autoRender = false;
		$this->response->type('application/vnd.ms-excel');
		$data = $this->Inventory->find('all');
		$objExcel = $this->Inventory->createExcel($data);
		$objExcelWriter = PHPExcel_IOFactory::createWriter($objExcel, 'Excel2007');
		$this->set('objExcel', $objExcel);
    $this->set('objExcelWriter', $objExcelWriter);
		$this->set('filename', $fileName);
    $this->render('index_excel');
	}

	/*
		Imports data from excel file
	*/
		public function inputExcel() {
			$filename = '';
			$uploadData = $this->data['uploadFile']['file_path'];
			if ( $uploadData['size'] == 0 || $uploadData['error'] !== 0) {
				$this->Flash->error(__('Upload a file before importing!'));
				return $this->redirect(array('action' => 'index'));
			}
			$filename = basename($uploadData['name']);
			$uploadFolder = WWW_ROOT. 'excel';
			$filename = time() .'_'. $filename;
			$uploadPath =  $uploadFolder . DS . $filename;
			if( !file_exists($uploadFolder) ){
				mkdir($uploadFolder);
			}
			if (!move_uploaded_file($uploadData['tmp_name'], $uploadPath)) {
				return false;
			}
			$messages = $this->Inventory->loadFromExcel($uploadPath);
			foreach($messages['Success'] as $message) {
				$this->Flash->success(__($message));
			}
			foreach($messages['Error'] as $message) {
				$this->Flash->error(__($message));
			}
			return $this->redirect(array('action' => 'index'));
		}

		/*
			Generates PDF file with table and content
		*/
			public function outputPDF() {
				$fileName = 'inventories.pdf';

				//Close and output PDF document
				$this->Inventory->createPDF()->Output($fileName, 'I');
			}
}
