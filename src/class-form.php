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
	 * Form id.
	 *
	 * @var string
	 */
	protected $id;

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
	 * The form store instance.
	 *
	 * @var \Frozzare\Forms\Store
	 */
	protected $store;

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

		$this->store  = new Store;
		$this->name   = $name;
		$this->id     = strtolower( $name );
		$this->tag    = new Tag( 'form', '', $this->attributes );
		$this->div    = new Tag( 'div', '', ['class' => 'form-group'] );
		$this->button = new Tag( 'button', esc_html__( 'Save', 'forms' ), ['class' => 'form-submit'] );

		// Set form fields.
		$this->set_fields( $fields );

		// Set form tag attributes.
		$this->tag->set_attribute( 'id', strtolower( $this->name ) );
		$this->tag->set_attribute( 'name', strtolower( $this->name ) );

		// Save fields if nonce is verified and fields validated correct.
		if ( ! empty( $_POST ) && $this->verify_nonce() ) {
			$this->validate_fields();
			$this->save();
		}
	}

	/**
	 * Modify button.
	 *
	 * @param  string $content
	 * @param  array  $attributes
	 *
	 * @return \Frozzare\Forms\Form
	 */
	public function button( $content, array $attributes = [] ) {
		$this->button->set_content( $content );
		$this->button->set_attributes( $attributes );

		return $this;
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
	 * Get form id.
	 *
	 * @return string
	 */
	public function get_id() {
		return $this->id;
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
	 * Render form.
	 */
	public function render() {
		// @codingStandardsIgnoreStart
		echo $this->tag->open();

		// Create nonce field.
		wp_nonce_field( 'forms_' . $this->id, '_forms_nonce' );

		foreach ( $this->fields as $field ) {
			echo $this->div->open();

			echo $field->label();
			echo $field->field();

			echo $this->div->close();
		}

		echo $this->button->render();
		echo $this->tag->close();
		// @codingStandardsIgnoreEnd
	}

	/**
	 * Save form data.
	 *
	 * @return bool
	 */
	public function save() {
		if ( ! empty( $this->errors ) ) {
			return;
		}

		return $this->store->save( $this->id, $this->get_values() );
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

	/**
	 * Verify nonce.
	 *
	 * @return bool
	 */
	protected function verify_nonce() {
		if ( ! isset( $_POST['_forms_nonce'] ) ) {
			return false;
		}

		return wp_verify_nonce( $_POST['_forms_nonce'], 'forms_' . $this->id );
	}
}
