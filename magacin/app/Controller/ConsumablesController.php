<?php
App::uses('AppController', 'Controller');
/**
 * Consumables Controller
 *
 * @property Consumable $Consumable
 * @property PaginatorComponent $Paginator
 */
class ConsumablesController extends AppController {

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
		$this->Consumable->recursive = 0;
		$this->set('consumables', $this->Paginator->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->Consumable->exists($id)) {
			throw new NotFoundException(__('Invalid consumable'));
		}
		$options = array('conditions' => array('Consumable.' . $this->Consumable->primaryKey => $id));
		$this->set('consumable', $this->Consumable->find('first', $options));
	}

	/**
	 * save method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	 public function save($id = null) {
		 $statusOp = $this->Consumable->prepareStatus();
		 $ratingOp = $this->Consumable->prepareRecommendation();
		 $this->set('unitOptions', $this->Consumable->prepareMeasurementUnits($id));
		 $this->set('statusOptions', $statusOp);
		 $this->set('typeOptions', $this->Consumable->prepareTypes($id));
		 $this->set('recommendationOptions', $ratingOp);
		 $comData = null;
		 if ($this->Consumable->exists($id)) {
			 $comData = $this->Consumable->getCompleteData($id);
			 $this->set('name', $comData['Item']['name']);
			 $this->set('description', $comData['Item']['description']);
			 $this->set('weight', $comData['Item']['weight']);
			 $this->set('currentUnit', $comData['Item']['measurement_unit']);
			 $this->set('currentType', $comData['Item']['item_type']);
			 $this->set('currentStatus', array_flip($statusOp)[$comData['Consumable']['consumable_status']]);
			 $this->set('currentRecommendation', array_flip($ratingOp)[$comData['Consumable']['recommended_rating']]);
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
			 if (!$this->Consumable->exists($id)) {
				$this->Consumable->create();
 				$saveData = array(
 					'Item' => array(
						'code' => $this->Item->generateCode($data['Consumable']['item_type']),
 						'name' => $data['Consumable']['name'],
 						'description' => $data['Consumable']['description'],
 						'weight' => $data['Consumable']['weight'],
 						'measurement_unit' => $data['Consumable']['measurement_unit'],
 						'item_type' => $data['Consumable']['item_type']
 					),
 					'Consumable' => array(
 						'consumable_status' => $data['Consumable']['consumable_status'],
 						'recommended_rating' => $data['Consumable']['recommended_rating'],
 					)
 				);
			 } else {
				 $code = null;
				 if ($data['Consumable']['item_type']!=$comData['Item']['item_type']) {
					 $code = $this->Item->generateCode($data['Consumable']['item_type']);
				 } else {
					 $code = $comData['Item']['code'];
				 }
				 $saveData = array(
  					'Item' => array(
							'id' => $comData['Item']['id'],
							'code' => $code,
  						'name' => $data['Consumable']['name'],
  						'description' => $data['Consumable']['description'],
  						'weight' => $data['Consumable']['weight'],
  						'measurement_unit' => $data['Consumable']['measurement_unit'],
  						'item_type' => $data['Consumable']['item_type']
  					),
  					'Consumable' => array(
							'id' => $comData['Consumable']['id'],
							'item' => $comData['Consumable']['item'],
  						'consumable_status' => $data['Consumable']['consumable_status'],
  						'recommended_rating' => $data['Consumable']['recommended_rating'],
  					)
  				);
			 }
			 if ($this->Consumable->saveAssociated($saveData, array('validate' => true, 'deep' => true))) {
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
		if (!$this->Consumable->exists($id)) {
			throw new NotFoundException(__('Invalid consumable'));
		}
		$this->request->allowMethod('post', 'delete');
		$data = $this->Consumable->find(
			'first', array(
				'conditions' => array(
					'Consumable.id' => $id
				)
			)
		);
		if ($this->Consumable->delete($id)) {
			$this->loadModel('Item');
			$this->loadModel('MoveCardItem');
			$this->loadModel('WarehouseAddressItem');
			$this->MoveCardItem->deleteItems($data['Item']['id']);
			$this->WarehouseAddressItem->deleteItems($data['Item']['id']);
			$this->Item->deleteAll(array('Item.id' => $data['Item']['id']), false, false);
			$this->Flash->success(__('The consumable has been deleted.'));
		} else {
			$this->Flash->error(__('The consumable could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}

	/*
		Exports data to excel file
	*/
	public function outputExcel() {
		$fileName = 'consumables.xlsx';
		$this->layout = 'xls'; //this will use the no layout
		$this->autoRender = false;
		$this->response->type('application/vnd.ms-excel');
		$data = $this->Consumable->find('all');
		$objExcel = $this->Consumable->createExcel($data);
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
			$messages = $this->Consumable->loadFromExcel($uploadPath);
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
				$fileName = 'consumables.pdf';

				//Close and output PDF document
				$this->Consumable->createPDF()->Output($fileName, 'I');
			}
}
