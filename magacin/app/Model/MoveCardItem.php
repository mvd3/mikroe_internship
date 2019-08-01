<?php
App::uses('AppModel', 'Model');
/**
 * MoveCardItem Model
 *
 */
class MoveCardItem extends AppModel {

  public $belongsTo = array(
    'MoveCard' => array(
      'className' => 'MoveCard',
      'foreignKey' => 'move_card_id',
    ),
    'Item' => array(
      'className' => 'Item',
      'foreignKey' => 'item',
    ),
    'AddressIssued' => array(
      'className' => 'WarehouseAddress',
      'foreignKey' => 'address_issued',
    ),
    'AddressRecieved' => array(
      'className' => 'WarehouseAddress',
      'foreignKey' => 'address_recieved',
    )
  );

  /*
    Returns all data from the move card item
    Input argument is the id
  */
  public function getCompleteData($id) {
    $data = $this->find('first', array(
      'conditions' => array('MoveCardItem.id' => $id)
    ));
    return $data;
  }

  /*
  Returns an array with all the items belonging to the given card
  */
  public function getAllDataForCard($card) {
    return $this->find('all', array(
      'conditions' => array('MoveCardItem.move_card_id' => $card)
    ));
  }

  /*
  Deletes all items with the given item id
  */
  public function deleteItems($item) {
    $data = $this->find('all', array(
      'conditions' => array('MoveCardItem.item' => $item),
      'fields' => 'id'
    ));
    for($i=0;$i<count($data);$i++) {
      $this->delete($data[$i]['MoveCardItem']['id']);
    }
  }
}
