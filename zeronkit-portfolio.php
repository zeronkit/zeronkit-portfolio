<?php
/**
 * Plugin Name:       ZeronKit Portfolio
 * Plugin URI:        https://zeronkit.com/themes/
 * Description:       Portfolio projects, services, kind words, a contact and booking form that saves every message as an inquiry, and site details for the ZeronKit themes.
 * Version:           1.0.0
 * Requires at least: 6.5
 * Requires PHP:      8.1
 * Author:            ZeronKit
 * Author URI:        https://zeronkit.com
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       zeronkit-portfolio
 *
 * @package zeronkit-portfolio
 */

defined( 'ABSPATH' ) || exit;

define( 'ZERONKIT_PORTFOLIO_VERSION', '1.0.0' );
define( 'ZERONKIT_PORTFOLIO_DIR', plugin_dir_path( __FILE__ ) );
define( 'ZERONKIT_PORTFOLIO_URL', plugin_dir_url( __FILE__ ) );

/**
 * True when the older ZeronKit Themes Core plugin is active. It declares the same
 * zkt_* functions, so only one of the two may load. It sorts after this plugin, so
 * the active plugin list is checked rather than function_exists().
 */
function zeronkit_portfolio_core_active() {
	$core    = 'zeronkit-themes-core/zeronkit-themes-core.php';
	$plugins = (array) get_option( 'active_plugins', array() );
	if ( is_multisite() ) {
		$plugins = array_merge( $plugins, array_keys( (array) get_site_option( 'active_sitewide_plugins', array() ) ) );
	}
	return in_array( $core, $plugins, true ) || function_exists( 'zkt_info' );
}

if ( zeronkit_portfolio_core_active() ) {
	add_action(
		'admin_notices',
		static function () {
			if ( ! current_user_can( 'activate_plugins' ) ) {
				return;
			}
			echo '<div class="notice notice-warning is-dismissible"><p>' . esc_html__( 'ZeronKit Portfolio is resting because ZeronKit Themes Core is active and already provides the same features. Deactivate ZeronKit Themes Core to use ZeronKit Portfolio instead. Your projects, services and settings are kept.', 'zeronkit-portfolio' ) . '</p></div>';
		}
	);
	return;
}

foreach ( array( 'post-types', 'meta', 'settings', 'images', 'template-tags', 'contact', 'seo', 'assets' ) as $zeronkit_portfolio_file ) {
	require_once ZERONKIT_PORTFOLIO_DIR . "includes/{$zeronkit_portfolio_file}.php";
}
unset( $zeronkit_portfolio_file );

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	require_once ZERONKIT_PORTFOLIO_DIR . 'includes/cli.php';
}

register_activation_hook(
	__FILE__,
	static function () {
		zeronkit_portfolio_register_types();
		flush_rewrite_rules();
	}
);
register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );
