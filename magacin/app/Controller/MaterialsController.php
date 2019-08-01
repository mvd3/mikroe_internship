<?php
App::uses('AppController', 'Controller');
App::import('Vendor', 'PDF', array('file' => 'tcpdf/pdf.php'));
/**
 * Materials Controller
 *
 * @property Material $Material
 * @property PaginatorComponent $Paginator
 */
class MaterialsController extends AppController {

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
		$this->Material->recursive = 0;
		$this->set('materials', $this->Paginator->paginate());
		// $this->set($this->Acl->check(array('User' => array('id' => $this->Auth->User('id'))), 'Users'))
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->Material->exists($id)) {
			throw new NotFoundException(__('Invalid material'));
		}
		$options = array('conditions' => array('Material.' . $this->Material->primaryKey => $id));
		$this->set('material', $this->Material->find('first', $options));
	}

	/**
	 * save method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	 public function save($id = null) {
		 $statusOp = $this->Material->prepareStatus();
		 $ratingOp = $this->Material->prepareRecommendation();
		 $this->set('unitOptions', $this->Material->prepareMeasurementUnits($id));
		 $this->set('statusOptions', $statusOp);
		 $this->set('typeOptions', $this->Material->prepareTypes($id));
		 $this->set('recommendationOptions', $ratingOp);
		 $comData = null;
		 if ($this->Material->exists($id)) {
			 $comData = $this->Material->getCompleteData($id);
			 $this->set('name', $comData['Item']['name']);
			 $this->set('description', $comData['Item']['description']);
			 $this->set('weight', $comData['Item']['weight']);
			 $this->set('currentUnit', $comData['Item']['measurement_unit']);
			 $this->set('currentType', $comData['Item']['item_type']);
			 $this->set('currentStatus', array_flip($statusOp)[$comData['Material']['material_status']]);
			 $this->set('serviceProduction', $comData['Material']['service_production']);
			 $this->set('currentRecommendation', array_flip($ratingOp)[$comData['Material']['recommended_rating']]);
		 } else {
			 $this->set('name', null);
			 $this->set('description', null);
			 $this->set('weight', null);
			 $this->set('currentUnit', null);
			 $this->set('currentType', null);
			 $this->set('currentStatus', null);
			 $this->set('serviceProduction', null);
			 $this->set('currentRecommendation', null);
		 }
		 $saveData = array();
		 if ($this->request->is('post')) {
			 $data = $this->request->data;
			 $this->loadModel('Item');
			 if (!$this->Material->exists($id)) {
				$this->Material->create();
				$saveData = array(
					'Item' => array(
						'code' => $this->Item->generateCode($data['Material']['item_type']),
						'name' => $data['Material']['name'],
						'description' => $data['Material']['description'],
						'weight' => $data['Material']['weight'],
						'measurement_unit' => $data['Material']['measurement_unit'],
						'item_type' => $data['Material']['item_type']
					),
					'Material' => array(
						'material_status' => $data['Material']['material_status'],
						'service_production' => $data['Material']['service_production'],
						'recommended_rating' => $data['Material']['recommended_rating']
					)
				);
			 } else {
				 $code = null;
				 if ($data['Material']['item_type']!=$comData['Item']['item_type']) {
					 $code = $this->Item->generateCode($data['Material']['item_type']);
				 } else {
					 $code = $comData['Item']['code'];
				 }
				 $saveData = array(
						'Item' => array(
							'id' => $comData['Item']['id'],
							'code' => $code,
							'name' => $data['Material']['name'],
							'description' => $data['Material']['description'],
							'weight' => $data['Material']['weight'],
							'measurement_unit' => $data['Material']['measurement_unit'],
							'item_type' => $data['Material']['item_type']
						),
						'Material' => array(
							'id' => $comData['Material']['id'],
							'item' => $comData['Material']['item'],
							'material_status' => $data['Material']['material_status'],
							'service_production' => $data['Material']['service_production'],
							'recommended_rating' => $data['Material']['recommended_rating']
						)
					);
			 }
			 if ($this->Material->saveAssociated($saveData, array('validate' => true, 'deep' => true))) {
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
		if (!$this->Material->exists($id)) {
			throw new NotFoundException(__('Invalid material'));
		}
		$this->request->allowMethod('post', 'delete');
		$data = $this->Material->find(
			'first', array(
				'conditions' => array(
					'Material.id' => $id
				)
			)
		);
		if ($this->Material->delete($id, true)) {
			$this->loadModel('Item');
			$this->loadModel('MoveCardItem');
			$this->loadModel('WarehouseAddressItem');
			$this->MoveCardItem->deleteItems($data['Item']['id']);
			$this->WarehouseAddressItem->deleteItems($data['Item']['id']);
			$this->Item->deleteAll(array('Item.id' => $data['Item']['id']), false, false);
			$this->Flash->success(__('The material has been deleted.'));
		} else {
			$this->Flash->error(__('The material could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}

	/*
		Exports data to excel file
	*/
	public function outputExcel() {
		$fileName = 'materials.xls';
		$this->layout = 'xls'; //this will use the no layout
		$this->autoRender = false;
		$this->response->type('application/vnd.ms-excel');
		$data = $this->Material->find('all');
		$objExcel = $this->Material->createExcel($data);
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
		$messages = $this->Material->loadFromExcel($uploadPath);
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
		$fileName = 'materials.pdf';

		//Close and output PDF document
		$this->Material->createPDF()->Output($fileName, 'I');
	}
}
