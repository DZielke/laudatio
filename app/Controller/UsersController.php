<?php
App::uses('AppController', 'Controller');
/**
 * Users Controller
 *
 * @property User $User
 */
class UsersController extends AppController {
  	#var $components = array('Auth','Acl','Session');
	#var $helpers = array('Form','Session'); 
	
	function beforeFilter() {
		parent::beforeFilter();
        $this->Auth->allow('login','requestAccount','logout');
	#$this->Auth->allow(array('index','view','login','objects','plugin' => 'search'));
	
		#$this->Auth->loginAction = array('plugin' => null, 'admin' => false, 'controller' => 'Users', 'action' => 'login');
		#$this->Auth->loginRedirect = array('controller' => 'XMLObjects', 'action' => 'index');
		$this->Auth->logoutRedirect = array('controller' => 'XMLObjects', 'action' => 'index');
		$this->Auth->loginError = 'Wrong password!';
	}
	
	public function login() {
	    if ($this->request->is('post')) {
	        if ($this->Auth->login()) {
	        return $this->redirect($this->Auth->redirect());
	       # }elseif($this->Ldap->authorize($this->request->data['User'],$this->request)){
	       # 	$this->Auth->login($this->request->data['User']);       		
        	}
            else {
            	$this->Session->setFlash(__('Username or password is incorrect'), 'default', array(), 'auth');
			}
	        
	    }
	}
	public function admin_login() {
	   $this->redirect(array('plugin' => null, 'admin' => false, 'action' => 'index'));
	}
	
	public function logout() {
        $this->redirect($this->Auth->logout());
        $this->Session->destroy();
        $this->redirect('/', null, true);
    }

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->User->recursive = 0;
		$this->set('users', $this->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		$this->set('user', $this->User->read(null, $id));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->User->create();
			if ($this->User->save($this->request->data)) {
				$this->Session->setFlash(__('The user has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The user could not be saved. Please, try again.'));
			}
		}
		$groups = $this->User->Group->find('list');
		$this->set(compact('groups'));
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->User->save($this->request->data)) {
				$this->Session->setFlash(__('The user has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The user could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->User->read(null, $id);
		}
		$groups = $this->User->Group->find('list');
		$this->set(compact('groups'));
	}

/**
 * delete method
 *
 * @throws MethodNotAllowedException
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		if ($this->User->delete()) {
			$this->Session->setFlash(__('User deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('User was not deleted'));
		$this->redirect(array('action' => 'index'));
	}
    
     public function requestAccount() {
        if ($this->request->is('post')) {
            $this->User->create();
            $this->request->data['User']['group_id'] = '3';
            #var_dump($this->request->data);
            if ($this->User->save($this->request->data)) {
                $this->Session->setFlash(__('The account has been created'), 'default', array('class' => 'success'));
                $this->redirect(array('controller' => 'XMLObjects','action' => 'index'));
            } else {
                $this->Session->setFlash(__('The user could not be saved. Please, try again.'));
            }
        }
        $groups = $this->User->Group->find('list');
        $this->set(compact('groups'));
    }
}
