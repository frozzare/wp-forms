<?php

class Contact extends \Frozzare\Forms\Form {

	/**
	 * Get attributes.
	 *
	 * @return array
	 */
	public function attributes() {
		return [];
	}

	/**
	 * Get fields.
	 *
	 * @return array
	 */
	public function fields() {
		return [
			'name' => [
				'label' => 'Name',
				'rules' => 'required|max:250'
			]
		];
	}

	/**
	 * Get name.
	 *
	 * @return string
	 */
	public function name() {
		return 'contact';
	}
}
