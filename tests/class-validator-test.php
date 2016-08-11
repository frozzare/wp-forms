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
}