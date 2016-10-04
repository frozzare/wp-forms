<?php

/**
 * Add reCAPTCHA script.
 */
add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_script( 'recaptcha', 'https://www.google.com/recaptcha/api.js', [], null, true );
} );

/**
 * Add reCAPTCHA field.
 */
forms()->add_field( 'recaptcha', function ( $attributes ) {
	$attributes = array_merge( [
		'recaptcha' => []
	], $attributes );

	return sprintf( '<div class="g-recaptcha" %s></div>', forms()->html_attributes( $attributes['recaptcha'], 'data-' ) );
} );
