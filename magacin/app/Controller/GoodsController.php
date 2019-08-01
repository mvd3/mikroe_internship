<?php
App::uses('AppController', 'Controller');
/**
 * Goods Controller
 *
 * @property Good $Good
 * @property PaginatorComponent $Paginator
 */
class GoodsController extends AppController {

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
		$this->Good->recursive = 0;
		$this->set('goods', $this->Paginator->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->Good->exists($id)) {
			throw new NotFoundException(__('Invalid good'));
		}
		$options = array('conditions' => array('Good.' . $this->Good->primaryKey => $id));
		$this->set('good', $this->Good->find('first', $options));
	}

	/**
	 * save method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	 public function save($id = null) {
		 $statusOp = $this->Good->prepareStatus();
		 $this->set('unitOptions', $this->Good->prepareMeasurementUnits($id));
		 $this->set('statusOptions', $statusOp);
		 $this->set('typeOptions', $this->Good->prepareTypes($id));
		 $comData = null;
		 $pidCheck = true;
		 if ($this->Good->exists($id)) {
			 $comData = $this->Good->getCompleteData($id);
			 if ($comData['Good']['pid']>0) {
				 $pidCheck = false;
			 } else {
				 $this->set('pid', $comData['Good']['pid']);
			 }
			 $this->set('name', $comData['Item']['name']);
			 $this->set('description', $comData['Item']['description']);
			 $this->set('weight', $comData['Item']['weight']);
			 $this->set('currentUnit', $comData['Item']['measurement_unit']);
			 $this->set('currentType', $comData['Item']['item_type']);
			 $this->set('currentHts', $comData['Good']['hts_number']);
			 $this->set('currentTax', $comData['Good']['tax_group']);
			 $this->set('currentEccn', $comData['Good']['eccn']);
			 $this->set('currentDate', $comData['Good']['release_date']);
			 $this->set('currentDistributors', $comData['Good']['for_distributors']);
			 $this->set('currentStatus', array_flip($statusOp)[$comData['Good']['status']]);
		 } else {
			 $this->set('name', null);
			 $this->set('description', null);
			 $this->set('weight', null);
			 $this->set('currentUnit', null);
			 $this->set('currentType', null);
			 $this->set('pid', $this->Good->generateNextPID());
			 $this->set('currentHts', null);
			 $this->set('currentTax', null);
			 $this->set('currentEccn', null);
			 $this->set('currentDate', null);
			 $this->set('currentDistributors', null);
			 $this->set('currentStatus', null);
		 }
		 $this->set('pidCheck', $pidCheck);
		 $saveData = array();
		 if ($this->request->is('post')) {
			 $data = $this->request->data;
			 $this->loadModel('Item');
			 if (!$this->Good->exists($id)) {
			 $this->Good->create();
				$saveData = array(
					'Item' => array(
						'code' => $this->Item->generateCode($data['Good']['item_type']),
						'name' => $data['Good']['name'],
						'description' => $data['Good']['description'],
						'weight' => $data['Good']['weight'],
						'measurement_unit' => $data['Good']['measurement_unit'],
						'item_type' => $data['Good']['item_type']
					),
					'Good' => array(
						'pid' => $data['Good']['pid'],
						'hts_number' => $data['Good']['hts_number'],
						'tax_group' => $data['Good']['tax_group'],
						'eccn' => $data['Good']['eccn'],
						'release_date' => $data['Good']['release_date'],
						'for_distributors' => $data['Good']['for_distributors'],
						'status' => $data['Good']['status']
					)
				);
			 } else {
				 $code = null;
				 if ($data['Good']['item_type']!=$comData['Item']['item_type']) {
					 $code = $this->Item->generateCode($data['Good']['item_type']);
				 } else {
					 $code = $comData['Item']['code'];
				 }
				 $pid = null;
				 if ($comData['Good']['pid']!=null) {
					 $pid = $comData['Good']['pid'];
				 } else {
					 $pid = $data['Good']['pid'];
				 }
				 $saveData = array(
						'Item' => array(
							'id' => $comData['Item']['id'],
							'code' => $code,
							'name' => $data['Good']['name'],
							'description' => $data['Good']['description'],
							'weight' => $data['Good']['weight'],
							'measurement_unit' => $data['Good']['measurement_unit'],
							'item_type' => $data['Good']['item_type']
						),
						'Good' => array(
							'id' => $comData['Good']['id'],
							'item' => $comData['Good']['item'],
							'pid' => $pid,
							'hts_number' => $data['Good']['hts_number'],
							'tax_group' => $data['Good']['tax_group'],
							'eccn' => $data['Good']['eccn'],
							'release_date' => $data['Good']['release_date'],
							'for_distributors' => $data['Good']['for_distributors'],
							'status' => $data['Good']['status']
						)
					);
			 }
			 if ($this->Good->saveAssociated($saveData, array('validate' => true, 'deep' => true))) {
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
		if (!$this->Good->exists($id)) {
			throw new NotFoundException(__('Invalid good'));
		}
		$this->request->allowMethod('post', 'delete');
		$data = $this->Good->find(
			'first', array(
				'conditions' => array(
					'Good.id' => $id
				)
			)
		);
		if ($this->Good->delete($id, true)) {
			$this->loadModel('Item');
			$this->loadModel('MoveCardItem');
			$this->loadModel('WarehouseAddressItem');
			$this->MoveCardItem->deleteItems($data['Item']['id']);
			$this->WarehouseAddressItem->deleteItems($data['Item']['id']);
			$this->Item->deleteAll(array('Item.id' => $data['Item']['id']), false, false);
			$this->Flash->success(__('The good has been deleted.'));
		} else {
			$this->Flash->error(__('The good could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}

	/*
		Exports data to excel file
	*/
	public function outputExcel() {
		$fileName = 'goods.xlsx';
		$this->layout = 'xls'; //this will use the no layout
		$this->autoRender = false;
		$this->response->type('application/vnd.ms-excel');
		$data = $this->Good->find('all');
		$objExcel = $this->Good->createExcel($data);
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
			$messages = $this->Good->loadFromExcel($uploadPath);
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
				$fileName = 'goods.pdf';

				//Close and output PDF document
				$this->Good->createPDF()->Output($fileName, 'I');
			}
}
