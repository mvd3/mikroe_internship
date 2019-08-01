<?php
App::uses('AppController', 'Controller');
/**
 * MoveCards Controller
 *
 * @property MoveCard $MoveCard
 * @property PaginatorComponent $Paginator
 */
class MoveCardsController extends AppController {

private $types = array(
	1 => 'standard',
	2 => 'trebovanje'
);

private $statuses = array(
	1 => 'otvoren',
	2 => 'poslat',
	3 => 'spreman',
	4 => 'isporucen',
	5 => 'otkazan',
);

private $groups = array(
		1 => 'Administrators',
		2 => 'Operators',
		3 => 'Warehouse Clerks'
);

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator'/*, 'RequestHandler'*/);

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->loadModel('User');
		$this->loadModel('MoveAllowance');
		$currentUserID = $this->Auth->User('id');
		$currentUserGroup = $this->User->getCompleteData($currentUserID)['Group']['name'];
		$this->MoveCard->recursive = 0;
		$allowances = $this->MoveAllowance->getAllData($this->Auth->User('id'));
		$values = array();
		if ($this->User->getCompleteData($currentUserID)["Group"]['name']=='Warehouse Clerks') {
			for ($i=0;$i<count($allowances);$i++) {
				array_push($values, $allowances[$i]['WarehouseLocation']['id']);
			}
		}
		$cond = array('OR' => array(
			'MoveCard.creator' => $currentUserID,
			'MoveCard.move_from' => $values,
			'MoveCard.move_to' => $values,
		));
		$this->Paginator->settings = array(
			'conditions' => $cond,
			'limit' => 20
		);
		$data = $this->Paginator->paginate();
		// var_dump($data, $currentUserID);
		// exit;
		$operatorData = $this->MoveAllowance->getAllData($currentUserID);
		$this->set('moveCards', $data);
		$this->set('role', $this->User->getRole($currentUserID));
		$this->set('openStatus', $this->statuses[1]);
		$this->set('sentStatus', $this->statuses[2]);
		$this->set('readyStatus', $this->statuses[3]);
		$preparePass = false;
		$receivePass = false;
		if ($data!=null) {
			foreach($operatorData as $opData) {
				if ($data[0]['From']['id']==$opData['WarehouseLocation']['id'] && $opData['MoveAllowance']['allowance']) {
					$preparePass = true;
					break;
				}
			}
			foreach($operatorData as $opData) {
				if ($data[0]['To']['id']==$opData['WarehouseLocation']['id'] && $opData['MoveAllowance']['allowance']) {
					$receivePass = true;
					break;
				}
			}
		}
		$this->set('preparePass', $preparePass);
		$this->set('receivePass', $receivePass);
		$this->set('isOperator', false);
		$this->set('isClerk', false);
		if ($currentUserGroup==$this->groups[2]) {
			$this->set('isOperator', true);
		} else if ($currentUserGroup==$this->groups[3]) {
			$this->set('isClerk', true);
		}
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->MoveCard->exists($id)) {
			throw new NotFoundException(__('Invalid move card'));
		}
		$this->loadModel('WarehouseLocation');
		$this->loadModel('MoveAllowance');
		$this->loadModel('MoveCardItem');
		$allowances = $this->MoveAllowance->getAllData($this->Auth->User('id'));
		$keys = array();
		$values = array();
		for ($i=0;$i<count($allowances);$i++) {
			array_push($keys, $allowances[$i]['WarehouseLocation']['id']);
			array_push($values, $allowances[$i]['WarehouseLocation']['code']);
		}
		$locationOp = array_combine($keys, $values);
		$comData = $this->MoveCard->getCompleteData($id);
		$itemsOp = $this->prepareItems($comData['MoveCard']['move_from'], $comData['MoveCard']['move_to']);
		$this->set('locationOptions', $locationOp);
		$this->set('typeOptions', $this->types);
		$this->set('itemOptions', $itemsOp);
		$this->set('moveFromDefault', $comData['MoveCard']['move_from']);
		$this->set('moveToDefault', $comData['MoveCard']['move_to']);
		$this->set('typeDefault', array_flip($this->types)[$comData['MoveCard']['type']]);
		$this->set('workorderDefault', $comData['MoveCard']['work_order']);
		$moveCardItems = $this->MoveCardItem->getAllDataForCard($comData['MoveCard']['id']);
		$this->set('moveCardItems', $moveCardItems);
		if ($this->request->is('post')) {
			$data = $this->request->data;
			if ($data['MoveCard']['move_from']!=$data['MoveCard']['move_to']) {
				$workOrder = null;
				if ($data['MoveCard']['type']==2) {
					$workOrder = $data['MoveCard']['work_order'];
				}
				if ($comData['MoveCard']['type']!=$this->types[$data['MoveCard']['type']]) {
					$comData['MoveCard']['code'] = $this->MoveCard->generateNextCode($this->types[$data['MoveCard']['type']]);
					$comData['MoveCard']['type'] = $data['MoveCard']['type'];
					$comData['MoveCard']['work_order'] = $workOrder;
				}
				$comData['MoveCard']['move_from'] = $data['MoveCard']['move_from'];
				$comData['MoveCard']['move_to'] = $data['MoveCard']['move_to'];
				if ($this->MoveCard->save($comData)) {
					//Saving the items
					if ($data['Item']!=null) {
						$moveCardID = $this->MoveCard->id;
						$items = array();
						foreach($data['Item'] as $item) {
							$check = false;
							for($i=0;$i<count($items);$i++) {
								if ($items[$i]['item']==$item['item']) {
									$items[$i]['quantity'] += $item['quantity'];
									$check = true;
									break;
								}
							}
							if ($check) {
								continue;
							}
							array_push($items, $item);
						}
					}
					$toSave = array();
					$toDelete = array();
					for ($i=0;$i<count($moveCardItems);$i++) {
						$check = true;
						foreach($items as $item) {
								if ($moveCardItems[$i]['MoveCardItem']['item']==$item['item']) {
									$moveCardItems[$i]['MoveCardItem']['quantity_demanded'] = $item['quantity'];
									array_push($toSave, $moveCardItems[$i]);
									$check = false;
								}
							}
							if ($check) {
								array_push($toDelete, $moveCardItems[$i]);
							}
					}
					foreach($items as $item) {
						$check = true;
						foreach($toSave as $save) {
							if ($item['item']==$save['MoveCardItem']['item']) {
								$check = false;
								break;
							}
						}
						if ($check) {
							$this->MoveCardItem->create();
							$saveData = array(
								'MoveCardItem' => array(
									'move_card_id'=> $moveCardID,
									'item' => $item['item'],
									'quantity_demanded' => $item['quantity']
								)
							);
							$this->MoveCardItem->save($saveData);
						}
					}
					foreach($toSave as $save) {
						$this->MoveCardItem->save($save);
					}
					foreach($toDelete as $del) {
						$this->MoveCardItem->delete($del['MoveCardItem']['id']);
					}
					//Finish line
					$this->Flash->success(__('The move card has been saved.'));
					return $this->redirect(array('action' => 'index'));
				} else {
					$this->Flash->error(__('The move card could not be saved. Please, try again.'));
				}
			} else {
				$this->Flash->error(__('Can\'t move from the same location into the same!'));
			}
		}
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		$this->loadModel('WarehouseLocation');
		$this->loadModel('MoveAllowance');
		$allowances = $this->MoveAllowance->getAllData($this->Auth->User('id'));
		$keys = array();
		$values = array();
		for ($i=0;$i<count($allowances);$i++) {
			array_push($keys, $allowances[$i]['WarehouseLocation']['id']);
			array_push($values, $allowances[$i]['WarehouseLocation']['code']);
		}
		$locationOp = array_combine($keys, $values);
		$this->set('locationOptions', $locationOp);
		$this->set('typeOptions', $this->types);
		if ($this->request->is('post')) {
			$this->MoveCard->create();
			$data = $this->request->data;
			if ($data['MoveCard']['move_from']!=$data['MoveCard']['move_to'] && array_key_exists('Item', $data)) {
				$this->MoveCard->create();
				$workOrder = null;
				if ($data['MoveCard']['type']==2) {
					$workOrder = $data['MoveCard']['work_order'];
				}
				$saveData = array(
					'MoveCard' => array(
						'code' => $this->MoveCard->generateNextCode($this->types[$data['MoveCard']['type']]),
						'creator' => $this->Auth->User('id'),
						'move_from' => $data['MoveCard']['move_from'],
						'move_to' => $data['MoveCard']['move_to'],
						'status' => array_flip($this->statuses)['otvoren'],
						'type' => $data['MoveCard']['type'],
						'work_order' => $workOrder
					)
				);
					if ($this->MoveCard->save($saveData)) {
					//Saving the items
					if ($data['Item']!=null) {
						$this->loadModel('MoveCardItem');
						$moveCardID = $this->MoveCard->id;
						$items = array();
						foreach($data['Item'] as $item) {
							$check = false;
							for($i=0;$i<count($items);$i++) {
								if ($items[$i]['item']==$item['item']) {
									$items[$i]['quantity'] += $item['quantity'];
									$check = true;
									break;
								}
							}
							if ($check) {
								continue;
							}
							array_push($items, $item);
						}
					}
					foreach($items as $item) {
						$saveData = array(
							'MoveCardItem' => array(
								'move_card_id'=> $moveCardID,
								'item' => $item['item'],
								'quantity_demanded' => $item['quantity']
							)
						);
						$this->MoveCardItem->saveAssociated($saveData, array('validate' => true, 'deep' => true));
					}
					//Finish line
					$this->Flash->success(__('The move card has been saved.'));
					return $this->redirect(array('action' => 'index'));
				} else {
					$this->Flash->error(__('The move card could not be saved. Please, try again.'));
				}
			} else {
				$msg = null;
				if (array_key_exists('Item', $data)) {
					$msg = 'Can\'t move from the same location into the same!';
				} else {
					$msg = 'Add some items!';
				}
				$this->Flash->error(__($msg));
			}

		}
	}

/**
 * send method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function send($id) {
		if (!$this->MoveCard->exists($id)) {
			throw new NotFoundException(__('Invalid move card'));
		}
		$data = $this->MoveCard->getCompleteData($id);
		$data['MoveCard']['status'] = $this->statuses[2]; //Poslat
		if ($this->MoveCard->save($data)) {
			$this->Flash->success(__('The move card has been saved.'));
		} else {
			$this->Flash->error(__('The move card could not be saved. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		if (!$this->MoveCard->exists($id)) {
			throw new NotFoundException(__('Invalid move card'));
		}
		$this->request->allowMethod('post', 'delete');
		$moveCard = $this->MoveCard->getCompleteData($id);
		$moveCard['MoveCard']['status'] = array_flip($this->statuses)['otkazan'];
		$this->MoveCard->save($moveCard);
		return $this->redirect(array('action' => 'index'));
	}

	public function takeLocations() {
		if ($this->request->is('get')) {
        throw new MethodNotAllowedException();
    }
		$from = $this->request->data('from');
		$to = $this->request->data('to');
		$this->autoRender = false;
		//Magic happens here
		// $this->loadModel('WareLocType');
		// $this->loadModel('ItemType');
		// $this->loadModel('Item');
		// $fromClasses = $this->WareLocType->getClassesForLocation($from);
		// $toClasses = $this->WareLocType->getClassesForLocation($to);
		// $classes = array();
		// $types = array();
		// $keys = array();
		// $values = array();
		// $items = null;
		// foreach($fromClasses as $fromClass) {
		// 	foreach($toClasses as $toClass) {
		// 		if ($fromClass==$toClass) {
		// 			array_push($classes, $toClass);
		// 			break;
		// 		}
		// 	}
		// }
		// if ($classes!=null) {
		// 	foreach($classes as $class) {
		// 		$tmpTypes = $this->ItemType->getTypesByClass($class);
		// 		foreach($tmpTypes as $tmp) {
		// 			array_push($types, $tmp['ItemType']['id']);
		// 		}
		// 	}
		// 	foreach($types as $type) {
		// 		$tmpItems = $this->Item->getItemsByType($type);
		// 		foreach($tmpItems as $tmp) {
		// 			array_push($keys, $tmp['Item']['id']);
		// 			array_push($values, $tmp['Item']['name']);
		// 		}
		// 	}
		// 	$items = array_combine($keys, $values);
		// }
		$items = $this->prepareItems($from, $to);
		$this->sendJSON($items);
	}

	private function prepareItems($from, $to) {
		//Magic happens here
		$this->loadModel('WareLocType');
		$this->loadModel('ItemType');
		$this->loadModel('Item');
		$fromClasses = $this->WareLocType->getClassesForLocation($from);
		$toClasses = $this->WareLocType->getClassesForLocation($to);
		$classes = array();
		$types = array();
		$keys = array();
		$values = array();
		$items = null;
		foreach($fromClasses as $fromClass) {
			foreach($toClasses as $toClass) {
				if ($fromClass==$toClass) {
					array_push($classes, $toClass);
					break;
				}
			}
		}
		if ($classes!=null) {
			foreach($classes as $class) {
				$tmpTypes = $this->ItemType->getTypesByClass($class);
				foreach($tmpTypes as $tmp) {
					array_push($types, $tmp['ItemType']['id']);
				}
			}
			foreach($types as $type) {
				$tmpItems = $this->Item->getItemsByType($type);
				foreach($tmpItems as $tmp) {
					array_push($keys, $tmp['Item']['id']);
					array_push($values, $tmp['Item']['name']);
				}
			}
			$items = array_combine($keys, $values);
		}
		return $items;
	}

	/**
	 * prepare method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function prepare($id) {
		if (!$this->MoveCard->exists($id)) {
			throw new NotFoundException(__('Invalid move card'));
		}
		$this->loadModel('MoveCardItem');
		$this->loadModel('WarehouseAddressItem');
		$itemsData = $this->MoveCardItem->getAllDataForCard($id);
		$addressData = array();//['Address' => array(), 'Available' => array()];
		//Find addresses that contain that item
		foreach($itemsData as $item) {
			$rawData = $this->WarehouseAddressItem->getAddressesWithItem($item['MoveCardItem']['item']);
			$addressKeys = array();
			$addressValues = array();
			$availableKeys = array();
			$availableValues = array();
			$addrData = null;
			if ($rawData!=null) {
				foreach($rawData as $data) {
					$free = $data['WarehouseAddressItem']['quantity']-$data['WarehouseAddressItem']['reserved'];
					if ($free>0) {
						array_push($addressKeys, $data['Address']['id']);
						array_push($addressValues, $data['Address']['code']);
						array_push($availableKeys, $data['Address']['id']);
						array_push($availableValues, $free);
					}
				}
				$addrData = ['Address' => array_combine($addressKeys, $addressValues), 'Available' => array_combine($availableKeys, $availableValues)];
			}
			array_push($addressData, $addrData);
		}
		$this->set('moveCardItems', $itemsData);
		$this->set('addressData', $addressData);
		/*
		--- IMPORTANT NOTICE ---
		Some of the items might not be displayed inside the form because there are not currently located in the warhouse location.
		Because of this we'll skip them in the further processing.
		*/
		if ($this->request->is('post')) {
			$data = $this->request->data;
			$toSave = array();
			for ($i=0;$i<count($addressData);$i++) {
				if ($addressData[$i]!=null) {
					$dataForSave = ['WAI' => null, 'MCI' => null];
					$moveCardItemID = $itemsData[$i]['MoveCardItem']['id'];
					$addresID = $data['SuppliedItems'][$i]['address'];
					$itemID = $itemsData[$i]['MoveCardItem']['item'];
					$wareAddrItem = $this->WarehouseAddressItem->getCompleteData($addresID, $itemID);
					$mcItem = $this->MoveCardItem->getCompleteData($moveCardItemID);
					if ($wareAddrItem['WarehouseAddressItem']['quantity']-$wareAddrItem['WarehouseAddressItem']['reserved']>=$data['SuppliedItems'][$i]['number']) {
						$wareAddrItem['WarehouseAddressItem']['reserved'] += $data['SuppliedItems'][$i]['number'];
						$mcItem['MoveCardItem']['quantity_recieved'] = $data['SuppliedItems'][$i]['number'];
						$mcItem['MoveCardItem']['address_issued'] = $data['SuppliedItems'][$i]['address'];
						$dataForSave['WAI'] = $wareAddrItem;
						$dataForSave['MCI'] = $mcItem;
						array_push($toSave, $dataForSave);
					} else {
						$this->Flash->error(__('Error while saving. Please, try again.'));
						return $this->redirect(array('action' => 'prepare'));
					}
				}
			}
			$check = true;
			foreach($toSave as $saveData) {
				if (!$this->MoveCardItem->save($saveData['MCI']['MoveCardItem'])) {
						$check = false;
						break;
					}
				if (!$this->WarehouseAddressItem->save($saveData['WAI']['WarehouseAddressItem'])) {
						$check = false;
						break;
					}
			}
			$mvCard = $this->MoveCard->getCompleteData($id);
			$mvCard['MoveCard']['status'] = $this->statuses[3];
			$mvCard['MoveCard']['issued_by'] = $this->Auth->User('id');
			if (!$this->MoveCard->save($mvCard['MoveCard'])) {
					$check = false;
			}
			if ($check) {
				$this->Flash->success(__('Move card has been prepared successfully!'));
			} else {
				$this->Flash->error(__('Error while saving. Please, try again.'));
			}
			return $this->redirect(array('action' => 'index'));
		}
	}

	/**
	 * prepare method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	 public function receive($id) {
		 if (!$this->MoveCard->exists($id)) {
 			throw new NotFoundException(__('Invalid move card'));
 		}
		$this->loadModel('MoveCardItem');
		$this->loadModel('WarehouseAddress');
		$this->loadModel('WarehouseAddressItem');
		$itemsData = $this->MoveCardItem->getAllDataForCard($id);
		$addressData = array();
		// var_dump($itemsData);
		// exit;
		$curAddr = $this->WarehouseAddress->getAllWithLocation($itemsData[0]['MoveCard']['move_to']);
		$addrKeys = array();
		$addrValues = array();
		foreach($curAddr as $addr) {
			array_push($addrKeys, $addr['WarehouseAddress']['id']);
			array_push($addrValues, $addr['WarehouseAddress']['code']);
		}
		$addressData = array_combine($addrKeys, $addrValues);
		$this->set('itemsData', $itemsData);
		$this->set('addressData', $addressData);
		if ($this->request->is('post')) {
			$data = $this->request->data;
			//Save the data
			$check = true;
			foreach($data['ReceivedItems'] as $d) {
					$waiData = null;
					if ($this->WarehouseAddressItem->checkExistence($d['address'], $d['item'])) {
						$waiData = $this->WarehouseAddressItem->getCompleteData($d['address'], $d['item']);
					} else {
						$this->WarehouseAddressItem->create();
						$waiData = array(
							'WarehouseAddressItem' => array(
								'warehouse_address' => $d['address'],
								'item' => $d['item'],
								'quantity' => 0
							)
						);
					}
					$waiData['WarehouseAddressItem']['quantity'] += $d['number'];
					if (!$this->WarehouseAddressItem->save($waiData)) {
						$check = false;
						break;
					}
					$waiData = $this->WarehouseAddressItem->getCompleteData($d['originAddress'], $d['item']);
					$waiData['WarehouseAddressItem']['reserved'] -= $d['number'];
					$waiData['WarehouseAddressItem']['quantity'] -= $d['number'];
					if ($waiData['WarehouseAddressItem']['quantity']==0) {
						$this->WarehouseAddressItem->delete($waiData['WarehouseAddressItem']['id']);
					} else if (!$this->WarehouseAddressItem->save($waiData)) {
						$check = false;
						break;
					}
				if ($check) {
						$mciData = $this->MoveCardItem->getCompleteData($d['moveCardItemID']);
						$mciData['MoveCardItem']['address_recieved'] = $d['address'];
						if (!$this->MoveCardItem->save($mciData)) {
							$check = false;
							break;
						}
				}
			}
			if ($check) {
				$mcData = $this->MoveCard->getCompleteData($id);
				$mcData['MoveCard']['status'] = $this->statuses[4];
				$mcData['MoveCard']['recieved_by'] = $this->Auth->User('id');
				if (!$this->MoveCard->save($mcData)) {
					$check = false;
				}
			}
			if ($check) {
				$this->Flash->success(__('Move card has been prepared delivered successfully!'));
			} else {
				$this->Flash->error(__('Error while saving. Please, try again.'));
			}
			return $this->redirect(array('action' => 'index'));
		}
	 }

	 public function display($id) {
		 if (!$this->MoveCard->exists($id)) {
 			throw new NotFoundException(__('Invalid move card'));
	 		}
			$this->loadModel('MoveCardItem');
			$this->loadModel('MeasurementUnit');
		 $moveCard = $this->MoveCard->getCompleteData($id);
		 $items = $this->MoveCardItem->getAllDataForCard($moveCard['MoveCard']['id']);
		 $units = array();
		 foreach($items as $item) {
			 $unit = $this->MeasurementUnit->getCompleteData($item['Item']['measurement_unit']);
			 array_push($units, $unit);
		 }
		 $this->set('moveCard', $moveCard);
		 $this->set('items', $items);
		 $this->set('units', $units);
		 // var_dump($items);
		 // exit;
	 }
}
