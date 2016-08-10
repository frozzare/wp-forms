<?php

namespace Frozzare\Forms;

class Field extends Attributes {

	/**
	 * The field attributes.
	 *
	 * @var array
	 */
	protected $attributes = [
		'type' => 'string'
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
	 * Get field tag.
	 *
	 * @return \Frozzare\Forms\Tag
	 */
	public function field() {
		$escape = empty( $this->items ) ? $this->escape : false;

		return new Tag( $this->tag, $this->value(), $this->attributes, $escape, $this->xhtml );
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

	/**
	 * Get html value.
	 *
	 * @return string
	 */
	public function value() {
		$html = $this->text;

		foreach ( $this->items as $item ) {
			$item['type'] = '';
			$item['name'] = $this->name . '[]';
			$html .= ( new Field( $item, 'option', [], false ) )->field();
		}

		return $html;
	}
}
