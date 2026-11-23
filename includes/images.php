<?php
/**
 * Image sizes used by zkt_image() and the lightbox. The names are shared with the
 * ZeronKit themes and the older ZeronKit Themes Core plugin.
 *
 * @package zeronkit-portfolio
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'after_setup_theme',
	static function () {
		add_image_size( 'zkt-sm', 640, 0 );
		add_image_size( 'zkt-md', 1024, 0 );
		add_image_size( 'zkt-lg', 1600, 0 );
		add_image_size( 'zkt-xl', 2400, 0 );
	},
	5
);

// Lets the srcset reach the 2400px copy on large screens.
add_filter( 'max_srcset_image_width', static fn( $max ) => max( (int) $max, 2400 ) );
