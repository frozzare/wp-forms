<?php

/**
 * Plugin Name: Forms
 * Plugin URI: https://github.com/frozzare/wp-plugin-boilerplate
 * Description: Create forms in WordPress easy
 * Author: Fredrik Forsmo
 * Author URI: https://github.com/frozzare
 * Version: 1.0.0
 * Textdomain: forms
 */

// Load Composer.
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
}

/**
 * Bootstrap forms admin.
 *
 * @return \Frozzare\Forms\Admin;
 */
add_action( 'plugins_loaded', function () {
	new \Frozzare\Forms\Admin;
	new \Frozzare\Forms\Ajax;

	// Load custom fields.
	require_once __DIR__ . '/src/fields.php';
} );
