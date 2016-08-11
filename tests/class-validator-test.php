<?php

namespace Frozzare\Tests\Forms;

use Frozzare\Forms\Validator;

class Validator_Test extends \WP_UnitTestCase {

	public function test_validate_alpha() {
		$validator = new Validator( ['first_name' => 'alpha'] );
		$this->assertEmpty( $validator->validate( ['first_name' => 'Fredrik'] ) );
		$this->assertNotEmpty( $validator->validate( ['first_name' => '123'] ) );
	}

	public function test_validate_alpha_num() {
		$validator = new Validator( ['first_name' => 'alpha_num'] );
		$this->assertEmpty( $validator->validate( ['first_name' => 'Fredrik'] ) );
		$this->assertEmpty( $validator->validate( ['first_name' => 'Fredrik123'] ) );
		$this->assertNotEmpty( $validator->validate( ['first_name' => '30^'] ) );
	}

	public function test_validate_array() {
		$validator = new Validator( ['first_name' => 'array'] );
		$this->assertEmpty( $validator->validate( ['first_name' => []] ) );
		$this->assertEmpty( $validator->validate( ['first_name' => [1]] ) );
		$this->assertNotEmpty( $validator->validate( ['first_name' => null] ) );
		$this->assertNotEmpty( $validator->validate( ['first_name' => 'Fredrik'] ) );
	}

	public function test_validate_bool() {
		$validator = new Validator( ['first_name' => 'bool'] );
		$this->assertEmpty( $validator->validate( ['first_name' => true] ) );
		$this->assertEmpty( $validator->validate( ['first_name' => false] ) );
		$this->assertEmpty( $validator->validate( ['first_name' => '1'] ) );
		$this->assertEmpty( $validator->validate( ['first_name' => '0'] ) );
		$this->assertNotEmpty( $validator->validate( ['first_name' => null] ) );
		$this->assertNotEmpty( $validator->validate( ['first_name' => 'Fredrik'] ) );
	}

	public function test_validate_digit() {
		$validator = new Validator( ['first_name' => 'digit'] );
		$this->assertEmpty( $validator->validate( ['first_name' => '123'] ) );
		$this->assertNotEmpty( $validator->validate( ['first_name' => 'Fredrik'] ) );
	}

	public function test_validate_between() {
		$validator = new Validator( ['first_name' => 'between:7,10'] );
		$this->assertEmpty( $validator->validate( ['first_name' => 'Fredrik'] ) );
		$this->assertNotEmpty( $validator->validate( ['first_name' => 'Longer than seven'] ) );
	}

	public function test_validate_float() {
		$validator = new Validator( ['first_name' => 'float'] );
		$this->assertEmpty( $validator->validate( ['first_name' => 12.12] ) );
		$this->assertNotEmpty( $validator->validate( ['first_name' => 1212] ) );
		$this->assertNotEmpty( $validator->validate( ['first_name' => 'Fredrik'] ) );
	}

	public function test_validate_int() {
		$validator = new Validator( ['first_name' => 'int'] );
		$this->assertEmpty( $validator->validate( ['first_name' => 123] ) );
		$this->assertNotEmpty( $validator->validate( ['first_name' => 12.12] ) );
		$this->assertNotEmpty( $validator->validate( ['first_name' => 'Fredrik'] ) );
	}

	public function test_validate_max() {
		$validator = new Validator( ['first_name' => 'max:7'] );
		$this->assertEmpty( $validator->validate( ['first_name' => 'Fredrik'] ) );
		$this->assertNotEmpty( $validator->validate( ['first_name' => 'Longer than seven'] ) );
	}

	public function test_validate_min() {
		$validator = new Validator( ['first_name' => 'min:7'] );
		$this->assertEmpty( $validator->validate( ['first_name' => 'Fredrik'] ) );
		$this->assertNotEmpty( $validator->validate( ['first_name' => 'Elli'] ) );
	}

	public function test_validate_numeric() {
		$validator = new Validator( ['first_name' => 'numeric'] );
		$this->assertEmpty( $validator->validate( ['first_name' => '123'] ) );
		$this->assertEmpty( $validator->validate( ['first_name' => '123.3'] ) );
		$this->assertNotEmpty( $validator->validate( ['first_name' => 'Fredrik'] ) );
	}

	public function test_validate_required() {
		$validator = new Validator( ['first_name' => 'required'] );
		$this->assertEmpty( $validator->validate( ['first_name' => 'Elli'] ) );
		$this->assertNotEmpty( $validator->validate( ['first_name' => ''] ) );
	}

	public function test_validate_size() {
		$validator = new Validator( ['first_name' => 'size:7'] );
		$this->assertEmpty( $validator->validate( ['first_name' => 'Fredrik'] ) );
		$this->assertNotEmpty( $validator->validate( ['first_name' => 'Elli'] ) );
	}

	public function test_validate_string() {
		$validator = new Validator( ['first_name' => 'string'] );
		$this->assertEmpty( $validator->validate( ['first_name' => 'Fredrik'] ) );
		$this->assertEmpty( $validator->validate( ['first_name' => 'Fredrik123'] ) );
		$this->assertNotEmpty( $validator->validate( ['first_name' => null] ) );
	}

	public function test_validate_url() {
		$validator = new Validator( ['first_name' => 'url'] );
		$this->assertEmpty( $validator->validate( ['first_name' => 'https://localhost'] ) );
		$this->assertEmpty( $validator->validate( ['first_name' => 'http://wordpress.org'] ) );
		$this->assertNotEmpty( $validator->validate( ['first_name' => 'Fredrik123'] ) );
		$this->assertNotEmpty( $validator->validate( ['first_name' => null] ) );
		$this->assertNotEmpty( $validator->validate( ['first_name' => 'foo://bar'] ) );
	}
}