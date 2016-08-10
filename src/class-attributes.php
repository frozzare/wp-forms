<?php

namespace Frozzare\Forms;

use InvalidArgumentException;

class Attributes {

	/**
	 * Attributes.
	 *
	 * @var array
	 */
	protected $attributes = [];

	/**
	 * Get attribute.
	 *
	 * @param  string $name
	 *
	 * @throws InvalidArgumentException if attribute name don't exists.
	 *
	 * @return mixed
	 */
	public function get_attribute( $name ) {
		if ( ! $this->has_attribute( $name ) ) {
			throw new InvalidArgumentException( 'Attribute tag must be a string and not empty' );
		}

		return $this->attributes[$name];
	}

	/**
	 * Get attributes.
	 *
	 * @return array
	 */
	public function get_attributes() {
		return $this->attributes;
	}

	/**
	 * Check if attribute exists or not.
	 *
	 * @param  string $name
	 *
	 * @return bool
	 */
	public function has_attribute( $name ) {
		return array_key_exists( $name, $this->attributes );
	}

	/**
	 * Set attribute.
	 *
	 * @param  string $name
	 * @param  mixed  $value
	 *
	 * @throws InvalidArgumentException if attribute name isn't a string or empty.
	 */
	public function set_attribute( $name, $value ) {
		if ( ! is_string( $name ) || empty( $name ) ) {
			throw new InvalidArgumentException( 'Attribute tag must be a string and not empty' );
		}

		$this->attributes[$name] = $value;
	}

	/**
	 * Set attributes.
	 *
	 * @param array $attributes
	 */
	public function set_attributes( array $attributes ) {
		foreach ( $attributes as $name => $value ) {
			$this->set_attribute( $name, $value );
		}
	}
}
