<?php

namespace Frozzare\Tests\Forms;

use Frozzare\Forms\Form;
use Frozzare\Forms\Forms;

class Forms_Test extends \WP_UnitTestCase {

	public function test_register() {
		Forms::instance()->register( 'contact', [
			'name' => [
				'label' => 'Name'
			]
		] );

		$this->assertInstanceOf( Form::class, Forms::instance()->get( 'contact' ) );
		$this->assertSame( 'contact', Forms::instance()->get( 'contact' )->get_name() );
	}

	public function test_register_class() {
		require_once __DIR__ . '/fixtures/class-contact.php';

		Forms::instance()->register( Contact::class );

		$this->assertInstanceOf( Form::class, Forms::instance()->get( 'contact' ) );
		$this->assertSame( 'contact', Forms::instance()->get( 'contact' )->get_name() );
	}

	public function test_render() {
		Forms::instance()->register( 'contact', [
			'name' => [
				'label' => 'Name'
			]
		] );

		Forms::instance()->render( 'contact' );

		$this->expectOutputRegex( '/<input/' );
	}
}