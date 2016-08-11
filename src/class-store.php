<?php

namespace Frozzare\Forms;

class Store {

	/**
	 * Save form data.
	 *
	 * @param  string $id
	 * @param  string $data
	 *
	 * @return bool
	 */
	public function save( $id, $data ) {
		if ( empty( $id ) || empty( $data ) ) {
			return false;
		}

		$post_id = wp_insert_post( [
			'post_status' => 'publish',
			'post_title'  => current_time( 'mysql' ),
			'post_type'   => 'forms_data'
		] );

		if ( ! $post_id ) {
			return false;
		}

		foreach ( $data as $key => $value ) {
			// If update post meta returns false, force delete post.
			if ( ! update_post_meta( $post_id, $key, $value ) ) {
				return wp_delete_post( $post_id, true );
			}
		}

		// If update post meta returns false, force delete post.
		if ( update_post_meta( $post_id, forms()->id_key(), $id ) ) {
			return true;
		}

		return wp_delete_post( $post_id, true );
	}
}
