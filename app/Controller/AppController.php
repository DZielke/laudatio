<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {
	var $helpers = array('Html', 'Form', 'Session');
	var $user;
    public $components = array(
    	#'DebugKit.Toolbar',
	    'Acl',
	    'Auth' => array(
	    	'loginAction' => array(
	    		'plugin' => null, 'admin' => false, 'controller' => 'Users', 'action' => 'login'),
	       'authorize' => array('Actions' => array('actionPath' => 'controllers/')
	        ),
	        'authenticate' => array('Form','Ldap')
	    ),
	    'Session'
    );
}
function beforeFilter(){
	    #$this->Auth->authenticate = array('LdapAuth');
        #$this->Auth->authorize = 'actions';
		#$this->Auth->actionPath = 'controllers/';
}
###DZ, 21.12.12
/*
class AppController extends Controller {
	var $helpers = array('Html', 'Form');

        var $components = array('Auth', 'Session', 'RequestHandler');
        var $user;
        function beforeFilter(){
                global $menus;
                $this->Auth->authenticate = array('Idbroker.Ldap'=>array('userModel'=>'Idbroker.LdapAuth'));
                //If you want to do your authorization from the isAuthorized Controller use the following
                $this->Auth->authorize = array('Controller');
        }

        public function isAuthorized(){
                $user = $this->Auth->user();
                if($user) return true;
                return false;
        }
}
*/