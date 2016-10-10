<?php

namespace Frozzare\Tests\Forms;

use Frozzare\Forms\Ajax;

class Ajax_Test extends \WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
		$_GET       = [];
		$_POST      = [];
		$this->ajax = new Ajax();
		add_filter( 'wp_die_ajax_handler', [$this, 'get_wp_die_handler'], 1, 1 );
	}

	public function tearDown() {
		parent::tearDown();
		unset( $_GET, $_POST, $this->ajax );
		remove_filter( 'wp_die_ajax_handler', [$this, 'get_wp_die_handler'], 1, 1 );
	}

	public function wp_die_handler( $message ) {
	}

	public function test_actions() {
		$this->assertSame( 10, has_action( 'init', [$this->ajax, 'add_endpoint'] ) );
		$this->assertSame( 10, has_action( 'parse_request', [$this->ajax, 'handle_ajax'] ) );
	}

	public function test_endpoint() {
		$this->ajax->add_endpoint();
		global $wp_rewrite;
		$this->assertNotNull( $wp_rewrite->extra_rules_top['forms-ajax/([^/]*)/?'] );
		$this->assertSame( 'index.php?action=$matches[1]', $wp_rewrite->extra_rules_top['forms-ajax/([^/]*)/?'] );
	}

	public function test_handle_ajax_wp_query() {
		global $wp_query;
		$wp_query = null;
		$this->assertNull( $this->ajax->handle_ajax() );
	}

	public function test_handle_ajax_doing_ajax() {
		$this->assertNull( $this->ajax->handle_ajax() );
	}

	public function test_handle_ajax() {
		$this->assertNull( $this->ajax->handle_ajax() );
		$this->expectOutputRegex( '//' );
	}

	public function test_save() {
		if ( ! defined( 'DOING_AJAX' ) ) {
			define( 'DOING_AJAX', true );
		}

		$_GET = [
			'action' => 'save',
		    'form'   => 'contact'
		];

		$_POST['_forms_nonce'] = wp_create_nonce( 'forms_contact' );

		forms()->add( 'contact', [
			'name' => [
				'label' => 'Name',
				'rules' => 'required'
			]
		] )->save( __return_true() );


		$this->ajax->save();
		$this->expectOutputRegex( '/\{\"success\":false,\"errors\":\{\"name.required\":\"\"\}\}/' );
	}

	public function test_render_error() {
		if ( ! defined( 'DOING_AJAX' ) ) {
			define( 'DOING_AJAX', true );
		}

		$this->ajax->render_error( 'No form found' );
		$this->expectOutputRegex( '/\{\"success\":false,\"message\"\:\"No form found\"\}/' );
	}
}
