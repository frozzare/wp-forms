<?php

namespace Frozzare\Tests\Forms;

use Frozzare\Forms\Attributes;

class Attributes_Test extends \WP_UnitTestCase {

	public function test_get_attribute() {
		$attrs = new Attributes;
		$attrs->set_attributes( ['class' => 'foo'] );
		$this->assertSame( 'foo', $attrs->get_attribute( 'class' ) );

		try {
			$attrs->get_attribute( 'fake' );
			$this->assertFalse( true );
		} catch ( \Exception $e ) {
			$this->assertNotEmpty( $e->getMessage() );
		}
	}

	public function test_get_attributes() {
		$attrs = new Attributes( ['class' => 'foo'] );
		$attrs->set_attributes( ['class' => 'foo'] );
		$this->assertSame( ['class' => 'foo'], $attrs->get_attributes() );
	}

	public function test_has_attribute() {
		$attrs = new Attributes();
		$attrs->set_attribute( 'foo', 'bar' );
		$this->assertTrue( $attrs->has_attribute( 'foo' ) );
	}

	public function test_set_attribute() {
		$attrs = new Attributes();
		$attrs->set_attribute( 'foo', 'bar' );
		$this->assertSame( 'bar', $attrs->get_attribute( 'foo' ) );
	}

	public function test_set_attributes() {
		$attrs = new Attributes();
		$attrs->set_attributes( ['foo' => 'bar'] );
		$this->assertSame( 'bar', $attrs->get_attribute( 'foo' ) );
	}
}
