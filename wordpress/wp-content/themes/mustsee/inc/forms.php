<?php
/**
 * Lead / newsletter forms: CPT storage + AJAX handlers.
 *
 * @package MustSee_Travel
 */
defined( 'ABSPATH' ) || exit;

add_action(
	'init',
	function () {
		register_post_type(
			'mustsee_lead',
			array(
				'labels'       => array(
					'name'          => 'Заявки',
					'singular_name' => 'Заявка',
					'menu_name'     => 'Заявки',
				),
				'public'       => false,
				'show_ui'      => true,
				'menu_icon'    => 'dashicons-email',
				'supports'     => array( 'title' ),
				'capability_type' => 'post',
			)
		);
	}
);

/**
 * Lightweight spam check: honeypot field filled, or submitted implausibly fast.
 * (Time check is JS-driven so it stays compatible with full-page caching.)
 */
function mustsee_is_spam(): bool {
	if ( ! empty( $_POST['company'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		return true;
	}
	$elapsed = isset( $_POST['elapsed'] ) ? (int) $_POST['elapsed'] : 9999; // phpcs:ignore WordPress.Security.NonceVerification.Missing
	return $elapsed < 1500;
}

/** Store a submission as a mustsee_lead post. */
function mustsee_store_lead( string $title, array $meta ): int {
	$id = wp_insert_post(
		array(
			'post_type'   => 'mustsee_lead',
			'post_status' => 'publish',
			'post_title'  => $title,
		),
		true
	);
	if ( is_wp_error( $id ) ) {
		return 0;
	}
	foreach ( $meta as $key => $value ) {
		update_post_meta( $id, $key, $value );
	}
	return (int) $id;
}

add_action( 'wp_ajax_mustsee_lead', 'mustsee_ajax_lead' );
add_action( 'wp_ajax_nopriv_mustsee_lead', 'mustsee_ajax_lead' );
function mustsee_ajax_lead(): void {
	if ( ! check_ajax_referer( 'mustsee', 'nonce', false ) ) {
		mustsee_json_error( 'Сесія застаріла, оновіть сторінку.' );
	}
	if ( mustsee_is_spam() ) {
		mustsee_log( 'Spam blocked (lead)', 'warning' );
		mustsee_json_success( 'Дякуємо! Ми звʼяжемося з вами найближчим часом.' );
	}
	if ( ! mustsee_rate_limit( 'lead', 5, 120 ) ) {
		mustsee_log( 'Lead throttled', 'warning', array( 'ip' => mustsee_client_ip() ) );
		mustsee_json_error( 'Забагато заявок. Спробуйте трохи пізніше.' );
	}
	$name  = sanitize_text_field( wp_unslash( $_POST['name'] ?? '' ) );
	$phone = sanitize_text_field( wp_unslash( $_POST['phone'] ?? '' ) );

	if ( '' === trim( $name ) ) {
		mustsee_json_error( "Вкажіть ім'я." );
	}
	if ( strlen( preg_replace( '/\D/', '', $phone ) ) < 10 ) {
		mustsee_json_error( 'Вкажіть коректний номер телефону.' );
	}

	$id = mustsee_store_lead(
		$name,
		array( '_lead_type' => 'lead', '_lead_phone' => $phone, '_lead_source' => esc_url_raw( wp_get_referer() ?: '' ) )
	);
	mustsee_log( 'New lead stored', 'success', array( 'id' => $id ) );
	mustsee_queue_email(
		get_option( 'admin_email' ),
		'Нова заявка з сайту',
		"Імʼя: {$name}\nТелефон: {$phone}\nДжерело: " . ( wp_get_referer() ?: '—' )
	);

	mustsee_json_success( 'Дякуємо! Ми звʼяжемося з вами найближчим часом.' );
}

add_action( 'wp_ajax_mustsee_newsletter', 'mustsee_ajax_newsletter' );
add_action( 'wp_ajax_nopriv_mustsee_newsletter', 'mustsee_ajax_newsletter' );
function mustsee_ajax_newsletter(): void {
	if ( ! check_ajax_referer( 'mustsee', 'nonce', false ) ) {
		mustsee_json_error( 'Сесія застаріла, оновіть сторінку.' );
	}
	if ( mustsee_is_spam() ) {
		mustsee_log( 'Spam blocked (newsletter)', 'warning' );
		mustsee_json_success( 'Дякуємо за підписку!' );
	}
	if ( ! mustsee_rate_limit( 'newsletter', 5, 120 ) ) {
		mustsee_log( 'Newsletter throttled', 'warning', array( 'ip' => mustsee_client_ip() ) );
		mustsee_json_error( 'Забагато запитів. Спробуйте трохи пізніше.' );
	}
	$email   = sanitize_email( wp_unslash( $_POST['email'] ?? '' ) );
	$consent = ! empty( $_POST['consent'] );

	if ( ! is_email( $email ) ) {
		mustsee_json_error( 'Вкажіть коректний e-mail.' );
	}
	if ( ! $consent ) {
		mustsee_json_error( 'Потрібна згода на обробку даних.' );
	}

	$id = mustsee_store_lead( $email, array( '_lead_type' => 'newsletter', '_lead_email' => $email ) );
	mustsee_log( 'New subscriber stored', 'success', array( 'id' => $id ) );
	mustsee_queue_email( get_option( 'admin_email' ), 'Нова підписка на розсилку', "E-mail: {$email}" );

	mustsee_json_success( 'Дякуємо за підписку!' );
}

add_action( 'wp_ajax_mustsee_booking', 'mustsee_ajax_booking' );
add_action( 'wp_ajax_nopriv_mustsee_booking', 'mustsee_ajax_booking' );
/** Booking stub: stores the request locally until the FastAPI endpoint exists. */
function mustsee_ajax_booking(): void {
	if ( ! check_ajax_referer( 'mustsee', 'nonce', false ) ) {
		mustsee_json_error( 'Сесія застаріла, оновіть сторінку.' );
	}
	if ( ! mustsee_rate_limit( 'booking', 5, 120 ) ) {
		mustsee_log( 'Booking throttled', 'warning', array( 'ip' => mustsee_client_ip() ) );
		mustsee_json_error( 'Забагато запитів. Спробуйте трохи пізніше.' );
	}
	$tour = sanitize_title( wp_unslash( $_POST['tour'] ?? '' ) );
	$meta = array(
		'_lead_type'      => 'booking',
		'_booking_tour'   => $tour,
		'_booking_dep'    => sanitize_text_field( wp_unslash( $_POST['departure'] ?? '' ) ),
		'_booking_room'   => sanitize_text_field( wp_unslash( $_POST['room'] ?? '' ) ),
		'_booking_seats'  => sanitize_text_field( wp_unslash( $_POST['seats'] ?? '' ) ),
		'_booking_people' => sanitize_textarea_field( wp_unslash( $_POST['tourists'] ?? '' ) ),
	);

	$id = mustsee_store_lead( 'Бронювання: ' . ( $tour ? $tour : 'без туру' ), $meta );
	mustsee_log( 'Booking stored', 'success', array( 'id' => $id ) );
	mustsee_queue_email(
		get_option( 'admin_email' ),
		'Нове бронювання',
		"Тур: {$tour}\nВиїзд: {$meta['_booking_dep']}\nРозміщення: {$meta['_booking_room']}\nМісця: {$meta['_booking_seats']}"
	);

	mustsee_json_success( 'Заявку на бронювання прийнято! Менеджер звʼяжеться з вами.' );
}
