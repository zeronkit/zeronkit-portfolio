<?php
/**
 * Content types: projects (public portfolio), services and kind words (edited in the
 * admin, shown by the theme) and inquiries (private copies of contact form messages).
 *
 * The names (zkt_project and so on) are shared with the ZeronKit themes and with the
 * older ZeronKit Themes Core plugin, so existing content keeps working.
 *
 * @package zeronkit-portfolio
 */

defined( 'ABSPATH' ) || exit;

/** Registers the taxonomies and post types. */
function zeronkit_portfolio_register_types() {
	// Taxonomies first, so /portfolio/category/... rules come before the project attachment rules.
	register_taxonomy(
		'zkt_category',
		'zkt_project',
		array(
			'labels'            => array(
				'name'          => __( 'Project Categories', 'zeronkit-portfolio' ),
				'singular_name' => __( 'Project Category', 'zeronkit-portfolio' ),
			),
			'hierarchical'      => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rewrite'           => array(
				'slug'       => 'portfolio/category',
				'with_front' => false,
			),
		)
	);

	register_taxonomy(
		'zkt_project_type',
		'zkt_project',
		array(
			'labels'            => array(
				'name'          => __( 'Project Types', 'zeronkit-portfolio' ),
				'singular_name' => __( 'Project Type', 'zeronkit-portfolio' ),
			),
			'hierarchical'      => false,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rewrite'           => array(
				'slug'       => 'portfolio/type',
				'with_front' => false,
			),
		)
	);

	register_taxonomy(
		'zkt_place',
		'zkt_project',
		array(
			'labels'            => array(
				'name'          => __( 'Places', 'zeronkit-portfolio' ),
				'singular_name' => __( 'Place', 'zeronkit-portfolio' ),
			),
			'hierarchical'      => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rewrite'           => array(
				'slug'       => 'portfolio/place',
				'with_front' => false,
			),
		)
	);

	// Only real words from real clients. The sample content adds none on purpose.
	register_post_type(
		'zkt_testimonial',
		array(
			'labels'        => array(
				'name'          => __( 'Kind Words', 'zeronkit-portfolio' ),
				'singular_name' => __( 'Kind Word', 'zeronkit-portfolio' ),
				'add_new_item'  => __( 'Add a client quote or press mention', 'zeronkit-portfolio' ),
				'edit_item'     => __( 'Edit Kind Word', 'zeronkit-portfolio' ),
			),
			'public'        => false,
			'show_ui'       => true,
			'show_in_rest'  => true,
			'menu_icon'     => 'dashicons-format-quote',
			'menu_position' => 8,
			'supports'      => array( 'title', 'editor', 'page-attributes' ),
		)
	);

	register_post_type(
		'zkt_project',
		array(
			'labels'        => array(
				'name'          => __( 'Projects', 'zeronkit-portfolio' ),
				'singular_name' => __( 'Project', 'zeronkit-portfolio' ),
				'add_new_item'  => __( 'Add New Project', 'zeronkit-portfolio' ),
				'edit_item'     => __( 'Edit Project', 'zeronkit-portfolio' ),
				'all_items'     => __( 'All Projects', 'zeronkit-portfolio' ),
			),
			'public'        => true,
			'has_archive'   => 'portfolio',
			'rewrite'       => array(
				'slug'       => 'portfolio',
				'with_front' => false,
			),
			'menu_icon'     => 'dashicons-camera',
			'menu_position' => 5,
			'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes', 'custom-fields' ),
			'show_in_rest'  => true,
		)
	);

	register_post_type(
		'zkt_service',
		array(
			'labels'        => array(
				'name'          => __( 'Services', 'zeronkit-portfolio' ),
				'singular_name' => __( 'Service', 'zeronkit-portfolio' ),
				'add_new_item'  => __( 'Add New Service', 'zeronkit-portfolio' ),
				'edit_item'     => __( 'Edit Service', 'zeronkit-portfolio' ),
			),
			'public'        => false,
			'show_ui'       => true,
			'show_in_rest'  => true,
			'menu_icon'     => 'dashicons-tag',
			'menu_position' => 6,
			'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes', 'custom-fields' ),
		)
	);

	register_post_type(
		'zkt_inquiry',
		array(
			'labels'          => array(
				'name'          => __( 'Inquiries', 'zeronkit-portfolio' ),
				'singular_name' => __( 'Inquiry', 'zeronkit-portfolio' ),
				'edit_item'     => __( 'Inquiry', 'zeronkit-portfolio' ),
			),
			'public'          => false,
			'show_ui'         => true,
			'menu_icon'       => 'dashicons-email-alt',
			'menu_position'   => 7,
			'supports'        => array( 'title', 'editor', 'custom-fields' ),
			'capability_type' => 'post',
			'capabilities'    => array( 'create_posts' => 'do_not_allow' ),
			'map_meta_cap'    => true,
		)
	);
}
add_action( 'init', 'zeronkit_portfolio_register_types' );

// The portfolio archive lists every project in the editor's order, so the category filters can work in the browser.
add_action(
	'pre_get_posts',
	static function ( WP_Query $q ) {
		if ( is_admin() || ! $q->is_main_query() ) {
			return;
		}
		if ( $q->is_post_type_archive( 'zkt_project' ) || $q->is_tax( array( 'zkt_category', 'zkt_project_type', 'zkt_place' ) ) ) {
			$q->set( 'posts_per_page', 100 ); // shortcut: filters need every item on one page, add pagination past about 100 projects.
			$q->set(
				'orderby',
				array(
					'menu_order' => 'ASC',
					'date'       => 'DESC',
				)
			);
		}
	}
);
