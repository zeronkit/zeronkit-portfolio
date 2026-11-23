<?php
/**
 * Meta description, canonical, Open Graph and JSON-LD. Steps aside automatically
 * when a full SEO plugin is active (Yoast, Rank Math, All in One SEO, SEOPress, The SEO Framework).
 *
 * @package zeronkit-portfolio
 */

defined( 'ABSPATH' ) || exit;

/** True when a full SEO plugin prints these tags already. */
function zeronkit_portfolio_seo_plugin_active() {
	$active = defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'AIOSEO_VERSION' ) || defined( 'SEOPRESS_VERSION' ) || defined( 'THE_SEO_FRAMEWORK_VERSION' );
	/**
	 * Filters whether the plugin leaves the SEO tags to another plugin.
	 *
	 * @param bool $active True to print nothing.
	 */
	return (bool) apply_filters( 'zeronkit_portfolio_skip_seo', $active );
}

/** Description for the current view. */
function zeronkit_portfolio_meta_description() {
	if ( is_singular() ) {
		$post = get_queried_object();
		$text = has_excerpt( $post ) ? get_the_excerpt( $post ) : wp_strip_all_tags( strip_shortcodes( $post->post_content ) );
		if ( is_front_page() || '' === trim( $text ) ) {
			$text = zkt_info( 'intro' );
		}
	} elseif ( is_post_type_archive( 'zkt_project' ) ) {
		$text = __( 'Selected photography projects, by category.', 'zeronkit-portfolio' );
	} elseif ( is_tax() ) {
		$term = get_queried_object();
		/* translators: 1: category name, 2: site title */
		$text = $term->description ?: sprintf( __( '%1$s photography by %2$s.', 'zeronkit-portfolio' ), $term->name, zkt_site_name() );
	} else {
		$text = get_bloginfo( 'description' );
	}
	return wp_html_excerpt( trim( preg_replace( '/\s+/', ' ', $text ) ), 158, '...' );
}

/** Share image for the current view. */
function zeronkit_portfolio_meta_image() {
	$id = 0;
	if ( is_singular( 'zkt_project' ) ) {
		$id = zkt_cover_id();
	} elseif ( is_singular() && has_post_thumbnail() ) {
		$id = get_post_thumbnail_id();
	}
	if ( ! $id ) {
		$featured = zkt_projects( array( 'featured' => true, 'number' => 1 ) );
		$id       = $featured ? zkt_cover_id( $featured[0] ) : 0;
	}
	return $id ? wp_get_attachment_image_src( $id, 'zkt-lg' ) : false;
}

add_action(
	'wp_head',
	static function () {
		if ( zeronkit_portfolio_seo_plugin_active() || is_404() ) {
			return;
		}
		$desc  = zeronkit_portfolio_meta_description();
		$title = wp_get_document_title();
		$url   = is_singular() ? get_permalink() : ( is_post_type_archive() ? get_post_type_archive_link( get_query_var( 'post_type' ) ?: 'zkt_project' ) : ( is_tax() ? get_term_link( get_queried_object() ) : ( is_home() ? zkt_journal_url() : '' ) ) );
		$img   = zeronkit_portfolio_meta_image();

		echo "\n<meta name=\"description\" content=\"" . esc_attr( $desc ) . "\">\n";
		if ( ! is_singular() && $url && ! is_wp_error( $url ) ) {
			echo '<link rel="canonical" href="' . esc_url( $url ) . "\">\n"; // Core already prints it on single pages.
		}
		$og = array(
			'og:type'        => is_singular( 'zkt_project' ) ? 'article' : 'website',
			'og:site_name'   => get_bloginfo( 'name' ),
			'og:title'       => $title,
			'og:description' => $desc,
			'og:url'         => is_wp_error( $url ) ? '' : $url,
			'og:locale'      => str_replace( '-', '_', get_bloginfo( 'language' ) ),
		);
		if ( $img ) {
			$og['og:image']        = $img[0];
			$og['og:image:width']  = $img[1];
			$og['og:image:height'] = $img[2];
		}
		foreach ( array_filter( $og ) as $prop => $content ) {
			echo '<meta property="' . esc_attr( $prop ) . '" content="' . esc_attr( (string) $content ) . "\">\n";
		}
		echo '<meta name="twitter:card" content="summary_large_image">' . "\n";

		if ( is_front_page() ) {
			$schema = array(
				'@context'    => 'https://schema.org',
				'@type'       => 'ProfessionalService',
				'name'        => get_bloginfo( 'name' ),
				'description' => zkt_info( 'intro' ),
				'url'         => home_url( '/' ),
				'email'       => zkt_info( 'email' ),
				'areaServed'  => zkt_info( 'location' ),
				'sameAs'      => array_values( zkt_social_links() ),
			);
			if ( $img ) {
				$schema['image'] = $img[0];
			}
			echo '<script type="application/ld+json">' . wp_json_encode( array_filter( $schema ), JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP ) . "</script>\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON with < > & hex encoded.
		}
	},
	2
);
