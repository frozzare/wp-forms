<?php

namespace Frozzare\Forms;

class Ajax {

	/**
	 * The action prefix for ajax actions.
	 *
	 * @var string
	 */
	protected $action_prefix = 'forms_ajax';

	/**
	 * The construct.
	 */
	public function __construct() {
		$this->setup_actions();
	}

	/**
	 * Add ajax endpoint.
	 */
	public function add_endpoint() {
		add_rewrite_tag( '%action%', '([^/]*)' );
		add_rewrite_rule( 'forms-ajax/([^/]*)/?', 'index.php?action=$matches[1]', 'top' );
	}

	/**
	 * Handle ajax.
	 */
	public function handle_ajax() {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}

		$ajax_action = '';

		if ( ! empty( $_GET['action'] ) ) {
			$ajax_action = sanitize_text_field( $_GET['action'] );
		}

		if ( has_action( $this->action_prefix . $ajax_action ) !== false ) {
			if ( ! defined( 'DOING_AJAX' ) ) {
				define( 'DOING_AJAX', true );
			}

			if ( ! defined( 'DOING_FORMS_AJAX' ) ) {
				define( 'DOING_FORMS_AJAX', true );
			}

			status_header( 200 );
			do_action( $this->action_prefix . $ajax_action );
			wp_die();
		}
	}

	/**
	 * Render error message.
	 *
	 * @param string $message
	 */
	public function render_error( $message ) {
		wp_send_json( [
			'success' => false,
			'message' => $message
		] );
	}

	/**
	 * Save form data.
	 */
	public function save() {
		if ( ! isset( $_GET['form'] ) ) {
			$this->render_error( 'Form query string is missing' );

			return;
		}

		$form = forms()->get( sanitize_text_field( $_GET['form'] ) );

		if ( ! $form ) {
			$this->render_error( 'No form found' );

			return;
		}

		wp_send_json( [
			'success' => $form->save(),
		    'errors'  => $form->get_errors()
		] );
	}

	/**
	 * Setup action hooks.
	 */
	protected function setup_actions() {
		add_action( 'init', [$this, 'add_endpoint'] );
		add_action( 'parse_request', [$this, 'handle_ajax'] );

		// Ajax actions.
		add_action( $this->action_prefix . 'save', [$this, 'save'] );
	}
}
