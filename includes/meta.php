<?php
/**
 * Native custom fields for projects, services and kind words, with a simple Details box.
 *
 * @package zeronkit-portfolio
 */

defined( 'ABSPATH' ) || exit;

/** Field definitions per post type. The meta keys are shared with the ZeronKit themes. */
function zeronkit_portfolio_meta_fields() {
	return array(
		'zkt_project'     => array(
			'_zkt_location' => array(
				'label' => __( 'Location', 'zeronkit-portfolio' ),
				'type'  => 'text',
			),
			'_zkt_date'     => array(
				'label' => __( 'Photography date', 'zeronkit-portfolio' ),
				'type'  => 'date',
			),
			'_zkt_client'   => array(
				'label' => __( 'Client (only with permission)', 'zeronkit-portfolio' ),
				'type'  => 'text',
			),
			'_zkt_gallery'  => array(
				'label' => __( 'Gallery images', 'zeronkit-portfolio' ),
				'type'  => 'gallery',
			),
			'_zkt_featured' => array(
				'label' => __( 'Show on the homepage', 'zeronkit-portfolio' ),
				'type'  => 'checkbox',
			),
		),
		'zkt_service'     => array(
			'_zkt_price'    => array(
				'label' => __( 'Price (e.g. From EUR 450)', 'zeronkit-portfolio' ),
				'type'  => 'text',
			),
			'_zkt_duration' => array(
				'label' => __( 'Duration (e.g. 2 hours)', 'zeronkit-portfolio' ),
				'type'  => 'text',
			),
			'_zkt_includes' => array(
				'label' => __( 'What is included (one item per line)', 'zeronkit-portfolio' ),
				'type'  => 'textarea',
			),
		),
		'zkt_testimonial' => array(
			'_zkt_source' => array(
				'label' => __( 'Who said it (e.g. a client or a publication name)', 'zeronkit-portfolio' ),
				'type'  => 'text',
			),
			'_zkt_link'   => array(
				'label' => __( 'Link to the source (optional)', 'zeronkit-portfolio' ),
				'type'  => 'url',
			),
		),
	);
}

/**
 * Cleans one field value for its type.
 *
 * @param string $type  Field type.
 * @param mixed  $value Raw value.
 * @return string
 */
function zeronkit_portfolio_clean_meta( $type, $value ) {
	$value = is_scalar( $value ) ? (string) $value : '';
	return match ( $type ) {
		'textarea' => sanitize_textarea_field( $value ),
		'url'      => esc_url_raw( $value ),
		'gallery'  => implode( ',', zkt_parse_ids( $value ) ),
		'checkbox' => '1' === $value ? '1' : '',
		default    => sanitize_text_field( $value ),
	};
}

add_action(
	'init',
	static function () {
		foreach ( zeronkit_portfolio_meta_fields() as $post_type => $fields ) {
			foreach ( $fields as $key => $field ) {
				register_post_meta(
					$post_type,
					$key,
					array(
						'type'              => 'string',
						'single'            => true,
						'show_in_rest'      => true,
						'auth_callback'     => static fn( $allowed, $meta_key, $post_id ) => current_user_can( 'edit_post', $post_id ),
						'sanitize_callback' => static fn( $value ) => zeronkit_portfolio_clean_meta( $field['type'], $value ),
					)
				);
			}
		}
	}
);

add_action(
	'add_meta_boxes',
	static function () {
		foreach ( array_keys( zeronkit_portfolio_meta_fields() ) as $post_type ) {
			add_meta_box( 'zeronkit_portfolio_details', __( 'Details', 'zeronkit-portfolio' ), 'zeronkit_portfolio_render_meta_box', $post_type, 'normal', 'high' );
		}
	}
);

/**
 * Prints the Details box.
 *
 * @param WP_Post $post Post being edited.
 */
function zeronkit_portfolio_render_meta_box( WP_Post $post ) {
	wp_nonce_field( 'zeronkit_portfolio_meta_save', 'zeronkit_portfolio_meta_nonce' );
	echo '<table class="form-table" role="presentation">';
	foreach ( zeronkit_portfolio_meta_fields()[ $post->post_type ] as $key => $field ) {
		$value = (string) get_post_meta( $post->ID, $key, true );
		$id    = 'zeronkit-portfolio-' . ltrim( $key, '_' );
		echo '<tr><th scope="row"><label for="' . esc_attr( $id ) . '">' . esc_html( $field['label'] ) . '</label></th><td>';
		switch ( $field['type'] ) {
			case 'textarea':
				echo '<textarea class="large-text" rows="5" id="' . esc_attr( $id ) . '" name="' . esc_attr( $key ) . '">' . esc_textarea( $value ) . '</textarea>';
				break;
			case 'checkbox':
				echo '<input type="checkbox" id="' . esc_attr( $id ) . '" name="' . esc_attr( $key ) . '" value="1"' . checked( $value, '1', false ) . '>';
				break;
			case 'gallery':
				echo '<input type="hidden" id="' . esc_attr( $id ) . '" name="' . esc_attr( $key ) . '" value="' . esc_attr( $value ) . '">';
				echo '<div class="zeronkit-portfolio-preview">';
				foreach ( zkt_parse_ids( $value ) as $img ) {
					echo wp_get_attachment_image( $img, 'thumbnail' );
				}
				echo '</div><p><button type="button" class="button zeronkit-portfolio-pick" data-target="' . esc_attr( $id ) . '">' . esc_html__( 'Choose images', 'zeronkit-portfolio' ) . '</button></p>';
				break;
			default:
				echo '<input class="regular-text" type="' . esc_attr( $field['type'] ) . '" id="' . esc_attr( $id ) . '" name="' . esc_attr( $key ) . '" value="' . esc_attr( $value ) . '">';
		}
		echo '</td></tr>';
	}
	echo '</table>';
}

add_action(
	'save_post',
	static function ( $post_id, $post ) {
		if ( ! isset( $_POST['zeronkit_portfolio_meta_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['zeronkit_portfolio_meta_nonce'] ), 'zeronkit_portfolio_meta_save' ) ) {
			return;
		}
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		foreach ( zeronkit_portfolio_meta_fields()[ $post->post_type ] ?? array() as $key => $field ) {
			// Cleaned per field type by zeronkit_portfolio_clean_meta().
			$raw = isset( $_POST[ $key ] ) ? wp_unslash( $_POST[ $key ] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			update_post_meta( $post_id, $key, zeronkit_portfolio_clean_meta( $field['type'], $raw ) );
		}
	},
	10,
	2
);

add_action(
	'admin_enqueue_scripts',
	static function ( $hook ) {
		if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) || 'zkt_project' !== get_post_type() ) {
			return;
		}
		wp_enqueue_media();
		wp_enqueue_script( 'zeronkit-portfolio-admin', ZERONKIT_PORTFOLIO_URL . 'assets/js/admin.js', array( 'jquery' ), ZERONKIT_PORTFOLIO_VERSION, true );
		wp_add_inline_script( 'zeronkit-portfolio-admin', 'var zeronkitPortfolioAdminText = ' . wp_json_encode( array( 'title' => __( 'Project gallery', 'zeronkit-portfolio' ) ) ) . ';', 'before' );
		wp_add_inline_style( 'wp-admin', '.zeronkit-portfolio-preview{display:flex;flex-wrap:wrap;gap:6px}.zeronkit-portfolio-preview img{width:72px;height:72px;object-fit:cover}' );
	}
);

/**
 * Turns "12, 7,x" into [12, 7].
 *
 * @param mixed $value Comma separated IDs.
 * @return int[]
 */
function zkt_parse_ids( $value ) { // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound -- public API name the ZeronKit themes call.
	return array_values( array_filter( array_map( 'absint', explode( ',', (string) $value ) ) ) );
}
