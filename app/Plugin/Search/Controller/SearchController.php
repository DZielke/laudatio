<?php
class SearchController extends AppController {
	public function index() {
	
	}
    
	function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow();
	}
}
?>