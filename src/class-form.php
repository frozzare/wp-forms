<?php

namespace Frozzare\Forms;

class Form {

	/**
	 * Form attributes.
	 *
	 * @var array
	 */
	protected $attributes = [
		'action'  => '#',
		'enctype' => 'multipart/form-data',
		'method'  => 'POST'
	];

	/**
	 * The button tag.
	 *
	 * @var \Frozzare\Forms\Tag
	 */
	protected $button;

	/**
	 * The div tag.
	 *
	 * @var \Frozzare\Forms\Tag
	 */
	protected $div;

	/**
	 * Form error messages.
	 *
	 * @var array
	 */
	protected $errors = [];

	/**
	 * Form fields.
	 *
	 * @var array
	 */
	protected $fields = [];

	/**
	 * Form name.
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * Form fields rules.
	 *
	 * @var array
	 */
	protected $rules = [];

	/**
	 * The form tag.
	 *
	 * @var \Frozzare\Forms\Tag
	 */
	protected $tag;

	/**
	 * Form constructor.
	 *
	 * @param string $name
	 * @param array  $fields
	 * @param array  $attributes
	 */
	public function __construct( $name = '', array $fields = [], array $attributes = [] ) {
		if ( empty( $name ) ) {
			return;
		}

		$this->name   = $name;
		$this->tag    = new Tag( 'form', '', $this->attributes );
		$this->div    = new Tag( 'div', '', ['class' => 'form-group'] );
		$this->button = new Tag( 'button', $name, ['class' => 'form-submit'] );

		// Set form fields.
		$this->set_fields( $fields );

		// Set form tag attributes.
		$this->tag->set_attribute( 'id', $this->name );
		$this->tag->set_attribute( 'name', $this->name );

		// Validate fields
		if ( ! empty( $_POST ) ) {
			$this->validate_fields();
		}
	}

	/**
	 * Get fields.
	 *
	 * @return array
	 */
	public function get_fields() {
		return $this->fields;
	}

	/**
	 * Get error.
	 *
	 * @param  string $name
	 *
	 * @return null|string
	 */
	public function get_error( $name ) {
		return isset( $this->errors[$name] ) ? $this->errors[$name] : null;
	}

	/**
	 * Get error messages.
	 *
	 * @return array
	 */
	public function get_errors() {
		return $this->errors;
	}

	/**
	 * Get form name.
	 *
	 * @return string
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * Get post values for fields.
	 *
	 * @return array
	 */
	public function get_values() {
		$values = [];

		foreach ( $this->fields as $field ) {
			$values[$field->name] = $field->get_value();
		}

		return $values;
	}

	/**
	 * Set error.
	 *
	 * @param string $name
	 * @param string $error
	 */
	public function set_error( $name, $error ) {
		$this->errors[$name] = $error;
	}

	/**
	 * Set errors.
	 *
	 * @param array $errors
	 */
	public function set_errors( array $errors ) {
		$this->errors = array_merge( $this->errors, $errors );
	}

	/**
	 * Prepare fields.
	 *
	 * @param  array $fields
	 *
	 * @return array
	 */
	public function set_fields( array $fields ) {
		foreach ( $fields as $key => $field ) {
			if ( is_string( $key ) ) {
				$field['name'] = $key;
			}

			if ( isset( $field['rules'] ) ) {
				$this->rules[$field['name']] = $field['rules'];
			}

			$this->fields[] = new Field( $field );
		}
	}

	/**
	 * Render form.
	 */
	public function render() {
		echo $this->tag->open();

		foreach ( $this->fields as $field ) {
			echo $this->div->open();

			echo $field->label();
			echo $field->field();

			echo $this->div->close();
		}

		echo $this->button->render() . "\n";

		echo $this->tag->close();
	}

	/**
	 * Validate fields.
	 */
	protected function validate_fields() {
		$values = $this->get_values();

		if ( empty( $values ) ) {
			return;
		}

		// Validate values with fields rules.
		$validator = new Validator( $this->rules );
		$errors    = $validator->validate( $values );

		// Merge with existing errors.
		$this->errors = array_merge( $this->errors, $errors );
	}
}
