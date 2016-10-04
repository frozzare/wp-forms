<?php

namespace Frozzare\Forms;

use Frozzare\Tank\Container;

class Containerable {

	/**
	 * The container.
	 *
	 * @var \Frozzare\Tank\Container
	 */
	protected $container;

	/**
	 * Set container.
	 *
	 * @param \Frozzare\Tank\Container $container
	 */
	public function set_container( Container $container ) {
		$this->container = $container;
	}
}
