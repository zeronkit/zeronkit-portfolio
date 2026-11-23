<?php
/**
 * Site details (Settings > ZeronKit Portfolio). The themes read them through zkt_info().
 *
 * The values live in the zkt_settings option, shared with the older ZeronKit Themes
 * Core plugin, so a site can switch between the two without typing anything again.
 * Keys this plugin does not show (for example the old footer credit switch) are kept.
 *
 * @package zeronkit-portfolio
 */

defined( 'ABSPATH' ) || exit;

/** Settings sections: title and help text. */
function zeronkit_portfolio_settings_sections() {
	return array(
		'brand'   => array( __( 'Brand', 'zeronkit-portfolio' ), __( 'The wordmark is the site title in Settings > General. These fields fill in the rest.', 'zeronkit-portfolio' ) ),
		'contact' => array( __( 'Contact', 'zeronkit-portfolio' ), __( 'Shown in footers, on the contact page and in the structured data.', 'zeronkit-portfolio' ) ),
		'social'  => array( __( 'Social links', 'zeronkit-portfolio' ), __( 'A link left empty is simply not printed.', 'zeronkit-portfolio' ) ),
		'booking' => array( __( 'Booking and display', 'zeronkit-portfolio' ), __( 'Extras the ZeronKit themes can show. Each one stays hidden until it is switched on here.', 'zeronkit-portfolio' ) ),
	);
}

/** Fields: label, section, input kind. */
function zeronkit_portfolio_settings_fields() {
	return array(
		'monogram'       => array( __( 'Monogram letter', 'zeronkit-portfolio' ), 'brand', 'letter' ),
		'tagline'        => array( __( 'Hero tagline', 'zeronkit-portfolio' ), 'brand', 'text' ),
		'intro'          => array( __( 'Short introduction (homepage)', 'zeronkit-portfolio' ), 'brand', 'textarea' ),
		'email'          => array( __( 'Public email', 'zeronkit-portfolio' ), 'contact', 'email' ),
		'phone'          => array( __( 'Phone', 'zeronkit-portfolio' ), 'contact', 'text' ),
		'location'       => array( __( 'Based in', 'zeronkit-portfolio' ), 'contact', 'text' ),
		'notify'         => array( __( 'Send inquiries to (private email)', 'zeronkit-portfolio' ), 'contact', 'email' ),
		'instagram'      => array( __( 'Instagram URL', 'zeronkit-portfolio' ), 'social', 'url' ),
		'pinterest'      => array( __( 'Pinterest URL', 'zeronkit-portfolio' ), 'social', 'url' ),
		'facebook'       => array( __( 'Facebook URL', 'zeronkit-portfolio' ), 'social', 'url' ),
		'booking_label'  => array( __( 'Booking button text', 'zeronkit-portfolio' ), 'booking', 'text' ),
		'booking_banner' => array( __( 'Booking banner text', 'zeronkit-portfolio' ), 'booking', 'text' ),
		'show_book'      => array( __( 'Show the booking button in the header', 'zeronkit-portfolio' ), 'booking', 'checkbox' ),
		'show_banner'    => array( __( 'Show the booking banner (needs banner text)', 'zeronkit-portfolio' ), 'booking', 'checkbox' ),
		'show_social'    => array( __( 'Show the social links block', 'zeronkit-portfolio' ), 'booking', 'checkbox' ),
		'show_places'    => array( __( 'Let visitors filter projects by place', 'zeronkit-portfolio' ), 'booking', 'checkbox' ),
	);
}

/** Defaults, read from the WordPress settings wherever one exists. */
function zeronkit_portfolio_settings_defaults() {
	return array(
		'monogram'       => zeronkit_portfolio_first_letter( wp_strip_all_tags( (string) get_bloginfo( 'name' ) ) ),
		'tagline'        => wp_strip_all_tags( (string) get_bloginfo( 'description' ) ),
		'intro'          => '',
		'email'          => (string) get_option( 'admin_email' ),
		'phone'          => '',
		'whatsapp'       => '',
		'location'       => '',
		'notify'         => '',
		'instagram'      => '',
		'pinterest'      => '',
		'facebook'       => '',
		'booking_label'  => __( 'Book a session', 'zeronkit-portfolio' ),
		'booking_banner' => '',
		'show_book'      => '0',
		'show_banner'    => '0',
		'show_social'    => '0',
		'show_places'    => '0',
	);
}

/**
 * First letter of a name, upper case, or Z when there is no letter.
 *
 * @param string $text Any text.
 * @return string
 */
function zeronkit_portfolio_first_letter( $text ) {
	return preg_match( '/\p{L}/u', (string) $text, $m ) ? mb_strtoupper( $m[0] ) : 'Z';
}

/**
 * One site detail, or all of them when $key is empty. Empty values fall back to the default
 * for the fields that must never be blank.
 *
 * @param string $key Detail name.
 * @return string|array
 */
function zkt_info( $key = '' ) { // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound -- public API name the ZeronKit themes call.
	$defaults = zeronkit_portfolio_settings_defaults();
	$saved    = (array) get_option( 'zkt_settings', array() );
	$all      = array();
	foreach ( $defaults as $k => $default ) {
		$value     = isset( $saved[ $k ] ) && is_scalar( $saved[ $k ] ) ? (string) $saved[ $k ] : '';
		$all[ $k ] = ( '' === $value && ( in_array( $k, array( 'monogram', 'email', 'booking_label' ), true ) || ! isset( $saved[ $k ] ) ) ) ? $default : $value;
	}
	return $key ? ( $all[ $key ] ?? '' ) : $all;
}

/** The site name used as the wordmark. */
function zkt_site_name() { // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound -- public API name the ZeronKit themes call.
	$name = wp_strip_all_tags( (string) get_bloginfo( 'name' ) );
	return '' !== trim( $name ) ? $name : 'ZeronKit';
}

/** The single monogram letter. */
function zkt_monogram() { // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound -- public API name the ZeronKit themes call.
	$letter = trim( (string) zkt_info( 'monogram' ) );
	return '' !== $letter ? mb_substr( $letter, 0, 1 ) : zeronkit_portfolio_first_letter( zkt_site_name() );
}

/** The hero tagline, falling back to the WordPress tagline. */
function zkt_tagline() { // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound -- public API name the ZeronKit themes call.
	$tagline = trim( (string) zkt_info( 'tagline' ) );
	return '' !== $tagline ? $tagline : wp_strip_all_tags( (string) get_bloginfo( 'description' ) );
}

/** The homepage introduction, falling back to the tagline so a page is never empty. */
function zkt_intro() { // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound -- public API name the ZeronKit themes call.
	$intro = trim( (string) zkt_info( 'intro' ) );
	return '' !== $intro ? $intro : zkt_tagline();
}

add_action(
	'admin_menu',
	static function () {
		add_options_page( __( 'ZeronKit Portfolio', 'zeronkit-portfolio' ), __( 'ZeronKit Portfolio', 'zeronkit-portfolio' ), 'manage_options', 'zeronkit-portfolio', 'zeronkit_portfolio_render_settings_page' );
	}
);

add_action(
	'admin_init',
	static function () {
		register_setting(
			'zeronkit_portfolio',
			'zkt_settings',
			array(
				'type'              => 'array',
				'sanitize_callback' => 'zeronkit_portfolio_sanitize_settings',
				'default'           => array(),
			)
		);
		foreach ( zeronkit_portfolio_settings_sections() as $sid => $section ) {
			add_settings_section(
				'zeronkit_portfolio_' . $sid,
				$section[0],
				static function () use ( $section ) {
					echo '<p class="description">' . esc_html( $section[1] ) . '</p>';
				},
				'zeronkit-portfolio'
			);
		}
		foreach ( zeronkit_portfolio_settings_fields() as $key => $field ) {
			add_settings_field(
				'zeronkit-portfolio-' . $key,
				$field[0],
				'zeronkit_portfolio_render_field',
				'zeronkit-portfolio',
				'zeronkit_portfolio_' . $field[1],
				array(
					'key'       => $key,
					'kind'      => $field[2],
					'label_for' => 'zeronkit-portfolio-' . $key,
				)
			);
		}
	}
);

/**
 * Cleans the submitted settings. Keys this plugin does not edit are kept as they were.
 *
 * @param mixed $input Submitted values.
 * @return array
 */
function zeronkit_portfolio_sanitize_settings( $input ) {
	$input = is_array( $input ) ? $input : array();
	$out   = (array) get_option( 'zkt_settings', array() );
	foreach ( zeronkit_portfolio_settings_fields() as $k => $field ) {
		$v         = isset( $input[ $k ] ) && is_scalar( $input[ $k ] ) ? (string) $input[ $k ] : '';
		$out[ $k ] = match ( $field[2] ) {
			'email'    => sanitize_email( $v ),
			'url'      => esc_url_raw( $v ),
			'textarea' => sanitize_textarea_field( $v ),
			'checkbox' => '1' === $v ? '1' : '0',
			'letter'   => mb_substr( sanitize_text_field( $v ), 0, 1 ),
			default    => sanitize_text_field( $v ),
		};
	}
	return $out;
}

/**
 * Prints one settings input.
 *
 * @param array $args key, kind.
 */
function zeronkit_portfolio_render_field( array $args ) {
	$key   = $args['key'];
	$value = (string) zkt_info( $key );
	$name  = 'zkt_settings[' . $key . ']';
	$id    = 'zeronkit-portfolio-' . $key;
	switch ( $args['kind'] ) {
		case 'checkbox':
			printf( '<input type="checkbox" id="%1$s" name="%2$s" value="1"%3$s>', esc_attr( $id ), esc_attr( $name ), checked( $value, '1', false ) );
			break;
		case 'textarea':
			printf( '<textarea class="large-text" rows="4" id="%1$s" name="%2$s">%3$s</textarea>', esc_attr( $id ), esc_attr( $name ), esc_textarea( $value ) );
			break;
		case 'letter':
			printf( '<input class="small-text" maxlength="1" id="%1$s" name="%2$s" value="%3$s"> <span class="description">%4$s</span>', esc_attr( $id ), esc_attr( $name ), esc_attr( $value ), esc_html__( 'One letter. Defaults to the first letter of the site title.', 'zeronkit-portfolio' ) );
			break;
		default:
			$type = in_array( $args['kind'], array( 'email', 'url' ), true ) ? $args['kind'] : 'text';
			printf( '<input class="regular-text" type="%1$s" id="%2$s" name="%3$s" value="%4$s">', esc_attr( $type ), esc_attr( $id ), esc_attr( $name ), esc_attr( $value ) );
	}
}

/** The settings page. */
function zeronkit_portfolio_render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	echo '<div class="wrap"><h1>' . esc_html( get_admin_page_title() ) . '</h1>';
	echo '<form method="post" action="options.php">';
	settings_fields( 'zeronkit_portfolio' );
	do_settings_sections( 'zeronkit-portfolio' );
	submit_button();
	echo '</form></div>';
}

// Body classes that reveal the optional extras. The plugin stylesheet hides them otherwise.
add_filter(
	'body_class',
	static function ( $classes ) {
		$map = array(
			'show_book'   => 'zkt-bookbtn',
			'show_banner' => 'zkt-banner-on',
			'show_social' => 'zkt-social-on',
			'show_places' => 'zkt-places',
		);
		foreach ( $map as $key => $class ) {
			if ( '1' === zkt_info( $key ) ) {
				$classes[] = $class;
			}
		}
		return $classes;
	}
);

add_filter(
	'plugin_action_links_' . plugin_basename( ZERONKIT_PORTFOLIO_DIR . 'zeronkit-portfolio.php' ),
	static function ( $links ) {
		array_unshift( $links, '<a href="' . esc_url( admin_url( 'options-general.php?page=zeronkit-portfolio' ) ) . '">' . esc_html__( 'Settings', 'zeronkit-portfolio' ) . '</a>' );
		return $links;
	}
);
