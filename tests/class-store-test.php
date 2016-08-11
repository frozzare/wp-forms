<?php

namespace Frozzare\Tests\Forms;

use Frozzare\Forms\Store;

class Store_Test extends \WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
		$this->store = new Store;
	}

	public function tearDown() {
		parent::tearDown();
		unset( $this->store );
	}

	public function test_save() {
		$this->assertFalse( $this->store->save( '', [] ) );
		$this->assertFalse( $this->store->save( 'contact', [] ) );
		$this->assertTrue( $this->store->save( 'contact', ['name' => 'Fredrik'] ) );
	}
}