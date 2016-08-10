<?php

namespace Frozzare\Tests\Forms;

use Frozzare\Forms\Form;
use Frozzare\Forms\Forms;

class Forms_Test extends \WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
		$this->forms = Forms::instance();
	}

	public function tearDown() {
		parent::tearDown();
		unset( $this->forms );
	}

	public function test_add() {
		$this->forms->add( 'contact', [
			'name' => [
				'label' => 'Name'
			]
		] );

		$this->assertInstanceOf( Form::class, $this->forms->get( 'contact' ) );
		$this->assertSame( 'contact', $this->forms->get( 'contact' )->get_name() );
	}

	public function test_add_class() {
		require_once __DIR__ . '/fixtures/class-contact.php';

		$this->forms->add( Contact::class );

		$this->assertInstanceOf( Form::class, $this->forms->get( 'contact' ) );
		$this->assertSame( 'contact', $this->forms->get( 'contact' )->get_name() );
	}

	public function test_errors_empty() {
		$this->forms->add( 'contact', [
			'name' => [
				'label' => 'Name',
				'rules' => 'required'
			]
		] );

		$this->assertEmpty( $this->forms->errors( 'contact' ) );
	}

	public function test_errors() {
		$_POST['name'] = '';

		$this->forms->add( 'contact', [
			'name' => [
				'label' => 'Name',
				'rules' => 'required'
			]
		] );

		$this->assertArrayHasKey( 'name.required', $this->forms->errors( 'contact' ) );
	}

	public function test_render() {
		$this->forms->add( 'contact', [
			'name' => [
				'label' => 'Name'
			]
		] );

		$this->forms->render( 'contact' );

		$this->expectOutputRegex( '/<input/' );
	}
}