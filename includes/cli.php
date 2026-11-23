<?php
/**
 * WP-CLI command: wp zeronkit-portfolio seed [--force]
 *
 * Imports the sample pictures that ship in assets/samples and creates projects,
 * services, journal posts, pages and menus. Safe to run twice: it stops when
 * content already exists unless --force is given. Runs only from the command line.
 *
 * @package zeronkit-portfolio
 */

defined( 'ABSPATH' ) || exit;

WP_CLI::add_command(
	'zeronkit-portfolio seed',
	static function ( $args, $assoc ) {
		$dir = ZERONKIT_PORTFOLIO_DIR . 'assets/samples';
		if ( get_option( 'zeronkit_portfolio_seeded' ) && empty( $assoc['force'] ) ) {
			WP_CLI::warning( 'Sample content already exists. Use --force to add it again.' );
			return;
		}
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$data = require ZERONKIT_PORTFOLIO_DIR . 'includes/seed-data.php';
		$site = zkt_site_name();
		$text = static fn( $value ) => str_replace( '%SITE%', $site, (string) $value );

		$images = array();
		$import = static function ( $key ) use ( $dir, &$images, $data ) {
			if ( isset( $images[ $key ] ) ) {
				return $images[ $key ];
			}
			$src = "{$dir}/sample-{$key}.jpg";
			if ( ! file_exists( $src ) ) {
				WP_CLI::warning( "Missing {$src}" );
				return 0;
			}
			$tmp = wp_tempnam( $src );
			copy( $src, $tmp ); // phpcs:ignore WordPress.WP.AlternativeFunctions -- media_handle_sideload() moves the temporary copy, not the bundled file.
			$alt = $data['alts'][ $key ] ?? 'Sample picture';
			$id  = media_handle_sideload( array( 'name' => "zkt-sample-{$key}.jpg", 'tmp_name' => $tmp ), 0, $alt );
			if ( is_wp_error( $id ) ) {
				WP_CLI::warning( $id->get_error_message() );
				return 0;
			}
			update_post_meta( $id, '_wp_attachment_image_alt', $alt );
			update_post_meta( $id, '_zkt_placeholder', '1' );
			return $images[ $key ] = $id;
		};

		foreach ( $data['projects'] as $order => $p ) {
			$gallery = array_filter( array_map( $import, $p['photos'] ) );
			$id      = wp_insert_post(
				array(
					'post_type'    => 'zkt_project',
					'post_status'  => 'publish',
					'post_title'   => $p['title'],
					'post_excerpt' => $p['excerpt'],
					'post_content' => $text( $p['content'] ),
					'menu_order'   => $order,
				),
				true
			);
			if ( is_wp_error( $id ) ) {
				WP_CLI::error( $id->get_error_message() );
			}
			set_post_thumbnail( $id, reset( $gallery ) );
			update_post_meta( $id, '_zkt_gallery', implode( ',', $gallery ) );
			update_post_meta( $id, '_zkt_location', $p['location'] );
			update_post_meta( $id, '_zkt_date', $p['date'] );
			update_post_meta( $id, '_zkt_featured', $p['featured'] ? '1' : '' );
			wp_set_object_terms( $id, $p['category'], 'zkt_category' );
			wp_set_object_terms( $id, $p['type'], 'zkt_project_type' );
			wp_set_object_terms( $id, $p['place'], 'zkt_place' );
			WP_CLI::log( "Project: {$p['title']}" );
		}

		foreach ( $data['services'] as $order => $s ) {
			$id = wp_insert_post(
				array(
					'post_type'    => 'zkt_service',
					'post_status'  => 'publish',
					'post_title'   => $s['title'],
					'post_excerpt' => $s['excerpt'],
					'post_content' => $text( $s['content'] ),
					'menu_order'   => $order,
				)
			);
			set_post_thumbnail( $id, $import( $s['photo'] ) );
			update_post_meta( $id, '_zkt_price', $s['price'] );
			update_post_meta( $id, '_zkt_duration', $s['duration'] );
			update_post_meta( $id, '_zkt_includes', implode( "\n", $s['includes'] ) );
		}

		foreach ( $data['posts'] as $post ) {
			$id = wp_insert_post(
				array(
					'post_type'    => 'post',
					'post_status'  => 'publish',
					'post_title'   => $post['title'],
					'post_excerpt' => $post['excerpt'],
					'post_content' => $text( $post['content'] ),
					'post_date'    => $post['date'] . ' 09:00:00',
				)
			);
			set_post_thumbnail( $id, $import( $post['photo'] ) );
			WP_CLI::log( "Journal: {$post['title']}" );
		}

		$page_ids = array();
		foreach ( $data['pages'] as $slug => $page ) {
			$existing = get_page_by_path( $slug );
			$args     = array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_title'   => $page['title'],
				'post_name'    => $slug,
				'post_content' => $text( $page['content'] ),
			);
			if ( $existing ) {
				$args['ID'] = $existing->ID;
			}
			$page_ids[ $slug ] = wp_insert_post( $args );
			if ( ! empty( $page['photo'] ) ) {
				set_post_thumbnail( $page_ids[ $slug ], $import( $page['photo'] ) );
			}
		}

		// The journal is the WordPress posts page, so it needs a page of its own.
		$journal = get_page_by_path( 'journal' );
		$page_ids['journal'] = wp_insert_post(
			array(
				'ID'          => $journal ? $journal->ID : 0,
				'post_type'   => 'page',
				'post_status' => 'publish',
				'post_title'  => 'Journal',
				'post_name'   => 'journal',
			)
		);

		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $page_ids['home'] );
		update_option( 'page_for_posts', $page_ids['journal'] );
		update_option( 'wp_page_for_privacy_policy', $page_ids['privacy-policy'] );

		$hero = array_map( $import, $data['hero'] );
		update_option( 'zkt_hero_images', array_values( array_filter( $hero ) ) );

		$menus = array();
		foreach ( $data['menus'] as $location => $items ) {
			$name = 'primary' === $location ? 'Main menu' : 'Footer menu';
			$menu = wp_get_nav_menu_object( $name );
			$mid  = $menu ? $menu->term_id : wp_create_nav_menu( $name );
			foreach ( wp_get_nav_menu_items( $mid ) ?: array() as $old ) {
				wp_delete_post( $old->ID, true );
			}
			foreach ( $items as $pos => $item ) {
				$args = array( 'menu-item-status' => 'publish', 'menu-item-position' => $pos + 1 );
				if ( 'portfolio' === $item ) {
					$args += array( 'menu-item-type' => 'post_type_archive', 'menu-item-object' => 'zkt_project', 'menu-item-title' => 'Portfolio' );
				} else {
					$args += array( 'menu-item-type' => 'post_type', 'menu-item-object' => 'page', 'menu-item-object-id' => $page_ids[ $item ] );
				}
				wp_update_nav_menu_item( $mid, 0, $args );
			}
			$menus[ $location ] = $mid;
		}
		// Assign the menus to the active theme's locations of the same name.
		$locations = array_intersect_key( $menus, get_registered_nav_menus() );
		if ( $locations ) {
			set_theme_mod( 'nav_menu_locations', array_merge( (array) get_theme_mod( 'nav_menu_locations', array() ), $locations ) );
		}

		$settings = (array) get_option( 'zkt_settings', array() );
		update_option( 'zkt_settings', array_merge( zkt_info(), $settings, array( 'notify' => get_option( 'admin_email' ) ) ) );
		update_option( 'zeronkit_portfolio_seeded', time() );
		flush_rewrite_rules();
		WP_CLI::success( count( $images ) . ' pictures, ' . count( $data['projects'] ) . ' projects, ' . count( $data['services'] ) . ' services, ' . count( $data['posts'] ) . ' journal posts, ' . count( $page_ids ) . ' pages.' );
	}
);
