<?php
App::uses('AppController', 'Controller');
App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel/Classes/PHPExcel.php'));
/**
 * SemiProducts Controller
 *
 * @property SemiProduct $SemiProduct
 * @property PaginatorComponent $Paginator
 */
class SemiProductsController extends AppController {

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
		$this->SemiProduct->recursive = 0;
		$this->set('semiProducts', $this->Paginator->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->SemiProduct->exists($id)) {
			throw new NotFoundException(__('Invalid semi product'));
		}
		$options = array('conditions' => array('SemiProduct.' . $this->SemiProduct->primaryKey => $id));
		$this->set('semiProduct', $this->SemiProduct->find('first', $options));
	}

	/**
	 * save method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	 public function save($id = null) {
		 $statusOp = $this->SemiProduct->prepareStatus();
		 $this->set('unitOptions', $this->SemiProduct->prepareMeasurementUnits($id));
		 $this->set('statusOptions', $statusOp);
		 $this->set('typeOptions', $this->SemiProduct->prepareTypes($id));
		 $comData = null;
		 if ($this->SemiProduct->exists($id)) {
			 $comData = $this->SemiProduct->getCompleteData($id);
			 $this->set('name', $comData['Item']['name']);
			 $this->set('description', $comData['Item']['description']);
			 $this->set('weight', $comData['Item']['weight']);
			 $this->set('currentUnit', $comData['Item']['measurement_unit']);
			 $this->set('currentType', $comData['Item']['item_type']);
			 $this->set('currentService', $comData['SemiProduct']['service_production']);
			 $this->set('currentStatus', array_flip($statusOp)[$comData['SemiProduct']['semi_product_status']]);
		 } else {
			 $this->set('name', null);
			 $this->set('description', null);
			 $this->set('weight', null);
			 $this->set('currentUnit', null);
			 $this->set('currentType', null);
			 $this->set('currentService', null);
			 $this->set('currentStatus', null);
		 }
		 $saveData = array();
		 if ($this->request->is('post')) {
			 $data = $this->request->data;
			 $this->loadModel('Item');
			 if (!$this->SemiProduct->exists($id)) {
				$this->SemiProduct->create();
				$saveData = array(
					'Item' => array(
						'code' => $this->Item->generateCode($data['SemiProduct']['item_type']),
						'name' => $data['SemiProduct']['name'],
						'description' => $data['SemiProduct']['description'],
						'weight' => $data['SemiProduct']['weight'],
						'measurement_unit' => $data['SemiProduct']['measurement_unit'],
						'item_type' => $data['SemiProduct']['item_type']
					),
					'SemiProduct' => array(
						'semi_product_status' => $data['SemiProduct']['semi_product_status'],
						'service_production' => $data['SemiProduct']['service_production'],
					)
				);
			 } else {
				 $code = null;
				 if ($data['SemiProduct']['item_type']!=$comData['Item']['item_type']) {
					 $code = $this->Item->generateCode($data['SemiProduct']['item_type']);
				 } else {
					 $code = $comData['Item']['code'];
				 }
				 $saveData = array(
						'Item' => array(
							'id' => $comData['Item']['id'],
							'code' => $code,
							'name' => $data['SemiProduct']['name'],
							'description' => $data['SemiProduct']['description'],
							'weight' => $data['SemiProduct']['weight'],
							'measurement_unit' => $data['SemiProduct']['measurement_unit'],
							'item_type' => $data['SemiProduct']['item_type']
						),
						'SemiProduct' => array(
							'id' => $comData['SemiProduct']['id'],
							'item' => $comData['SemiProduct']['item'],
							'semi_product_status' => $data['SemiProduct']['semi_product_status'],
							'service_production' => $data['SemiProduct']['service_production'],
						)
					);
			 }
			 if ($this->SemiProduct->saveAssociated($saveData, array('validate' => true, 'deep' => true))) {
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
		if (!$this->SemiProduct->exists($id)) {
			throw new NotFoundException(__('Invalid semi product'));
		}
		$this->request->allowMethod('post', 'delete');
		$data = $this->SemiProduct->find(
			'first', array(
				'conditions' => array(
					'SemiProduct.id' => $id
				)
			)
		);
		if ($this->SemiProduct->delete($id)) {
			$this->loadModel('Item');
			$this->loadModel('MoveCardItem');
			$this->loadModel('WarehouseAddressItem');
			$this->MoveCardItem->deleteItems($data['Item']['id']);
			$this->WarehouseAddressItem->deleteItems($data['Item']['id']);
			$this->Item->deleteAll(array('Item.id' => $data['Item']['id']), false, false);
			$this->Flash->success(__('The semi product has been deleted.'));
		} else {
			$this->Flash->error(__('The semi product could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}

	/*
		Exports data to excel file
	*/
	public function outputExcel() {
		$fileName = 'semiproducts.xlsx';
		$this->layout = 'xls'; //this will use the no layout
		$this->autoRender = false;
		$this->response->type('application/vnd.ms-excel');
		$data = $this->SemiProduct->find('all');
		$objExcel = $this->SemiProduct->createExcel($data);
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
			$messages = $this->SemiProduct->loadFromExcel($uploadPath);
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
				$fileName = 'semiproducts.pdf';

				//Close and output PDF document
				$this->SemiProduct->createPDF()->Output($fileName, 'I');
			}
}
