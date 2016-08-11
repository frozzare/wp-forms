<?php

namespace Frozzare\Forms;

use WP_Query;

class Admin {

	/**
	 * The current form instance.
	 *
	 * @var \Frozzare\Forms\Form
	 */
	protected $form;

	/**
	 * The post id.
	 *
	 * @var int
	 */
	protected $post_id = 0;

	/**
	 * The post type.
	 *
	 * @var string
	 */
	protected $post_type = 'forms_data';

	/**
	 * Admin constructor.
	 */
	public function __construct() {
		$this->setup_hooks();
	}

	/**
	 * Add meta boxes.
	 */
	public function add_meta_boxes() {
		add_meta_box( 'form-data', esc_html__( 'Fields data', 'forms' ), [$this, 'metabox'] );
	}

	/**
	 * Admin init callback.
	 */
	public function admin_init() {
		$this->post_id = $_GET['post'];

		if ( $form = get_post_meta( $this->post_id, forms()->id_key(), true ) ) {
			$this->form = forms()->get( $form );
		}
	}

	/**
	 * Init hook callaback.
	 */
	public function init() {
		$labels = [
			'name'                  => _x( 'Forms data', 'Post Type General Name', 'forms' ),
			'singular_name'         => _x( 'Form data', 'Post Type Singular Name', 'forms' ),
			'menu_name'             => __( 'Forms data', 'forms' ),
			'name_admin_bar'        => __( 'Forms data', 'forms' ),
			'archives'              => __( 'Forms data Archives', 'forms' ),
			'parent_item_colon'     => __( 'Parent Form data:', 'forms' ),
			'all_items'             => __( 'All Forms data', 'forms' ),
			'add_new_item'          => __( 'Add New Form data', 'forms' ),
			'add_new'               => __( 'Add New', 'forms' ),
			'new_item'              => __( 'New Form data', 'forms' ),
			'edit_item'             => __( 'Edit Form data', 'forms' ),
			'update_item'           => __( 'Update Form data', 'forms' ),
			'view_item'             => __( 'View Form data', 'forms' ),
			'search_items'          => __( 'Search Form data', 'forms' ),
			'not_found'             => __( 'Not found', 'forms' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'forms' ),
			'featured_image'        => __( 'Featured Image', 'forms' ),
			'set_featured_image'    => __( 'Set featured image', 'forms' ),
			'remove_featured_image' => __( 'Remove featured image', 'forms' ),
			'use_featured_image'    => __( 'Use as featured image', 'forms' ),
			'insert_into_item'      => __( 'Insert into form data', 'forms' ),
			'uploaded_to_this_item' => __( 'Uploaded to this form data', 'forms' ),
			'items_list'            => __( 'Forms data list', 'forms' ),
			'items_list_navigation' => __( 'Forms data list navigation', 'forms' ),
			'filter_items_list'     => __( 'Filter forms data list', 'forms' )
		];

		$args = [
			'label'               => __( 'Form', 'forms' ),
			'description'         => __( 'Forms', 'forms' ),
			'labels'              => $labels,
			'supports'            => ['title',],
			'hierarchical'        => false,
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'map_meta_cap'        => true,
			'menu_position'       => 5,
			'menu_icon'           => 'dashicons-feedback',
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'rewrite'             => false,
			'capability_type'     => 'page',
			'capabilities'        => [
				'create_posts' => 'create_forms'
			]
		];

		register_post_type( $this->post_type, $args );
	}

	/**
	 * Add custom table column.
	 *
	 * @param  string $column_name
	 * @param  int    $post_id
	 */
	public function manage_posts_custom_column( $column_name, $post_id ) {
		if ( $column_name !== 'form' ) {
			return;
		}

		if ( $form = get_post_meta( $post_id, forms()->id_key(), true ) ) {
			if ( $form = forms()->get( $form ) ) {
				echo $form->get_name();
			}
		}
	}

	/**
	 * Add custom table header.
	 *
	 * @param  array $defaults
	 *
	 * @return array
	 */
	public function manage_posts_columns( array $defaults = [] ) {
		$defaults['form'] = esc_html__( 'Form', 'forms' );

		return $defaults;
	}

	/**
	 * Render metabox.
	 */
	public function metabox() {
		?>
		<table class="table">
			<thead>
			<tr>
				<th>Field</th>
				<th>Value</th>
			</tr>
			</thead>
			<tbody>
			<?php foreach ( $this->form->get_fields() as $field ): ?>
				<tr>
					<td>
						<?php echo esc_html( $field->label ); ?>
					</td>
					<td>
						<?php echo esc_html( get_post_meta( $this->post_id, $field->name, true ) ); ?>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<?php
	}

	/**
	 * Filter posts on load if `form` query string is set.
	 *
	 * @param  WP_Query $query
	 *
	 * @return WP_Query
	 */
	public function pre_get_posts( WP_Query $query ) {
		global $pagenow;

		$form = isset( $_GET['form'] ) ? sanitize_text_field( $_GET['form'] ) : '';

		if ( $pagenow === 'edit.php' && ! empty( $form ) ) {
			$query->set( 'meta_key', forms()->id_key() );
			$query->set( 'meta_value', $form );
		}

		return $query;
	}

	/**
	 * Filter form in post type list.
	 */
	public function restrict_manage_posts() {
		$forms   = forms()->all();
		$current = isset( $_GET['form'] ) ? sanitize_text_field( $_GET['form'] ) : '';
		?>
		<select name="form" id="form" class="postform">
			<option value="0" selected><?php esc_html_e( 'All forms', 'forms' ); ?></option>
			<?php foreach ( $forms as $form ): ?>
				<option value="<?php echo esc_attr( $form->get_id() ); ?>" <?php selected( $form->get_id(), $current ); ?>>
					<?php echo esc_html( $form->get_name() ); ?>
				</option>
			<?php endforeach; ?>
		</select>
		<?php
	}

	/**
	 * Setup hooks.
	 */
	protected function setup_hooks() {
		add_action( 'add_meta_boxes', [$this, 'add_meta_boxes'] );
		add_action( 'admin_init', [$this, 'admin_init'] );
		add_action( 'init', [$this, 'init'] );
		add_filter( 'pre_get_posts', [$this, 'pre_get_posts'] );
		add_action( 'restrict_manage_posts', [$this, 'restrict_manage_posts'], 9999 );
		add_action( sprintf( 'manage_%s_posts_custom_column', $this->post_type ), [
			$this,
			'manage_posts_custom_column'
		], 10, 2 );
		add_filter( sprintf( 'manage_%s_posts_columns', $this->post_type ), [$this, 'manage_posts_columns'] );
	}
}