<?php

namespace Frozzare\Forms;

class Field extends Attributes {

	/**
	 * The field attributes.
	 *
	 * @var array
	 */
	protected $attributes = [
		'class' => 'form-control',
		'type'  => 'string'
	];

	/**
	 * Escape value.
	 *
	 * @var bool
	 */
	public $escape = true;

	/**
	 * The field items.
	 *
	 * @var array
	 */
	public $items = [];

	/**
	 * The field label.
	 *
	 * @var string
	 */
	public $label = '';

	/**
	 * The field name.
	 *
	 * @var string
	 */
	public $name = '';

	/**
	 * The field rules.
	 *
	 * @var string
	 */
	public $rules = '';

	/**
	 * The field tag.
	 *
	 * @var string
	 */
	public $tag = 'input';

	/**
	 * The field text.
	 *
	 * @var string
	 */
	public $text = '';

	/**
	 * Xhtml tag.
	 *
	 * @var bool
	 */
	protected $xhtml = false;

	/**
	 * Field constructor.
	 *
	 * @param array  $attributes
	 * @param string $tag
	 * @param bool   $escape
	 */
	public function __construct( array $attributes = [], $tag = 'input', $escape = true ) {
		$this->set_attributes( $attributes );

		$this->tag    = $this->tag_name( $tag );
		$this->escape = $escape;
		$this->xhtml  = $this->tag === 'input';
	}

	/**
	 * Render custom field if bound and container exists.
	 */
	protected function custom_field() {
		if ( is_null( $this->container ) ) {
			return;
		}

		$type = $this->get_attribute( 'type' );
		$key  = 'field_' . $type;

		if ( ! $this->container->bound( $key ) ) {
			return;
		}

		// Support both ob and a return value.
		ob_start();
		$value = $this->container->make( $key, [$this->attributes] );
		$html  = ltrim( ob_get_clean() );

		// If return value is a string and not empty it should be used instead.
		if ( is_string( $value ) && ! empty( $value ) ) {
			$html = $value;
		}

		// If a instanceof `Tag` is returned we can return that.
		if ( $value instanceof Tag ) {
			return $value;
		}

		// If a instanceof `Field` is returned we can return
		// the tag from the field.
		if ( $value instanceof Field ) {
			return $value->field();
		}

		// Create a new tag based on the html.
		$tag = new Tag;
		$tag->set_html( $html );

		return $tag;
	}

	/**
	 * Get field tag.
	 *
	 * @return \Frozzare\Forms\Tag
	 */
	public function field() {
		$escape = empty( $this->items ) ? $this->escape : false;

		// Render custom fields if bound.
		if ( $tag = $this->custom_field() ) {
			return $tag;
		}

		return new Tag( $this->tag, $this->get_content(), $this->attributes, $escape, $this->xhtml );
	}

	/**
	 * Get html content.
	 *
	 * @return string
	 */
	public function get_content() {
		$html = $this->text;

		foreach ( $this->items as $item ) {
			$item['type'] = '';

			foreach ( ['name', 'id'] as $key ) {
				if ( isset( $item[$key] ) ) {
					unset( $item[$key] );
				}
			}

			$field = new Field( $item, 'option', [], false );
			$field->set_container( $this->container );

			$html .= $field->field();
		}

		return $html;
	}

	/**
	 * Get name attribute.
	 *
	 * @return string
	 */
	public function get_name() {
		return $this->attributes['name'];
	}

	/**
	 * Get post value.
	 *
	 * @return mixed
	 */
	public function get_value() {
		if ( isset( $_POST[$this->name] ) ) {
			$value = $_POST[$this->name];

			return is_string( $value ) ? wp_unslash( $value ) : $value;
		}
	}

	/**
	 * Get tag name.
	 *
	 * @param  string $tag
	 *
	 * @return string
	 */
	public function tag_name( $tag = 'input' ) {
		switch ( $this->get_attribute( 'type' ) ) {
			case 'select':
			case 'textarea':
				return $this->get_attribute( 'type' );
			default:
				return $tag;
		}
	}

	/**
	 * Get label tag.
	 *
	 * @return \Frozzare\Forms\Tag
	 */
	public function label() {
		return new Tag( 'label', $this->label, ['for' => $this->attributes['id']] );
	}

	/**
	 * Set attributes.
	 *
	 * @param array $attributes
	 */
	public function set_attributes( array $attributes ) {
		foreach ( $attributes as $key => $value ) {
			if ( property_exists( $this, $key ) ) {
				if ( is_array( $this->$key ) && is_array( $value ) ) {
					$this->$key = array_merge( $this->$key, $value );
				} else {
					$this->$key = $value;
				}
			} else {
				$this->attributes[$key] = $value;
			}
		}

		if ( ! isset( $this->attributes['id'] ) ) {
			$this->attributes['id'] = $this->name;
		}

		if ( ! isset( $this->attributes['name'] ) ) {
			$this->attributes['name'] = $this->name;
		}
	}
}
