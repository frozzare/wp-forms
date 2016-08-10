<?php

class Contact extends \Frozzare\Forms\Form {

	/**
	 * Get name.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'contact';
	}

	/**
	 * Get fields.
	 *
	 * @return array
	 */
	public function get_fields() {
		return [
			'name' => [
				'label' => 'Name',
				'rules' => 'required|max:250'
			]
		];
	}

	/**
	 * Get attributes.
	 *
	 * @return array
	 */
	public function get_attributes() {
		return [];
	}
}
