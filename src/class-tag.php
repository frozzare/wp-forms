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
	 * Tag content.
	 *
	 * @var mixed
	 */
	protected $content;

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
	 * @param null   $content
	 * @param array  $attributes
	 * @param bool   $escape
	 * @param bool   $xhtml
	 */
	public function __construct( $name, $content = null, array $attributes = [], $escape = true, $xhtml = false ) {
		$this->name       = $name;
		$this->content    = $content;
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

			if ( empty( $value ) ) {
				continue;
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
	 * Get html content.
	 *
	 * @param  mixed $content
	 *
	 * @return string
	 */
	public function content( $content = null ) {
		$html = '';

		if ( is_null( $content ) && ! is_null( $this->content ) ) {
			$content = $this->content;
		}

		if ( is_string( $content ) ) {
			$html .= $this->escape( $content );
		} else if ( $content instanceof Tag ) {
			$html .= $content->render();
		} else if ( is_array( $content ) ) {
			foreach ( $content as $value ) {
				$html .= $this->content( $value );
			}
		}

		return $html;
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
	 * Get tag content.
	 *
	 * @return mixed
	 */
	public function get_content() {
		return $this->content;
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
	 * Set tag content.
	 *
	 * @param  mixed $content
	 *
	 * @throws InvalidArgumentException if tag isn't a array, null, string or a instanceof Tag class
	 *
	 * @return \Frozzare\Forms\Tag
	 */
	public function set_content( $content ) {
		if ( ! ( is_array( $content ) || is_null( $content ) || is_string( $content ) || $content instanceof Tag ) ) {
			throw new InvalidArgumentException( 'Tag content must be array, null, string or a instanceof of Tag class' );
		}

		$this->content = $content;

		return $this;
	}

	/**
	 * Render html tag with attributes and content.
	 *
	 * @return string
	 */
	public function render() {
		if ( is_null( $this->content ) || $this->is_xhtml() ) {
			return $this->open();
		}

		return $this->open() . $this->content() . $this->close();
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
