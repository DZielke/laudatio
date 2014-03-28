<?php
App::uses('ArosAco', 'Model');

/**
 * ArosAco Test Case
 *
 */
class ArosAcoTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.aros_aco',
		'app.aro',
		'app.aco'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->ArosAco = ClassRegistry::init('ArosAco');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->ArosAco);

		parent::tearDown();
	}

}
