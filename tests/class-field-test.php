<?php

namespace Frozzare\Tests\Forms;

use Frozzare\Forms\Field;
use Frozzare\Forms\Tag;
use Frozzare\Tank\Container;

class Field_Test extends \WP_UnitTestCase {

	public function test_field() {
		$field = new Field( ['name' => 'name'] );
		$this->assertInstanceOf( Tag::class, $field->field() );
		$this->assertSame( '<input class="form-control" type="string" id="name" name="name" />', $field->field()->__toString() );
	}

	public function test_custom_field() {
		$container = new Container;
		$container->bind( 'field_custom', function ( $attributes ) {
			return '<p>Hello</p>';
		} );
		$field = new Field( ['name' => 'name', 'type' => 'custom'] );
		$field->set_container( $container );
		$this->assertSame( '<p>Hello</p>', $field->field()->__toString() );
	}

	public function test_label() {
		$field = new Field( ['name' => 'name', 'label' => 'Name'] );
		$this->assertInstanceOf( Tag::class, $field->label() );
		$this->assertSame( '<label for="name">Name</label>', $field->label()->__toString() );
	}

	public function test_tag_name() {
		$field = new Field( ['name' => 'name'] );
		$this->assertSame( 'input', $field->tag_name() );

		$field = new Field( ['name' => 'name', 'type' => 'select'] );
		$this->assertSame( 'select', $field->tag_name() );
	}

	public function test_set_attributes() {
		$field = new Field( ['name' => 'name', 'label' => 'Name'] );
		$this->assertSame( 'name', $field->name );
		$this->assertSame( 'Name', $field->label );
		$field->set_attributes( ['name' => 'email', 'label' => 'Email', 'class' => 'email'] );
		$this->assertSame( 'email', $field->name );
		$this->assertSame( 'Email', $field->label );
		$this->assertSame( 'email', $field->get_attribute( 'class' ) );
		$field->set_attributes( ['attributes' => ['class' => 'foo']] );
		$this->assertSame( 'foo', $field->get_attribute( 'class' ) );
	}
}
