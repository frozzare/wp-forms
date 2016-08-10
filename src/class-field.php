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
	 * The field rules.
	 *
	 * @var string
	 */
	public $rules = '';

	/**
	 * The field slug.
	 *
	 * @var string
	 */
	public $slug = '';

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
		$this->set_attribute( 'id', str_replace( '[]', '', $this->slug ) );
		$this->set_attribute( 'name', $this->slug );

		$this->tag    = $this->tag_name();
		$this->escape = $escape;
		$this->xhtml  = $this->tag === 'input';
	}

	/**
	 * Render field.
	 */
	public function field() {
		return new Tag( $this->tag_name(), $this->value(), $this->attributes, empty( $this->items ) ? $this->escape : false, $this->xhtml );
	}

	/**
	 * Get tag name.
	 *
	 * @return string
	 */
	public function tag_name() {
		switch ( $this->get_attribute( 'type' ) ) {
			case 'select':
			case 'textarea':
				return $this->get_attribute( 'type' );
			case 'input':
				return $this->tag;
			default:
				return $this->tag;
		}
	}

	/**
	 * Render label.
	 */
	public function label() {
		return new Tag( 'label', $this->label, ['for' => $this->slug] );
	}

	/**
	 * Set attributes.
	 *
	 * @param array $attributes
	 */
	public function set_attributes( array $attributes ) {
		foreach ( $attributes as $key => $value ) {
			if ( property_exists( $this, $key ) ) {
				$this->$key = $value;
			} else {
				$this->attributes[$key] = $value;
			}
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
			$item['slug'] = $this->slug . '[]';
			$html .= ( new Field( $item, 'option', [], false ) )->field();
		}

		return $html;
	}
}
