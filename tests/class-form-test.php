<?php

namespace Frozzare\Tests\Forms;

use Frozzare\Forms\Form;

class Form_Test extends \WP_UnitTestCase {

	public function test_get_name() {
		$form = new Form( 'contact' );
		$this->assertSame( 'contact', $form->get_name() );
	}

	public function test_set_fields() {
		$form = new Form( 'contact' );
		$form->set_fields( [['slug' => 'name']] );
		$this->assertSame( 'name', $form->get_fields()[0]->slug );
	}

	public function test_render() {
		$form = new Form( 'contact' );
		$form->render();
		$this->expectOutputString( '<form action="#" enctype="multipart/form-data" method="POST" id="contact" name="contact"></form>' );
	}
}