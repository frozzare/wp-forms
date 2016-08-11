<?php

namespace Frozzare\Tests\Forms;

use Frozzare\Forms\Form;

class Form_Test extends \WP_UnitTestCase {

	public function test_button() {
		$form = new Form( 'contact' );
		$form->render();
		$this->expectOutputRegex( '/\<button class=\"form-submit\">Save<\/button>/' );
	}

	public function test_button_modified() {
		$form = new Form( 'contact' );
		$form->button('Send', ['id' => 'test'])->render();
		$this->expectOutputRegex( '/\<button class=\"form-submit\" id=\"test\">Send<\/button>/' );
	}

	public function test_get_name() {
		$form = new Form( 'contact' );
		$this->assertSame( 'contact', $form->get_name() );
	}

	public function test_get_error() {
		$form = new Form( 'contact' );
		$form->set_error( 'name', 'not ok' );
		$this->assertSame( 'not ok', $form->get_error( 'name' ) );
		$this->assertNull( $form->get_error( 'fake' ) );
	}

	public function test_get_errors() {
		$form = new Form( 'contact' );
		$this->assertEmpty( $form->get_errors() );
		$form->set_error( 'name', 'not ok' );
		$this->assertSame( ['name' => 'not ok'], $form->get_errors() );
	}

	public function test_get_values() {
		$form = new Form( 'contact' );
		$form->set_fields( [['name' => 'name']] );
		$this->assertEmpty( $form->get_values()['name'] );
		$_POST['name'] = 'Fredrik';
		$this->assertSame( 'Fredrik', $form->get_values()['name'] );
	}

	public function test_set_error() {
		$form = new Form( 'contact' );
		$form->set_error( 'name', 'not ok' );
		$this->assertSame( 'not ok', $form->get_error( 'name' ) );
	}

	public function test_set_errors() {
		$form = new Form( 'contact' );
		$form->set_errors( ['name' => 'not ok'] );
		$this->assertSame( 'not ok', $form->get_error( 'name' ) );
	}

	public function test_set_fields() {
		$form = new Form( 'contact' );
		$form->set_fields( [['name' => 'name']] );
		$this->assertSame( 'name', $form->get_fields()[0]->name );
	}

	public function test_render() {
		$form = new Form( 'contact' );
		$form->render();
		$this->expectOutputRegex( '/\<form action=\"#\" enctype=\"multipart\/form-data\" method=\"POST\" id=\"contact\" name=\"contact\"\>/' );
	}
}