<?php
/**
 * Template tags. The ZeronKit themes call these by name (through function_exists()
 * checks), so the names and return values must stay as they are.
 *
 * @package zeronkit-portfolio
 */

defined( 'ABSPATH' ) || exit;

// The zkt_ function names below are the public API the ZeronKit themes call, shared with the
// older ZeronKit Themes Core plugin, so they cannot take the longer plugin prefix.
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound

/**
 * Projects list.
 *
 * @param array $args featured (bool), number (int), category (slug), exclude (int[]).
 * @return WP_Post[]
 */
function zkt_projects( array $args = array() ) {
	$args    = wp_parse_args( $args, array( 'featured' => false, 'number' => -1, 'category' => '', 'exclude' => array() ) ); // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude -- a key of this function's own arguments, filtered in PHP below.
	$exclude = array_map( 'absint', (array) $args['exclude'] );
	$number  = (int) $args['number'];
	$query   = array(
		'post_type'      => 'zkt_project',
		'posts_per_page' => $number > 0 ? $number + count( $exclude ) : -1,
		'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
		'no_found_rows'  => true,
	);
	if ( $args['featured'] ) {
		$query['meta_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- one indexed key on a small post type.
			array(
				'key'   => '_zkt_featured',
				'value' => '1',
			),
		);
	}
	if ( $args['category'] ) {
		$query['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- filtering by category is the point.
			array(
				'taxonomy' => 'zkt_category',
				'field'    => 'slug',
				'terms'    => $args['category'],
			),
		);
	}
	return zeronkit_portfolio_without( get_posts( $query ), $exclude, $number );
}

/**
 * Removes some posts from a list and keeps at most $number. Used instead of post__not_in.
 *
 * @param WP_Post[] $posts   Posts.
 * @param int[]     $exclude IDs to remove.
 * @param int       $number  Maximum, -1 for all.
 * @return WP_Post[]
 */
function zeronkit_portfolio_without( array $posts, array $exclude, $number ) {
	$posts = array_values( array_filter( $posts, static fn( $p ) => ! in_array( (int) $p->ID, $exclude, true ) ) );
	return $number > 0 ? array_slice( $posts, 0, $number ) : $posts;
}

/** Project details: location, date, date_iso, client, categories (WP_Term[]), types (WP_Term[]), count (images). */
function zkt_project_meta( $post = null ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return array( 'location' => '', 'date' => '', 'date_iso' => '', 'client' => '', 'categories' => array(), 'types' => array(), 'count' => 0 );
	}
	$raw  = (string) get_post_meta( $post->ID, '_zkt_date', true );
	$cats = get_the_terms( $post, 'zkt_category' );
	$type = get_the_terms( $post, 'zkt_project_type' );
	return array(
		'location'   => (string) get_post_meta( $post->ID, '_zkt_location', true ),
		'date'       => $raw ? wp_date( 'F Y', strtotime( $raw ) ) : '',
		'date_iso'   => $raw,
		'client'     => (string) get_post_meta( $post->ID, '_zkt_client', true ),
		'categories' => is_array( $cats ) ? $cats : array(),
		'types'      => is_array( $type ) ? $type : array(),
		'count'      => count( zkt_gallery_ids( $post ) ),
	);
}

/** First category name, handy for captions. */
function zkt_primary_category( $post = null ) {
	$cats = zkt_project_meta( $post )['categories'];
	return $cats ? $cats[0]->name : '';
}

/** Gallery attachment IDs, falling back to the featured image. */
function zkt_gallery_ids( $post = null ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return array();
	}
	$ids  = zkt_parse_ids( get_post_meta( $post->ID, '_zkt_gallery', true ) );
	if ( ! $ids && has_post_thumbnail( $post ) ) {
		$ids = array( (int) get_post_thumbnail_id( $post ) );
	}
	return $ids;
}

/** Cover image ID: featured image or first gallery image. */
function zkt_cover_id( $post = null ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return 0;
	}
	return (int) ( get_post_thumbnail_id( $post ) ?: ( zkt_gallery_ids( $post )[0] ?? 0 ) );
}

/**
 * Responsive <img>. Lazy by default, pass array( 'loading' => 'eager', 'fetchpriority' => 'high' ) for the hero.
 * $attr['sizes'] should describe the rendered width, e.g. '(min-width: 900px) 50vw, 100vw'.
 */
function zkt_image( $id, $size = 'zkt-lg', array $attr = array() ) {
	if ( ! $id ) {
		return '';
	}
	$attr = wp_parse_args( $attr, array( 'loading' => 'lazy', 'decoding' => 'async' ) );
	if ( 'eager' === $attr['loading'] ) {
		unset( $attr['decoding'] );
	}
	return wp_get_attachment_image( $id, $size, false, $attr );
}

/** Same as zkt_image(), wrapped in a link that opens the shared lightbox. */
function zkt_lightbox_image( $id, $size = 'zkt-lg', array $attr = array(), $group = 'gallery' ) {
	$full    = wp_get_attachment_image_url( $id, 'zkt-xl' ) ?: wp_get_attachment_image_url( $id, 'full' );
	$caption = wp_get_attachment_caption( $id ) ?: get_post_meta( $id, '_wp_attachment_image_alt', true );
	return sprintf(
		'<a class="zkt-lightbox-link" href="%s" data-zkt-lightbox="%s" data-caption="%s">%s<span class="screen-reader-text">%s</span></a>',
		esc_url( $full ),
		esc_attr( $group ),
		esc_attr( $caption ),
		zkt_image( $id, $size, $attr ),
		esc_html__( 'Open image in full screen', 'zeronkit-portfolio' )
	);
}

/** Projects that share a category with $post. */
function zkt_related_projects( $post = null, $number = 3 ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return array();
	}
	$cats = wp_list_pluck( zkt_project_meta( $post )['categories'], 'term_id' );
	$args = array(
		'post_type'      => 'zkt_project',
		'posts_per_page' => $number + 1,
		'orderby'        => 'rand',
		'no_found_rows'  => true,
	);
	if ( $cats ) {
		$args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- related means same category.
			array(
				'taxonomy' => 'zkt_category',
				'terms'    => $cats,
			),
		);
	}
	$related = zeronkit_portfolio_without( get_posts( $args ), array( $post->ID ), $number );
	if ( count( $related ) < $number ) {
		$skip    = array_merge( array( $post->ID ), array_map( 'intval', wp_list_pluck( $related, 'ID' ) ) );
		$more    = get_posts(
			array(
				'post_type'      => 'zkt_project',
				'posts_per_page' => $number - count( $related ) + count( $skip ),
				'no_found_rows'  => true,
			)
		);
		$related = array_merge( $related, zeronkit_portfolio_without( $more, $skip, $number - count( $related ) ) );
	}
	return $related;
}

/** Homepage hero image IDs (set by the seed, editable later as the zkt_hero_images option). Falls back to featured project covers. */
function zkt_hero_ids() {
	$ids = array_filter( array_map( 'absint', (array) get_option( 'zkt_hero_images', array() ) ), 'wp_attachment_is_image' );
	if ( ! $ids ) {
		$ids = array_filter( array_map( 'zkt_cover_id', zkt_projects( array( 'featured' => true, 'number' => 4 ) ) ) );
	}
	return array_values( $ids );
}

/** Categories that have projects. @return WP_Term[] */
function zkt_categories() {
	$terms = get_terms( array( 'taxonomy' => 'zkt_category', 'hide_empty' => true ) );
	return is_wp_error( $terms ) ? array() : $terms;
}

/** Value for a portfolio item's data-zkt-cats attribute (categories and places, both filterable). */
function zkt_filter_attr( $post = null ) {
	$post   = get_post( $post );
	$places = get_the_terms( $post, 'zkt_place' );
	$slugs  = wp_list_pluck( zkt_project_meta( $post )['categories'], 'slug' );
	foreach ( is_array( $places ) ? $places : array() as $place ) {
		$slugs[] = 'place-' . $place->slug;
	}
	return esc_attr( implode( ' ', $slugs ) );
}

/** Places that have projects. @return WP_Term[] */
function zkt_places() {
	$terms = get_terms( array( 'taxonomy' => 'zkt_place', 'hide_empty' => true ) );
	return is_wp_error( $terms ) ? array() : $terms;
}

/**
 * "Book a session" link for the header. Always printed, shown only when the setting is on
 * (body class zkt-bookbtn). Themes style .zkt-book.
 *
 * @param string $class Extra class.
 */
function zkt_booking_button( $class = '' ) {
	printf(
		'<a class="zkt-book %1$s" href="%2$s">%3$s</a>',
		esc_attr( $class ),
		esc_url( zkt_page_url( 'contact' ) . '#contact-form' ),
		esc_html( zkt_info( 'booking_label' ) ?: __( 'Book a session', 'zeronkit-portfolio' ) )
	);
}

/** "Now booking" banner, shown only with body class zkt-banner and when the text is set. Themes style .zkt-banner. */
function zkt_booking_banner( $class = '' ) {
	$text = zkt_info( 'booking_banner' );
	if ( '' === trim( $text ) ) {
		return;
	}
	printf(
		'<p class="zkt-banner %1$s"><span>%2$s</span> <a href="%3$s">%4$s</a></p>',
		esc_attr( $class ),
		esc_html( $text ),
		esc_url( zkt_page_url( 'contact' ) . '#contact-form' ),
		esc_html( zkt_info( 'booking_label' ) ?: __( 'Book a session', 'zeronkit-portfolio' ) )
	);
}

/** Social links as a list, shown only with body class zkt-social. Prints nothing when no link is set. */
function zkt_social_block( $class = '' ) {
	$links = array_filter( array( 'Instagram' => zkt_info( 'instagram' ), 'Facebook' => zkt_info( 'facebook' ), 'Pinterest' => zkt_info( 'pinterest' ) ) );
	if ( ! $links ) {
		return;
	}
	echo '<ul class="zkt-social ' . esc_attr( $class ) . '" aria-label="' . esc_attr__( 'Social media', 'zeronkit-portfolio' ) . '">';
	foreach ( $links as $label => $url ) {
		printf( '<li><a href="%s" target="_blank" rel="noopener">%s</a></li>', esc_url( $url ), esc_html( $label ) );
	}
	echo '</ul>';
}

/**
 * Real client quotes and press mentions, newest order first. Empty until the owner adds some, and themes
 * must print nothing (not even a heading) when this returns an empty array.
 *
 * @return array[] Each: quote, source, link.
 */
function zkt_testimonials( $number = 6 ) {
	return array_map(
		static fn( $p ) => array(
			'quote'  => wp_strip_all_tags( $p->post_content ),
			'source' => (string) get_post_meta( $p->ID, '_zkt_source', true ) ?: $p->post_title,
			'link'   => (string) get_post_meta( $p->ID, '_zkt_link', true ),
		),
		get_posts( array( 'post_type' => 'zkt_testimonial', 'posts_per_page' => $number, 'orderby' => array( 'menu_order' => 'ASC', 'date' => 'DESC' ), 'no_found_rows' => true ) )
	);
}

/** Latest journal posts for the homepage. @return WP_Post[] */
function zkt_latest_posts( $number = 3 ) {
	return get_posts( array( 'post_type' => 'post', 'posts_per_page' => $number, 'no_found_rows' => true, 'ignore_sticky_posts' => true ) );
}

/** URL of the journal (the WordPress posts page). */
function zkt_journal_url() {
	$id = (int) get_option( 'page_for_posts' );
	return $id ? get_permalink( $id ) : home_url( '/journal/' );
}

/**
 * Filter buttons for a portfolio grid. Pair with a container that has data-zkt-portfolio
 * and items that carry data-zkt-cats="<?php echo zkt_filter_attr(); ?>".
 *
 * @param array $args layouts (bool, adds masonry/grid toggle), all_label (string).
 */
function zkt_portfolio_filters( array $args = array() ) {
	$args    = wp_parse_args( $args, array( 'layouts' => true, 'all_label' => __( 'All work', 'zeronkit-portfolio' ) ) );
	$current = is_tax( 'zkt_category' ) ? get_queried_object()->slug : 'all';
	echo '<div class="zkt-filters" data-zkt-filters>';
	echo '<div class="zkt-filters__cats" role="group" aria-label="' . esc_attr__( 'Filter projects by category', 'zeronkit-portfolio' ) . '">';
	printf( '<button type="button" class="zkt-filter" data-zkt-filter="all" aria-pressed="%s">%s</button>', 'all' === $current ? 'true' : 'false', esc_html( $args['all_label'] ) );
	foreach ( zkt_categories() as $term ) {
		printf(
			'<button type="button" class="zkt-filter" data-zkt-filter="%1$s" aria-pressed="%2$s">%3$s <span class="zkt-filter__count">%4$d</span></button>',
			esc_attr( $term->slug ),
			$current === $term->slug ? 'true' : 'false',
			esc_html( $term->name ),
			(int) $term->count
		);
	}
	echo '</div>';
	$places = zkt_places();
	if ( $places ) {
		// Place filter: shown when the "filter by place" setting is on (body class zkt-places).
		echo '<div class="zkt-filters__places" role="group" aria-label="' . esc_attr__( 'Filter projects by place', 'zeronkit-portfolio' ) . '">';
		printf( '<button type="button" class="zkt-filter" data-zkt-filter="all" aria-pressed="true">%s</button>', esc_html__( 'All places', 'zeronkit-portfolio' ) );
		foreach ( $places as $place ) {
			printf( '<button type="button" class="zkt-filter" data-zkt-filter="place-%1$s" aria-pressed="false">%2$s</button>', esc_attr( $place->slug ), esc_html( $place->name ) );
		}
		echo '</div>';
	}
	if ( $args['layouts'] ) {
		echo '<div class="zkt-filters__layouts" role="group" aria-label="' . esc_attr__( 'Layout', 'zeronkit-portfolio' ) . '">';
		echo '<button type="button" class="zkt-layout" data-zkt-layout="masonry" aria-pressed="true">' . esc_html__( 'Masonry', 'zeronkit-portfolio' ) . '</button>';
		echo '<button type="button" class="zkt-layout" data-zkt-layout="grid" aria-pressed="false">' . esc_html__( 'Grid', 'zeronkit-portfolio' ) . '</button>';
		echo '</div>';
	}
	echo '</div>';
}

/** Services in editor order. @return WP_Post[] */
function zkt_services() {
	return get_posts(
		array(
			'post_type'      => 'zkt_service',
			'posts_per_page' => -1,
			'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
			'no_found_rows'  => true,
		)
	);
}

/** Service details: price, duration, includes (string[]). */
function zkt_service_meta( $post = null ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return '';
	}
	if ( ! $post ) {
		return array( 'price' => '', 'duration' => '', 'includes' => array() );
	}
	return array(
		'price'    => (string) get_post_meta( $post->ID, '_zkt_price', true ),
		'duration' => (string) get_post_meta( $post->ID, '_zkt_duration', true ),
		'includes' => array_values( array_filter( array_map( 'trim', explode( "\n", (string) get_post_meta( $post->ID, '_zkt_includes', true ) ) ) ) ),
	);
}

/** Accessible label for a link back to the homepage, built from the site title. */
function zkt_home_label() {
	/* translators: %s site title */
	return sprintf( __( '%s, home', 'zeronkit-portfolio' ), zkt_site_name() );
}

/**
 * Short second line for the designs whose wordmark has one.
 *
 * It is the tagline, but only when it is short enough to sit under a wordmark.
 * A long tagline belongs in the hero, not in the header, so this returns an
 * empty string and the themes leave the line out.
 */
function zkt_wordmark_sub() {
	$tagline = zkt_tagline();
	return mb_strlen( $tagline ) <= 32 ? $tagline : '';
}

/** URL of a page by slug, or home when missing. */
function zkt_page_url( $slug ) {
	$page = get_page_by_path( $slug );
	return $page ? get_permalink( $page ) : home_url( '/' );
}

/** Social links as [label => url], empty ones removed. */
function zkt_social_links() {
	return array_filter(
		array(
			'Instagram' => zkt_info( 'instagram' ),
			'Pinterest' => zkt_info( 'pinterest' ),
		)
	);
}

/**
 * True when an image is one of the sample pictures that ship with the theme,
 * not the owner's own work. The sample pictures are ZeronKit's own artwork, so
 * they need no attribution, only replacing.
 *
 * @param int $attachment_id Attachment ID.
 */
function zkt_is_placeholder( $attachment_id ) {
	return (bool) get_post_meta( $attachment_id, '_zkt_placeholder', true );
}

/** Small footer notice while the sample pictures are still in place. Empty once they are all replaced. */
function zkt_placeholder_notice() {
	$count = (int) ( new WP_Query(
		array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'meta_key'       => '_zkt_placeholder', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- one row is enough.
			'posts_per_page' => 1,
			'fields'         => 'ids',
		)
	) )->found_posts;
	return $count ? __( 'Sample pictures shown for layout only, to be replaced with the studio\'s own photography.', 'zeronkit-portfolio' ) : '';
}
