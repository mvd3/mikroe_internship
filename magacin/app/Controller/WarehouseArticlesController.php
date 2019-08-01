<?php
App::uses('AppController', 'Controller');
class WarehouseArticlesController extends AppController {

/**
 * index method
 *
 * @return void
 */
	public function index($id = null) {
		$this->loadModel('WarehouseLocation');
		$this->loadModel('WarehouseAddress');
		$this->loadModel('WarehouseAddressItem');
		$this->loadModel('Item');
		$locationOp = $this->WarehouseLocation->getAll();
		$this->set('locationOptions', $locationOp);
		$loc = $id;
		if ($loc==null) {
			$loc = $this->request->data;
		}
		$location = null;
		$selectedData = null;
		if ($loc!=null) {
			$location = $this->WarehouseLocation->getCompleteData($loc);
			$addresses = $this->WarehouseAddress->getAllWithLocation($loc);
			$itemsID = array();
			if ($addresses!=null) {
				foreach($addresses as $address) {
					$tmpItems = $this->WarehouseAddressItem->getAllWithAddress($address['WarehouseAddress']['id']);
					if ($tmpItems!=null) {
						foreach($tmpItems as $itm) {
							$check = true;
							for ($i=0;$i<count($itemsID);$i++) {
								if($itm['WarehouseAddressItem']['item']==$itemsID[$i]['WarehouseAddressItem']['item']) {
									$itemsID[$i]['WarehouseAddressItem']['quantity'] += $itm['WarehouseAddressItem']['quantity'];
									$itemsID[$i]['WarehouseAddressItem']['reserved'] += $itm['WarehouseAddressItem']['reserved'];
									$check = false;
									break;
								}
							}
							if ($check) {
								array_push($itemsID, $itm);
							}
						}
					}
				}
			}
			$selectedData = array();
			foreach($itemsID as $item) {
				$itmc = $this->Item->getCompleteData($item['WarehouseAddressItem']['item']);
				$dataPiece = array(
					'code' => $itmc['Item']['code'],
					'name' => $itmc['Item']['name'],
					'quantity' => $item['WarehouseAddressItem']['quantity'],
					'reserved' => $item['WarehouseAddressItem']['reserved']
				);
				array_push($selectedData, $dataPiece);
			}
		}
		$this->set('location', $location);
		$this->set('selectedData', $selectedData);
	}
}
