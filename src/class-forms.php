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
		return $this->bind( $key, new Form( $key, $fields, $attributes ) );
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
