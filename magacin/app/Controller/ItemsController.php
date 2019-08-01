<?php
App::uses('AppController', 'Controller');
/**
 * Items Controller
 *
 * @property Item $Item
 * @property PaginatorComponent $Paginator
 */
class ItemsController extends AppController {

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
		$this->Item->recursive = 0;
		$this->set('typesOptions', $this->Item->getItemTypes());
		if (strpos($this->params->url, 'op:search') !== false) {
			/*$data = $this->request->data;
			$cond = array(
				'AND' => array(
					'Item.item_type' => $data['Search']['item_type'],
					'OR' => array(
						'Item.code LIKE' => '%' . $data['Search']['query'] . '%',
						'Item.name LIKE' => '%' . $data['Search']['query'] . '%',
						'Item.description LIKE' => '%' . $data['Search']['query'] . '%'
					)
				)
			);
			$this->Paginator->settings = array(
				'conditions' => $cond,
				'imit' => 20
			);*/
			$this->Item->paginatorSearchOptions($this->Paginator, $this->request->data);
		}
		$this->set('items', $this->Paginator->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->Item->exists($id)) {
			throw new NotFoundException(__('Invalid item'));
		}
		$options = array('conditions' => array('Item.' . $this->Item->primaryKey => $id));
		$this->set('item', $this->Item->find('first', $options));
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		if (!$this->Item->exists($id)) {
			throw new NotFoundException(__('Invalid item'));
		}
		$this->Item->deleteConnections($id);
		$this->request->allowMethod('post', 'delete');
		if ($this->Item->delete($id, true)) {
			$this->Flash->success(__('The item has been deleted.'));
		} else {
			$this->Flash->error(__('The item could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
