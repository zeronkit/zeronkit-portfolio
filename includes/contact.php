<?php
/**
 * Booking and contact form. Built in, so no form plugin is needed.
 * Spam protection: nonce, honeypot field, minimum fill time and a per-visitor hourly limit.
 * Every valid message is saved as a private Inquiry before email is sent, so nothing is lost if mail fails.
 *
 * @package zeronkit-portfolio
 */

defined( 'ABSPATH' ) || exit;

const ZERONKIT_PORTFOLIO_FORM_MIN_SECONDS = 3;
const ZERONKIT_PORTFOLIO_FORM_HOURLY_MAX  = 5;

/**
 * Prints the contact form. Themes style .zkt-form, .zkt-field, .zkt-form__notice.
 *
 * @param array $args submit_label.
 */
function zkt_contact_form( array $args = array() ) { // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound -- public API name the ZeronKit themes call.
	$args   = wp_parse_args( $args, array( 'submit_label' => __( 'Send inquiry', 'zeronkit-portfolio' ) ) );
	$state  = zeronkit_portfolio_form_state();
	$old    = $state['old'];
	$errors = $state['errors'];

	echo '<form class="zkt-form" id="contact-form" method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';

	if ( 'sent' === $state['status'] ) {
		echo '<div class="zkt-form__notice zkt-form__notice--success" role="status" tabindex="-1">' . esc_html__( 'Thank you. Your message has arrived and we will reply within two working days.', 'zeronkit-portfolio' ) . '</div>';
	} elseif ( $errors ) {
		echo '<div class="zkt-form__notice zkt-form__notice--error" role="alert" tabindex="-1">' . esc_html__( 'Please check the highlighted fields.', 'zeronkit-portfolio' ) . '</div>';
	}

	wp_nonce_field( 'zeronkit_portfolio_contact', 'zeronkit_portfolio_nonce' );
	echo '<input type="hidden" name="action" value="zeronkit_portfolio_contact">';
	echo '<input type="hidden" name="zeronkit_portfolio_ts" value="' . esc_attr( (string) time() ) . '">';
	echo '<div class="zkt-hp" aria-hidden="true"><label for="zkt-website">' . esc_html__( 'Website', 'zeronkit-portfolio' ) . '</label><input type="text" id="zkt-website" name="website" tabindex="-1" autocomplete="off"></div>';

	zeronkit_portfolio_form_field( 'name', __( 'Your name', 'zeronkit-portfolio' ), 'text', $old, $errors, array( 'required' => true, 'autocomplete' => 'name', 'maxlength' => 120 ) );
	zeronkit_portfolio_form_field( 'email', __( 'Email address', 'zeronkit-portfolio' ), 'email', $old, $errors, array( 'required' => true, 'autocomplete' => 'email', 'maxlength' => 190 ) );

	$service_error = $errors['service'] ?? '';
	echo '<div class="zkt-field' . ( $service_error ? ' is-invalid' : '' ) . '"><label for="zkt-service">' . esc_html__( 'Type of session', 'zeronkit-portfolio' ) . '</label>';
	echo '<select id="zkt-service" name="service" required' . ( $service_error ? ' aria-invalid="true" aria-describedby="zkt-service-error"' : '' ) . '>';
	echo '<option value="">' . esc_html__( 'Choose a service', 'zeronkit-portfolio' ) . '</option>';
	foreach ( zeronkit_portfolio_service_choices() as $choice ) {
		printf( '<option value="%1$s"%2$s>%1$s</option>', esc_attr( $choice ), selected( $old['service'] ?? '', $choice, false ) );
	}
	echo '</select>';
	if ( $service_error ) {
		echo '<p class="zkt-field__error" id="zkt-service-error">' . esc_html( $service_error ) . '</p>';
	}
	echo '</div>';

	zeronkit_portfolio_form_field( 'date', __( 'Preferred date (optional)', 'zeronkit-portfolio' ), 'date', $old, $errors, array( 'min' => wp_date( 'Y-m-d' ) ) );
	zeronkit_portfolio_form_field( 'message', __( 'Tell me about your idea', 'zeronkit-portfolio' ), 'textarea', $old, $errors, array( 'required' => true, 'rows' => 6, 'maxlength' => 5000 ) );

	echo '<p class="zkt-form__privacy">' . sprintf(
		/* translators: %s privacy policy link */
		esc_html__( 'Your details are used only to reply to you. Read the %s.', 'zeronkit-portfolio' ),
		'<a href="' . esc_url( get_privacy_policy_url() ?: home_url( '/privacy-policy/' ) ) . '">' . esc_html__( 'privacy policy', 'zeronkit-portfolio' ) . '</a>'
	) . '</p>';
	echo '<button type="submit" class="zkt-form__submit">' . esc_html( $args['submit_label'] ) . '</button>';
	echo '</form>';
}

/**
 * Prints one form field with its label and error.
 *
 * @param string $name   Field name.
 * @param string $label  Label text.
 * @param string $type   Input type or textarea.
 * @param array  $old    Previous values.
 * @param array  $errors Errors by field.
 * @param array  $attrs  Extra attributes.
 */
function zeronkit_portfolio_form_field( $name, $label, $type, array $old, array $errors, array $attrs = array() ) {
	$id    = 'zkt-' . $name;
	$error = $errors[ $name ] ?? '';
	$html  = '';
	foreach ( $attrs as $k => $v ) {
		if ( 'rows' === $k && 'textarea' !== $type ) {
			continue;
		}
		$html .= true === $v ? ' ' . $k : ' ' . $k . '="' . esc_attr( (string) $v ) . '"';
	}
	if ( $error ) {
		$html .= ' aria-invalid="true" aria-describedby="' . esc_attr( $id ) . '-error"';
	}
	echo '<div class="zkt-field zkt-field--' . esc_attr( $name ) . ( $error ? ' is-invalid' : '' ) . '"><label for="' . esc_attr( $id ) . '">' . esc_html( $label ) . '</label>';
	if ( 'textarea' === $type ) {
		echo '<textarea id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '"' . $html . '>' . esc_textarea( $old[ $name ] ?? '' ) . '</textarea>'; // phpcs:ignore WordPress.Security.EscapeOutput
	} else {
		echo '<input type="' . esc_attr( $type ) . '" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( $old[ $name ] ?? '' ) . '"' . $html . '>'; // phpcs:ignore WordPress.Security.EscapeOutput
	}
	if ( $error ) {
		echo '<p class="zkt-field__error" id="' . esc_attr( $id ) . '-error">' . esc_html( $error ) . '</p>';
	}
	echo '</div>';
}

/** Service titles plus a general option. @return string[] */
function zeronkit_portfolio_service_choices() {
	return array_merge( wp_list_pluck( zkt_services(), 'post_title' ), array( __( 'Something else', 'zeronkit-portfolio' ) ) );
}

/** Reads the result of the last submission from the redirect URL. */
function zeronkit_portfolio_form_state() {
	$state = array( 'status' => '', 'errors' => array(), 'old' => array() );
	// phpcs:disable WordPress.Security.NonceVerification.Recommended
	if ( isset( $_GET['zkt_form'] ) && 'sent' === sanitize_key( $_GET['zkt_form'] ) ) {
		$state['status'] = 'sent';
	}
	if ( isset( $_GET['zkt_ft'] ) ) {
		$saved = get_transient( 'zeronkit_portfolio_form_' . sanitize_key( $_GET['zkt_ft'] ) );
		if ( is_array( $saved ) ) {
			$state['errors'] = $saved['errors'];
			$state['old']    = $saved['old'];
		}
	}
	// phpcs:enable
	return $state;
}

add_action( 'admin_post_nopriv_zeronkit_portfolio_contact', 'zeronkit_portfolio_handle_contact' );
add_action( 'admin_post_zeronkit_portfolio_contact', 'zeronkit_portfolio_handle_contact' );

/** Handles a form submission: validates, stores an Inquiry, emails the owner and redirects back. */
function zeronkit_portfolio_handle_contact() {
	$back = wp_get_referer() ?: zkt_page_url( 'contact' );
	$back = remove_query_arg( array( 'zkt_form', 'zkt_ft' ), $back );

	if ( ! isset( $_POST['zeronkit_portfolio_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['zeronkit_portfolio_nonce'] ), 'zeronkit_portfolio_contact' ) ) {
		wp_die( esc_html__( 'This form has expired. Please go back, reload the page and try again.', 'zeronkit-portfolio' ), 400 );
	}

	// Bots that fill the hidden field or submit instantly get a fake success and nothing is stored.
	$too_fast = ( time() - absint( $_POST['zeronkit_portfolio_ts'] ?? 0 ) ) < ZERONKIT_PORTFOLIO_FORM_MIN_SECONDS;
	if ( ! empty( $_POST['website'] ) || $too_fast ) {
		wp_safe_redirect( add_query_arg( 'zkt_form', 'sent', $back ) . '#contact-form' );
		exit;
	}

	$ip       = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	$rate_key = 'zeronkit_portfolio_rate_' . md5( $ip . wp_salt() );
	$sent     = (int) get_transient( $rate_key );
	if ( $sent >= ZERONKIT_PORTFOLIO_FORM_HOURLY_MAX ) {
		wp_die( esc_html__( 'Too many messages were sent from your connection. Please try again in an hour or email directly.', 'zeronkit-portfolio' ), 429 );
	}

	$data = array(
		'name'    => sanitize_text_field( wp_unslash( $_POST['name'] ?? '' ) ),
		'email'   => sanitize_email( wp_unslash( $_POST['email'] ?? '' ) ),
		'service' => sanitize_text_field( wp_unslash( $_POST['service'] ?? '' ) ),
		'date'    => sanitize_text_field( wp_unslash( $_POST['date'] ?? '' ) ),
		'message' => sanitize_textarea_field( wp_unslash( $_POST['message'] ?? '' ) ),
	);

	$errors = array();
	if ( '' === $data['name'] || mb_strlen( $data['name'] ) > 120 ) {
		$errors['name'] = __( 'Please enter your name.', 'zeronkit-portfolio' );
	}
	if ( ! is_email( $data['email'] ) ) {
		$errors['email'] = __( 'Please enter a valid email address.', 'zeronkit-portfolio' );
	}
	if ( ! in_array( $data['service'], zeronkit_portfolio_service_choices(), true ) ) {
		$errors['service'] = __( 'Please choose a type of session.', 'zeronkit-portfolio' );
	}
	if ( '' !== $data['date'] && ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $data['date'] ) ) {
		$errors['date'] = __( 'Please use the date picker.', 'zeronkit-portfolio' );
	}
	if ( mb_strlen( $data['message'] ) < 10 || mb_strlen( $data['message'] ) > 5000 ) {
		$errors['message'] = __( 'Please write a few words about your idea (10 to 5000 characters).', 'zeronkit-portfolio' );
	}

	if ( $errors ) {
		$token = wp_generate_password( 12, false );
		set_transient( 'zeronkit_portfolio_form_' . strtolower( $token ), array( 'errors' => $errors, 'old' => $data ), 10 * MINUTE_IN_SECONDS );
		wp_safe_redirect( add_query_arg( 'zkt_ft', strtolower( $token ), $back ) . '#contact-form' );
		exit;
	}

	set_transient( $rate_key, $sent + 1, HOUR_IN_SECONDS );

	$body = sprintf(
		/* translators: 1: name, 2: email, 3: type of session, 4: preferred date, 5: message */
		__( "Name: %1\$s\nEmail: %2\$s\nSession: %3\$s\nPreferred date: %4\$s\n\n%5\$s", 'zeronkit-portfolio' ),
		$data['name'],
		$data['email'],
		$data['service'],
		$data['date'] ? $data['date'] : __( 'not given', 'zeronkit-portfolio' ),
		$data['message']
	);

	$inquiry_id = wp_insert_post(
		array(
			'post_type'    => 'zkt_inquiry',
			'post_status'  => 'private',
			'post_title'   => sprintf( '%s: %s', $data['service'], $data['name'] ),
			'post_content' => $body,
		)
	);
	if ( $inquiry_id && ! is_wp_error( $inquiry_id ) ) {
		update_post_meta( $inquiry_id, '_zkt_email', $data['email'] );
	}

	$to = zkt_info( 'notify' ) ?: get_option( 'admin_email' );
	wp_mail(
		$to,
		/* translators: 1: site title, 2: name of the sender */
		sprintf( __( '[%1$s] New inquiry from %2$s', 'zeronkit-portfolio' ), zkt_site_name(), $data['name'] ),
		$body,
		array( 'Reply-To: ' . $data['name'] . ' <' . $data['email'] . '>' )
	);

	wp_safe_redirect( add_query_arg( 'zkt_form', 'sent', $back ) . '#contact-form' );
	exit;
}
