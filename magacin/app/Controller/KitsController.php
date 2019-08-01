<?php
App::uses('AppController', 'Controller');
/**
 * Kits Controller
 *
 * @property Kit $Kit
 * @property PaginatorComponent $Paginator
 */
class KitsController extends AppController {

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
		$this->Kit->recursive = 0;
		$this->set('kits', $this->Paginator->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->Kit->exists($id)) {
			throw new NotFoundException(__('Invalid kit'));
		}
		$options = array('conditions' => array('Kit.' . $this->Kit->primaryKey => $id));
		$this->set('kit', $this->Kit->find('first', $options));
	}

	/**
	 * save method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	 public function save($id = null) {
		 $statusOp = $this->Kit->prepareStatus();
		 $this->set('unitOptions', $this->Kit->prepareMeasurementUnits($id));
		 $this->set('statusOptions', $statusOp);
		 $this->set('typeOptions', $this->Kit->prepareTypes($id));
		 $comData = null;
		 $pidCheck = true;
		 if ($this->Kit->exists($id)) {
			 $comData = $this->Kit->getCompleteData($id);
			 if ($comData['Kit']['pid']>0) {
				 $pidCheck = false;
			 } else {
				 $this->set('pid', $comData['Kit']['pid']);
			 }
			 $this->set('name', $comData['Item']['name']);
			 $this->set('description', $comData['Item']['description']);
			 $this->set('weight', $comData['Item']['weight']);
			 $this->set('currentUnit', $comData['Item']['measurement_unit']);
			 $this->set('currentType', $comData['Item']['item_type']);
			 $this->set('currentHts', $comData['Kit']['hts_number']);
			 $this->set('currentTax', $comData['Kit']['tax_group']);
			 $this->set('currentEccn', $comData['Kit']['eccn']);
			 $this->set('currentDate', $comData['Kit']['kit_release_date']);
			 $this->set('currentDistributors', $comData['Kit']['for_distributors']);
			 $this->set('currentContent', $comData['Kit']['hide_kid_content']);
			 $this->set('currentStatus', array_flip($statusOp)[$comData['Kit']['kit_status']]);
		 } else {
			 $this->set('name', null);
			 $this->set('description', null);
			 $this->set('weight', null);
			 $this->set('currentUnit', null);
			 $this->set('currentType', null);
			 $this->set('pid', $this->Kit->generateNextPID());
			 $this->set('currentHts', null);
			 $this->set('currentTax', null);
			 $this->set('currentEccn', null);
			 $this->set('currentDate', null);
			 $this->set('currentDistributors', null);
			 $this->set('currentContent', null);
			 $this->set('currentStatus', null);
		 }
		 $this->set('pidCheck', $pidCheck);
		 $saveData = array();
		 if ($this->request->is('post')) {
			 $data = $this->request->data;
			 $this->loadModel('Item');
			 if (!$this->Kit->exists($id)) {
				$this->Kit->create();
				$saveData = array(
					'Item' => array(
						'code' => $this->Item->generateCode($data['Kit']['item_type']),
						'name' => $data['Kit']['name'],
						'description' => $data['Kit']['description'],
						'weight' => $data['Kit']['weight'],
						'measurement_unit' => $data['Kit']['measurement_unit'],
						'item_type' => $data['Kit']['item_type']
					),
					'Kit' => array(
						'pid' => $data['Kit']['pid'],
						'hts_number' => $data['Kit']['hts_number'],
						'tax_group' => $data['Kit']['tax_group'],
						'eccn' => $data['Kit']['eccn'],
						'kit_release_date' => $data['Kit']['kit_release_date'],
						'for_distributors' => $data['Kit']['for_distributors'],
						'hide_kid_content' => $data['Kit']['hide_kid_content'],
						'kit_status' => $data['Kit']['kit_status']
					)
				);
			 } else {
				 $code = null;
				 if ($data['Kit']['item_type']!=$comData['Item']['item_type']) {
					 $code = $this->Item->generateCode($data['Kit']['item_type']);
				 } else {
					 $code = $comData['Item']['code'];
				 }
				 $pid = null;
				 if ($comData['Kit']['pid']!=null) {
					 $pid = $comData['Kit']['pid'];
				 } else {
					 $pid = $data['Kit']['pid'];
				 }
				 $saveData = array(
						'Item' => array(
							'id' => $comData['Item']['id'],
							'code' => $code,
							'name' => $data['Kit']['name'],
							'description' => $data['Kit']['description'],
							'weight' => $data['Kit']['weight'],
							'measurement_unit' => $data['Kit']['measurement_unit'],
							'item_type' => $data['Kit']['item_type']
						),
						'Kit' => array(
							'id' => $comData['Kit']['id'],
							'item' => $comData['Kit']['item'],
							'service_status' => $data['Kit']['kit_status'],
							'pid' => $pid,
							'hts_number' => $data['Kit']['hts_number'],
							'tax_group' => $data['Kit']['tax_group'],
							'eccn' => $data['Kit']['eccn'],
							'kit_release_date' => $data['Kit']['kit_release_date'],
							'for_distributors' => $data['Kit']['for_distributors'],
							'hide_kid_content' => $data['Kit']['hide_kid_content'],
							'kit_status' => $data['Kit']['kit_status']
						)
					);
			 }
			 if ($this->Kit->saveAssociated($saveData, array('validate' => true, 'deep' => true))) {
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
		if (!$this->Kit->exists($id)) {
			throw new NotFoundException(__('Invalid kit'));
		}
		$this->request->allowMethod('post', 'delete');
		$data = $this->Kit->find(
			'first', array(
				'conditions' => array(
					'Kit.id' => $id
				)
			)
		);
		if ($this->Kit->delete($id)) {
			$this->loadModel('Item');
			$this->loadModel('MoveCardItem');
			$this->loadModel('WarehouseAddressItem');
			$this->MoveCardItem->deleteItems($data['Item']['id']);
			$this->WarehouseAddressItem->deleteItems($data['Item']['id']);
			$this->Item->deleteAll(array('Item.id' => $data['Item']['id']), false, false);
			$this->Flash->success(__('The kit has been deleted.'));
		} else {
			$this->Flash->error(__('The kit could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}

	/*
		Exports data to excel file
	*/
	public function outputExcel() {
		$fileName = 'kits.xlsx';
		$this->layout = 'xls'; //this will use the no layout
		$this->autoRender = false;
		$this->response->type('application/vnd.ms-excel');
		$data = $this->Kit->find('all');
		$objExcel = $this->Kit->createExcel($data);
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
			$messages = $this->Kit->loadFromExcel($uploadPath);
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
				$fileName = 'kits.pdf';

				//Close and output PDF document
				$this->Kit->createPDF()->Output($fileName, 'I');
			}
}
