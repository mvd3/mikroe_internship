<?php
App::uses('AppController', 'Controller');
/**
 * ServiceSuppliers Controller
 *
 * @property ServiceSupplier $ServiceSupplier
 * @property PaginatorComponent $Paginator
 */
class ServiceSuppliersController extends AppController {

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
		$this->ServiceSupplier->recursive = 0;
		$this->set('serviceSuppliers', $this->Paginator->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->ServiceSupplier->exists($id)) {
			throw new NotFoundException(__('Invalid service supplier'));
		}
		$options = array('conditions' => array('ServiceSupplier.' . $this->ServiceSupplier->primaryKey => $id));
		$this->set('serviceSupplier', $this->ServiceSupplier->find('first', $options));
	}

	/**
	 * save method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	 public function save($id = null) {
		 $statusOp = $this->ServiceSupplier->prepareStatus();
		 $ratingOp = $this->ServiceSupplier->prepareRecommendation();
		 $this->set('unitOptions', $this->ServiceSupplier->prepareMeasurementUnits($id));
		 $this->set('statusOptions', $statusOp);
		 $this->set('typeOptions', $this->ServiceSupplier->prepareTypes($id));
		 $this->set('recommendationOptions', $ratingOp);
		 $comData = null;
		 if ($this->ServiceSupplier->exists($id)) {
			 $comData = $this->ServiceSupplier->getCompleteData($id);
			 $this->set('name', $comData['Item']['name']);
			 $this->set('description', $comData['Item']['description']);
			 $this->set('weight', $comData['Item']['weight']);
			 $this->set('currentUnit', $comData['Item']['measurement_unit']);
			 $this->set('currentType', $comData['Item']['item_type']);
			 $this->set('currentStatus', array_flip($statusOp)[$comData['ServiceSupplier']['service_status']]);
			 $this->set('currentRecommendation', array_flip($ratingOp)[$comData['ServiceSupplier']['service_rating']]);
		 } else {
			 $this->set('name', null);
			 $this->set('description', null);
			 $this->set('weight', null);
			 $this->set('currentUnit', null);
			 $this->set('currentType', null);
			 $this->set('currentStatus', null);
			 $this->set('currentRecommendation', null);
		 }
		 $saveData = array();
		 if ($this->request->is('post')) {
			 $data = $this->request->data;
			 $this->loadModel('Item');
			 if (!$this->ServiceSupplier->exists($id)) {
				$this->ServiceSupplier->create();
				$saveData = array(
					'Item' => array(
						'code' => $this->Item->generateCode($data['ServiceSupplier']['item_type']),
						'name' => $data['ServiceSupplier']['name'],
						'description' => $data['ServiceSupplier']['description'],
						'weight' => $data['ServiceSupplier']['weight'],
						'measurement_unit' => $data['ServiceSupplier']['measurement_unit'],
						'item_type' => $data['ServiceSupplier']['item_type']
					),
					'ServiceSupplier' => array(
						'service_status' => $data['ServiceSupplier']['service_status'],
						'service_rating' => $data['ServiceSupplier']['service_rating']
					)
				);
			 } else {
				 $code = null;
				 if ($data['ServiceSupplier']['item_type']!=$comData['Item']['item_type']) {
					 $code = $this->Item->generateCode($data['ServiceSupplier']['item_type']);
				 } else {
					 $code = $comData['Item']['code'];
				 }
				 $saveData = array(
						'Item' => array(
							'id' => $comData['Item']['id'],
							'code' => $code,
							'name' => $data['ServiceSupplier']['name'],
							'description' => $data['ServiceSupplier']['description'],
							'weight' => $data['ServiceSupplier']['weight'],
							'measurement_unit' => $data['ServiceSupplier']['measurement_unit'],
							'item_type' => $data['ServiceSupplier']['item_type']
						),
						'ServiceSupplier' => array(
							'id' => $comData['ServiceSupplier']['id'],
							'item' => $comData['ServiceSupplier']['item'],
							'service_status' => $data['ServiceSupplier']['service_status'],
							'service_rating' => $data['ServiceSupplier']['service_rating']
						)
					);
			 }
			 if ($this->ServiceSupplier->saveAssociated($saveData, array('validate' => true, 'deep' => true))) {
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
		if (!$this->ServiceSupplier->exists($id)) {
			throw new NotFoundException(__('Invalid service supplier'));
		}
		$this->request->allowMethod('post', 'delete');
		$data = $this->ServiceSupplier->find(
			'first', array(
				'conditions' => array(
					'ServiceSupplier.id' => $id
				)
			)
		);
		if ($this->ServiceSupplier->delete($id)) {
			$this->loadModel('Item');
			$this->loadModel('MoveCardItem');
			$this->loadModel('WarehouseAddressItem');
			$this->MoveCardItem->deleteItems($data['Item']['id']);
			$this->WarehouseAddressItem->deleteItems($data['Item']['id']);
			$this->Item->deleteAll(array('Item.id' => $data['Item']['id']), false, false);
			$this->Flash->success(__('The consumable has been deleted.'));
		} else {
			$this->Flash->error(__('The service supplier could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}

	/*
		Exports data to excel file
	*/
	public function outputExcel() {
		$fileName = 'servicesuppliers.xlsx';
		$this->layout = 'xls'; //this will use the no layout
		$this->autoRender = false;
		$this->response->type('application/vnd.ms-excel');
		$data = $this->ServiceSupplier->find('all');
		$objExcel = $this->ServiceSupplier->createExcel($data);
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
			$messages = $this->ServiceSupplier->loadFromExcel($uploadPath);
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
				$fileName = 'servicesuppliers.pdf';

				//Close and output PDF document
				$this->ServiceSupplier->createPDF()->Output($fileName, 'I');
			}
}
