<?php
/**
 * Front-end behaviour: lightbox, portfolio filters and layout toggle.
 * Themes restyle everything through the CSS custom properties in zeronkit-portfolio.css.
 *
 * @package zeronkit-portfolio
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'wp_enqueue_scripts',
	static function () {
		wp_enqueue_style( 'zeronkit-portfolio', ZERONKIT_PORTFOLIO_URL . 'assets/css/zeronkit-portfolio.css', array(), ZERONKIT_PORTFOLIO_VERSION );
		wp_enqueue_script(
			'zeronkit-portfolio',
			ZERONKIT_PORTFOLIO_URL . 'assets/js/zeronkit-portfolio.js',
			array(),
			ZERONKIT_PORTFOLIO_VERSION,
			array(
				'strategy'  => 'defer',
				'in_footer' => true,
			)
		);
		$strings = array(
			'viewer' => __( 'Image viewer', 'zeronkit-portfolio' ),
			'prev'   => __( 'Previous image', 'zeronkit-portfolio' ),
			'next'   => __( 'Next image', 'zeronkit-portfolio' ),
			'close'  => __( 'Close', 'zeronkit-portfolio' ),
		);
		wp_add_inline_script( 'zeronkit-portfolio', 'var zeronkitPortfolioText = ' . wp_json_encode( $strings ) . ';', 'before' );
	},
	5
);
