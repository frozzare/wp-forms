<?php

namespace Frozzare\Tests\Forms;

use Frozzare\Forms\Forms;

class Helpers_Test extends \WP_UnitTestCase {

	public function test_forms() {
		$this->assertInstanceOf( Forms::class, forms() );
	}
}