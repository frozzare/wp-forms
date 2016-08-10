<?php

namespace Frozzare\Forms;

use InvalidArgumentException;

class Tag extends Attributes {

	/**
	 * Tag attributes.
	 *
	 * @var array
	 */
	protected $attributes = [];

	/**
	 * Escape value.
	 *
	 * @var bool
	 */
	protected $escape = true;

	/**
	 * Tag name.
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * Tag value.
	 *
	 * @var mixed
	 */
	protected $value;

	/**
	 * Xhtml tag.
	 *
	 * @var bool
	 */
	protected $xhtml = false;

	/**
	 * Tag constructor.
	 *
	 * @param string $name
	 * @param null   $value
	 * @param array  $attributes
	 * @param bool   $escape
	 * @param bool   $xhtml
	 */
	public function __construct( $name, $value = null, array $attributes = [], $escape = true, $xhtml = false ) {
		$this->name       = $name;
		$this->value      = $value;
		$this->attributes = $attributes;
		$this->escape     = $escape;
		$this->xhtml      = $xhtml;
	}

	/**
	 * Get attributes converted to html.
	 *
	 * @param  array $attributes
	 *
	 * @return string
	 */
	public function attributes( array $attributes = [] ) {
		$html = '';

		if ( empty( $attributes ) && ! empty( $this->attributes ) ) {
			$attributes = $this->attributes;
		}

		foreach ( $attributes as $name => $value ) {
			if ( ! is_array( $value ) && ! is_object( $value ) ) {
				$value = $this->escape( $value );
			} else {
				$value = json_encode( $value, JSON_UNESCAPED_UNICODE );
			}

			$name = $this->escape( $name, true );

			if ( strpos( $value, '"' ) !== false ) {
				$value = "'$value'";
			} else {
				$value = "\"$value\"";
			}

			$html .= " $name=" . $value;
		}

		return $html;
	}

	/**
	 * Get close tag.
	 *
	 * @return string
	 */
	public function close() {
		return sprintf( '</%s>', $this->name );
	}

	/**
	 * Escape value.
	 *
	 * @param  mixed $value
	 * @param  bool  $attr
	 *
	 * @return mixed
	 */
	public function escape( $value, $attr = false ) {
		if ( $this->escape && ( is_string( $value ) || is_numeric( $value ) ) ) {
			return $attr ? esc_attr( $value ) : esc_html( $value );
		}

		return $value;
	}

	/**
	 * Get tag name.
	 *
	 * @return string
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * Get tag value.
	 *
	 * @return mixed
	 */
	public function get_value() {
		return $this->value;
	}

	/**
	 * Is xhtml tag or not.
	 *
	 * @return bool
	 */
	public function is_xhtml() {
		return $this->xhtml;
	}

	/**
	 * Get open tag.
	 *
	 * @return string
	 */
	public function open() {
		return sprintf( '<%s%s%s', $this->name, $this->attributes(), $this->is_xhtml() ? ' />' : '>' );
	}

	/**
	 * Set tag value.
	 *
	 * @param  mixed $value
	 *
	 * @throws InvalidArgumentException if tag isn't a array, null, string or a instanceof Tag class
	 *
	 * @return \Frozzare\Forms\Tag
	 */
	public function set_value( $value ) {
		if ( ! ( is_array( $value ) || is_null( $value ) || is_string( $value ) || $value instanceof Tag ) ) {
			throw new InvalidArgumentException( 'Tag value must be array, null, string or a instanceof of Tag class' );
		}

		$this->value = $value;

		return $this;
	}

	/**
	 * Render html tag with attributes and values.
	 *
	 * @return string
	 */
	public function render() {
		if ( is_null( $this->value ) || $this->is_xhtml() ) {
			return $this->open();
		}

		return $this->open() . $this->value() . $this->close();
	}

	/**
	 * Get html value.
	 *
	 * @param  mixed $value
	 *
	 * @return string
	 */
	public function value( $value = null ) {
		$html = '';

		if ( is_null( $value ) && ! is_null( $this->value ) ) {
			$value = $this->value;
		}

		if ( is_string( $value ) ) {
			$html .= $this->escape( $value );
		} else if ( $value instanceof Tag ) {
			$html .= $value->render();
		} else if ( is_array( $value ) ) {
			foreach ( $value as $val ) {
				$html .= $this->value( $val );
			}
		}

		return $html;
	}

	/**
	 * Output tag as html.
	 *
	 * @return string
	 */
	public function __toString() {
		return $this->render();
	}
}
