<?php
App::uses('AppController', 'Controller');
class ArticleAddressesController extends AppController {

/**
 * index method
 *
 * @return void
 */
	public function index($id = null) {
			$this->loadModel('WarehouseAddressItem');
			$this->loadModel('WarehouseAddress');
			$this->loadModel('Item');
			$this->set('addressOptions', $this->WarehouseAddress->getAll());
			$addr = $id;
			if ($addr==null) {
				$addr = $this->request->data;
			}
			$data = null;
			$address = null;
			if ($addr!=null) {
				$rawData = $this->WarehouseAddressItem->getAllWithAddress($addr);
				$data = array();
				$dataItem = ['itemCode' => null, 'name' => null, 'quantity' => null, 'reserved' => null];
				$address = $this->WarehouseAddress->getCompleteData($addr);
				foreach($rawData as $raw) {
					$item = $this->Item->getCompleteData($raw['WarehouseAddressItem']['item']);
					$dataItem['itemCode'] = $item['Item']['code'];
					$dataItem['name'] = $item['Item']['name'];
					$dataItem['quantity'] = $raw['WarehouseAddressItem']['quantity'];
					$dataItem['reserved'] = $raw['WarehouseAddressItem']['reserved'];
					array_push($data, $dataItem);
				}
			}
			$this->set('selectedData', $data);
			$this->set('addressData', $address);
	}

	/**
	 * add method
	 *
	 * @return void
	 */
		public function add($id) {
			$this->loadModel('WareLocType');
			$this->loadModel('ItemType');
			$this->loadModel('Item');
			$this->loadModel('WarehouseAddressItem');
			$this->set('addrID', $id);
			$classes = $this->WareLocType->getClassesForLocation($id);
			//Extract all item types id that belong to the selected classes
			$typesID = array();
			foreach($classes as $class) {
				$types = $this->ItemType->getTypesByClass($class);
				foreach($types as $type) {
					array_push($typesID, $type['ItemType']['id']);
				}
			}
			//Extract all items that belong to the selected type
			$itemKeys = array();
			$itemValues = array();
			foreach($typesID as $typeID) {
				$items = $this->Item->getItemsByType($typeID);
				foreach($items as $item) {
					array_push($itemKeys, $item['Item']['id']);
					array_push($itemValues, $item['Item']['name']);
				}
			}
			$itemOp = array_combine($itemKeys, $itemValues);
			$this->set('itemOptions', $itemOp);
			if ($this->request->is('post')) {
				$data = $this->request->data;
				if ($data['quantity']>0) {
					$saveData = null;
					if ($this->WarehouseAddressItem->checkExistence($id, $data['item'])) {
						//Already on the address
						$saveData = $this->WarehouseAddressItem->getCompleteData($id, $data['item']);
						$saveData['WarehouseAddressItem']['quantity'] += $data['quantity'];
					} else {
						//Other case
						$saveData['WarehouseAddressItem']['warehouse_address'] = $id;
						$saveData['WarehouseAddressItem']['item'] = $data['item'];
						$saveData['WarehouseAddressItem']['quantity'] = $data['quantity'];
					}
					if ($this->WarehouseAddressItem->saveAssociated($saveData, array('validate' => true, 'deep' => true))) {
	 				 $this->Flash->success(__('Save successfull.'));
	 				 return $this->redirect(array('action' => 'index', $id));
	 			 } else {
	 				 $this->Flash->error(__('There has been an error. Please, try again.'));
	 			 }
			 } else {
				 	$this->Flash->error(__('Check quantity input!'));
			 }
			}
		}
}
