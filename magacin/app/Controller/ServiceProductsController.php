<?php
App::uses('AppController', 'Controller');
/**
 * ServiceProducts Controller
 *
 * @property ServiceProduct $ServiceProduct
 * @property PaginatorComponent $Paginator
 */
class ServiceProductsController extends AppController {

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
		$this->ServiceProduct->recursive = 0;
		$this->set('serviceProducts', $this->Paginator->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->ServiceProduct->exists($id)) {
			throw new NotFoundException(__('Invalid service product'));
		}
		$options = array('conditions' => array('ServiceProduct.' . $this->ServiceProduct->primaryKey => $id));
		$this->set('serviceProduct', $this->ServiceProduct->find('first', $options));
	}

	/**
	 * save method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	 public function save($id = null) {
		 $statusOp = $this->ServiceProduct->prepareStatus();
		 $this->set('unitOptions', $this->ServiceProduct->prepareMeasurementUnits($id));
		 $this->set('statusOptions', $statusOp);
		 $this->set('typeOptions', $this->ServiceProduct->prepareTypes($id));
		 $comData = null;
		 $pidCheck = true;
		 if ($this->ServiceProduct->exists($id)) {
			 $comData = $this->ServiceProduct->getCompleteData($id);
			 if ($comData['ServiceProduct']['pid']>0) {
				 $pidCheck = false;
			 } else {
				 $this->set('pid', $comData['ServiceProduct']['pid']);
			 }
			 $this->set('name', $comData['Item']['name']);
			 $this->set('description', $comData['Item']['description']);
			 $this->set('weight', $comData['Item']['weight']);
			 $this->set('currentUnit', $comData['Item']['measurement_unit']);
			 $this->set('currentType', $comData['Item']['item_type']);
			 $this->set('hts', $comData['ServiceProduct']['hts_number']);
			 $this->set('tax', $comData['ServiceProduct']['tax_group']);
			 $this->set('eccn', $comData['ServiceProduct']['eccn']);
			 $this->set('releaseDate', $comData['ServiceProduct']['release_date']);
			 $this->set('distributors', $comData['ServiceProduct']['for_distributors']);
			 $this->set('project', $comData['ServiceProduct']['project']);
			 $this->set('currentStatus', array_flip($statusOp)[$comData['ServiceProduct']['service_status']]);
		 } else {
			 $this->set('name', null);
			 $this->set('description', null);
			 $this->set('weight', null);
			 $this->set('currentUnit', null);
			 $this->set('currentType', null);
			 $this->set('pid', $this->ServiceProduct->generateNextPID());
			 $this->set('hts', null);
			 $this->set('tax', null);
			 $this->set('eccn', null);
			 $this->set('releaseDate', null);
			 $this->set('distributors', null);
			 $this->set('project', null);
			 $this->set('currentStatus', null);
		 }
		 $this->set('pidCheck', $pidCheck);
		 $saveData = array();
		 if ($this->request->is('post')) {
			 $data = $this->request->data;
			 $this->loadModel('Item');
			 if (!$this->ServiceProduct->exists($id)) {
				$this->ServiceProduct->create();
				$saveData = array(
					'Item' => array(
						'code' => $this->Item->generateCode($data['ServiceProduct']['item_type']),
						'name' => $data['ServiceProduct']['name'],
						'description' => $data['ServiceProduct']['description'],
						'weight' => $data['ServiceProduct']['weight'],
						'measurement_unit' => $data['ServiceProduct']['measurement_unit'],
						'item_type' => $data['ServiceProduct']['item_type']
					),
					'ServiceProduct' => array(
						'pid' => $data['ServiceProduct']['pid'],
						'hts_number' => $data['ServiceProduct']['hts_number'],
						'tax_group' => $data['ServiceProduct']['tax_group'],
						'eccn' => $data['ServiceProduct']['eccn'],
						'release_date' => $data['ServiceProduct']['release_date'],
						'for_distributors' => $data['ServiceProduct']['for_distributors'],
						'service_status' => $data['ServiceProduct']['service_status'],
						'project' => $data['ServiceProduct']['project']
					)
				);
			 } else {
				 $code = null;
				 if ($data['ServiceProduct']['item_type']!=$comData['Item']['item_type']) {
					 $code = $this->Item->generateCode($data['ServiceProduct']['item_type']);
				 } else {
					 $code = $comData['Item']['code'];
				 }
				 $pid = null;
				 if ($comData['ServiceProduct']['pid']!=null) {
					 $pid = $comData['ServiceProduct']['pid'];
				 } else {
					 $pid = $data['ServiceProduct']['pid'];
				 }
				 $saveData = array(
						'Item' => array(
							'id' => $comData['Item']['id'],
							'code' => $code,
							'name' => $data['ServiceProduct']['name'],
							'description' => $data['ServiceProduct']['description'],
							'weight' => $data['ServiceProduct']['weight'],
							'measurement_unit' => $data['ServiceProduct']['measurement_unit'],
							'item_type' => $data['ServiceProduct']['item_type']
						),
						'ServiceProduct' => array(
							'id' => $comData['ServiceProduct']['id'],
							'item' => $comData['ServiceProduct']['item'],
							'service_status' => $data['ServiceProduct']['service_status'],
							'pid' => $pid,
							'hts_number' => $data['ServiceProduct']['hts_number'],
							'tax_group' => $data['ServiceProduct']['tax_group'],
							'eccn' => $data['ServiceProduct']['eccn'],
							'release_date' => $data['ServiceProduct']['release_date'],
							'for_distributors' => $data['ServiceProduct']['for_distributors'],
							'service_status' => $data['ServiceProduct']['service_status'],
							'project' => $data['ServiceProduct']['project']
						)
					);
			 }
			 if ($this->ServiceProduct->saveAssociated($saveData, array('validate' => true, 'deep' => true))) {
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
		if (!$this->ServiceProduct->exists($id)) {
			throw new NotFoundException(__('Invalid service product'));
		}
		$this->request->allowMethod('post', 'delete');
		$data = $this->ServiceProduct->find(
			'first', array(
				'conditions' => array(
					'ServiceProduct.id' => $id
				)
			)
		);
		if ($this->ServiceProduct->delete($id)) {
			$this->loadModel('Item');
			$this->loadModel('MoveCardItem');
			$this->loadModel('WarehouseAddressItem');
			$this->MoveCardItem->deleteItems($data['Item']['id']);
			$this->WarehouseAddressItem->deleteItems($data['Item']['id']);
			$this->Item->deleteAll(array('Item.id' => $data['Item']['id']), false, false);
			$this->Flash->success(__('The service product has been deleted.'));
		} else {
			$this->Flash->error(__('The service product could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}

	/*
		Exports data to excel file
	*/
	public function outputExcel() {
		$fileName = 'serviceproducts.xlsx';
		$this->layout = 'xls'; //this will use the no layout
		$this->autoRender = false;
		$this->response->type('application/vnd.ms-excel');
		$data = $this->ServiceProduct->find('all');
		$objExcel = $this->ServiceProduct->createExcel($data);
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
		$messages = $this->ServiceProduct->loadFromExcel($uploadPath);
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
			$fileName = 'serviceproducts.pdf';

			//Close and output PDF document
			$this->ServiceProduct->createPDF()->Output($fileName, 'I');
		}
}
