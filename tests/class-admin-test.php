<?php

namespace Frozzare\Tests\Forms;

use Frozzare\Forms\Admin;

class Admin_Test extends \WP_UnitTestCase {

	public function test_init() {
		$this->assertTrue( post_type_exists( 'forms_data' ) );
	}

	public function test_manage_posts_custom_column() {
		$admin = new Admin;
		$this->assertNull( $admin->manage_posts_custom_column( 'form', 0 ) );
		$post_id = $this->factory->post->create();
		$this->assertNull( $admin->manage_posts_custom_column( 'form', $post_id ) );
		update_post_meta( $post_id, '_form_id', 'test' );
		$this->assertSame( 'test', $admin->manage_posts_custom_column( 'form', $post_id ) );
	}

	public function test_manage_posts_columns() {
		$admin = new Admin;
		$this->assertSame( ['form' => 'Form'], $admin->manage_posts_columns( [] ) );
	}

	public function test_pre_get_posts() {
		$admin = new Admin;
		$query = $admin->pre_get_posts( new \WP_Query );

		$this->assertEmpty( $query->get( 'meta_key' ) );
		$_GET['form'] = 'test';

		$query = $admin->pre_get_posts( new \WP_Query );
		$this->assertEmpty( $query->get( 'meta_key' ) );

		global $pagenow;
		$pagenow = 'edit.php';

		$query = $admin->pre_get_posts( new \WP_Query );
		$this->assertSame( '_form_id', $query->get( 'meta_key' ) );
		$this->assertSame( 'test', $query->get( 'meta_value' ) );
	}

	public function test_restrict_manage_posts() {
		$admin = new Admin;
		$admin->restrict_manage_posts();
		$this->expectOutputRegex( '/<select/' );
	}
}