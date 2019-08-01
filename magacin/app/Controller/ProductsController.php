<?php
App::uses('AppController', 'Controller');
/**
 * Products Controller
 *
 * @property Product $Product
 * @property PaginatorComponent $Paginator
 */
class ProductsController extends AppController {

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
		$this->Product->recursive = 0;
		$this->set('products', $this->Paginator->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->Product->exists($id)) {
			throw new NotFoundException(__('Invalid product'));
		}
		$options = array('conditions' => array('Product.' . $this->Product->primaryKey => $id));
		$this->set('product', $this->Product->find('first', $options));
	}

	/**
	 * save method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	 public function save($id = null) {
		 $statusOp = $this->Product->prepareStatus();
		 $this->set('unitOptions', $this->Product->prepareMeasurementUnits($id));
		 $this->set('statusOptions', $statusOp);
		 $this->set('typeOptions', $this->Product->prepareTypes($id));
		 $comData = null;
		 $pidCheck = true;
		 if ($this->Product->exists($id)) {
			 $comData = $this->Product->getCompleteData($id);
			 if ($comData['Product']['pid']>0) {
				 $pidCheck = false;
			 } else {
				 $this->set('pid', $comData['Product']['pid']);
			 }
			 $this->set('name', $comData['Item']['name']);
			 $this->set('description', $comData['Item']['description']);
			 $this->set('weight', $comData['Item']['weight']);
			 $this->set('currentUnit', $comData['Item']['measurement_unit']);
			 $this->set('currentType', $comData['Item']['item_type']);
			 $this->set('hts', $comData['Product']['hts_number']);
			 $this->set('tax', $comData['Product']['tax_group']);
			 $this->set('eccn', $comData['Product']['product_eccn']);
			 $this->set('releaseDate', $comData['Product']['product_release_date']);
			 $this->set('distributors', $comData['Product']['for_distributors']);
			 $this->set('project', $comData['Product']['project']);
			 $this->set('currentStatus', array_flip($statusOp)[$comData['Product']['product_status']]);
			 $this->set('serviceProduction', $comData['Product']['service_production']);
		 } else {
			 $this->set('name', null);
			 $this->set('description', null);
			 $this->set('weight', null);
			 $this->set('currentUnit', null);
			 $this->set('currentType', null);
			 $this->set('pid', $this->Product->generateNextPID());
			 $this->set('hts', null);
			 $this->set('tax', null);
			 $this->set('eccn', null);
			 $this->set('releaseDate', null);
			 $this->set('distributors', null);
			 $this->set('project', null);
			 $this->set('currentStatus', null);
			 $this->set('serviceProduction', null);
		 }
		 $this->set('pidCheck', $pidCheck);
		 $saveData = array();
		 if ($this->request->is('post')) {
			 $data = $this->request->data;
			 $this->loadModel('Item');
			 if (!$this->Product->exists($id)) {
				$this->Product->create();
				$saveData = array(
					'Item' => array(
						'code' => $this->Item->generateCode($data['Product']['item_type']),
						'name' => $data['Product']['name'],
						'description' => $data['Product']['description'],
						'weight' => $data['Product']['weight'],
						'measurement_unit' => $data['Product']['measurement_unit'],
						'item_type' => $data['Product']['item_type']
					),
					'Product' => array(
						'pid' => $data['Product']['pid'],
						'hts_number' => $data['Product']['hts_number'],
						'tax_group' => $data['Product']['tax_group'],
						'product_eccn' => $data['Product']['product_eccn'],
						'product_release_date' => $data['Product']['product_release_date'],
						'for_distributors' => $data['Product']['for_distributors'],
						'product_status' => $data['Product']['product_status'],
						'service_production' => $data['Product']['service_production'],
						'project' => $data['Product']['project']
					)
				);
			 } else {
				 $code = null;
				 if ($data['Product']['item_type']!=$comData['Item']['item_type']) {
					 $code = $this->Item->generateCode($data['Product']['item_type']);
				 } else {
					 $code = $comData['Item']['code'];
				 }
				 $pid = null;
				 if ($comData['Product']['pid']!=null) {
					 $pid = $comData['Product']['pid'];
				 } else {
					 $pid = $data['Product']['pid'];
				 }
				 $saveData = array(
						'Item' => array(
							'id' => $comData['Item']['id'],
							'code' => $code,
							'name' => $data['Product']['name'],
							'description' => $data['Product']['description'],
							'weight' => $data['Product']['weight'],
							'measurement_unit' => $data['Product']['measurement_unit'],
							'item_type' => $data['Product']['item_type']
						),
						'Product' => array(
							'id' => $comData['Product']['id'],
							'item' => $comData['Product']['item'],
							'pid' => $pid,
							'hts_number' => $data['Product']['hts_number'],
							'tax_group' => $data['Product']['tax_group'],
							'product_eccn' => $data['Product']['product_eccn'],
							'product_release_date' => $data['Product']['product_release_date'],
							'for_distributors' => $data['Product']['for_distributors'],
							'product_status' => $data['Product']['product_status'],
							'service_production' => $data['Product']['service_production'],
							'project' => $data['Product']['project']
						)
					);
			 }
			 if ($this->Product->saveAssociated($saveData, array('validate' => true, 'deep' => true))) {
				 $this->Flash->success(__('The product has been saved.'));
				 return $this->redirect(array('action' => 'index'));
			 } else {
				 $this->Flash->error(__('The product could not be saved. Please, try again.'));
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
		if (!$this->Product->exists($id)) {
			throw new NotFoundException(__('Invalid product'));
		}
		$this->request->allowMethod('post', 'delete');
		$data = $this->Product->find(
			'first', array(
				'conditions' => array(
					'Product.id' => $id
				)
			)
		);
	if ($this->Product->delete($id, true)) {
			$this->loadModel('Item');
			$this->loadModel('MoveCardItem');
			$this->loadModel('WarehouseAddressItem');
			$this->MoveCardItem->deleteItems($data['Item']['id']);
			$this->WarehouseAddressItem->deleteItems($data['Item']['id']);
			$this->Item->deleteAll(array('Item.id' => $data['Item']['id']), false, false);
			$this->Flash->success(__('The product has been deleted.'));
		} else {
			$this->Flash->error(__('The product could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}

	/*
		Exports data to excel file
	*/
	public function outputExcel() {
		$fileName = 'products.xlsx';
		$this->layout = 'xls'; //this will use the no layout
		$this->autoRender = false;
		$this->response->type('application/vnd.ms-excel');
		$data = $this->Product->find('all');
		$objExcel = $this->Product->createExcel($data);
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
			$messages = $this->Product->loadFromExcel($uploadPath);
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
				$fileName = 'products.pdf';

				//Close and output PDF document
				$this->Product->createPDF()->Output($fileName, 'I');
			}
}
