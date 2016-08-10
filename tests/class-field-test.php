<?php

namespace Frozzare\Tests\Forms;

use Frozzare\Forms\Field;
use Frozzare\Forms\Tag;

class Field_Test extends \WP_UnitTestCase {

	public function test_field() {
		$field = new Field( ['slug' => 'name'] );
		$this->assertInstanceOf( Tag::class, $field->field() );
		$this->assertSame( '<input type="string" id="name" name="name" />', $field->field()->__toString() );
	}

	public function test_label() {
		$field = new Field( ['slug' => 'name', 'label' => 'Name'] );
		$this->assertInstanceOf( Tag::class, $field->label() );
		$this->assertSame( '<label for="name">Name</label>', $field->label()->__toString() );
	}

	public function test_tag_name() {
		$field = new Field( ['slug' => 'name'] );
		$this->assertSame( 'input', $field->tag_name() );

		$field = new Field( ['slug' => 'name', 'type' => 'select'] );
		$this->assertSame( 'select', $field->tag_name() );
	}

	public function test_set_attributes() {
		$field = new Field( ['slug' => 'name', 'label' => 'Name'] );
		$this->assertSame( 'name', $field->slug );
		$this->assertSame( 'Name', $field->label );
		$field->set_attributes( ['slug' => 'email', 'label' => 'Email', 'class' => 'email'] );
		$this->assertSame( 'email', $field->slug );
		$this->assertSame( 'Email', $field->label );
		$this->assertSame( 'email', $field->get_attribute( 'class' ) );
	}
}