<?php

namespace Frozzare\Forms;

use Frozzare\Tank\Container;

class Forms extends Container {

	/**
	 * The class instance.
	 *
	 * @var \Frozzare\Forms\Forms
	 */
	protected static $instance;

	/**
	 * Get the class instance.
	 *
	 * @return \Frozzare\Forms\Forms
	 */
	public static function instance() {
		if ( ! isset( static::$instance ) ) {
			static::$instance = new static;
		}

		return static::$instance;
	}

	/**
	 * Get form.
	 *
	 * @param  string $key
	 *
	 * @return \Frozzare\Forms\Form
	 */
	public function get( $key ) {
		return $this->make( $key );
	}

	/**
	 * Register form.
	 *
	 * @param  string $key
	 * @param  array  $fields
	 * @param  array  $attributes
	 *
	 * @return mixed
	 */
	public function register( $key, array $fields = [], array $attributes = [] ) {
		if ( class_exists( $key ) || $key instanceof Form ) {
			if ( class_exists( $key ) ) {
				$key = new $key;
			}

			$form = new Form( $key->get_name(), $key->get_fields() ?: [], $key->get_attributes() ?: [] );
			$key  = $key->get_name();
		} else {
			$form = new Form( $key, $fields, $attributes );
		}

		return $this->bind( strtolower( $key ), $form );
	}

	/**
	 * Render form if form can be found.
	 *
	 * @param string $key
	 */
	public function render( $key ) {
		if ( $form = $this->make( $key ) ) {
			if ( $form instanceof Form ) {
				$form->render();
			}
		}
	}
}
