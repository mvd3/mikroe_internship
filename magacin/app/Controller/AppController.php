<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		https://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {
  public $components = array('Flash',
        'Acl',
        'Auth' => array(
            'authorize' => array(
                'Actions' => array('actionPath' => 'controllers')
            )
        ),
        'Session'
    );

    public $helpers = array('Html', 'Form', 'Session');

    public function beforeFilter() {
        //Configure AuthComponent
        $this->Auth->loginAction = array(
          'controller' => 'users',
          'action' => 'login'
        );
        $this->Auth->logoutRedirect = array(
          'controller' => 'users',
          'action' => 'login'
        );
        $this->Auth->loginRedirect = array(
          'controller' => 'Items',
          'action' => 'index'
        );
    }

  public function getFirstMessage($messages) {
    foreach ($messages as $message) {
      return $message[0];
    }
  }

  /**
 * Function to send json response. This function is generally used when an ajax request is made
 *
 * @param array   $response Data to be sent in json response
 *
 * @return void
 */
  public function sendJSON($response)
  {
      // Make sure no debug info is printed
      // Configure::write('debug', 0); // Turn this to 2 for debugging
      // Set the data for view
      $this->set('response', $response);
      // We will use no layout
      $this->layout = '';
      // Render the json element
      $this->render('/Elements/json');
      // $this->render(null, null, 'elements' . DS . 'json.ctp');
  }//end sendJson()
}
