<?php
/**
 * Authentication: AJAX login + partner registration (application).
 *
 * @package MustSee_Travel
 */
defined( 'ABSPATH' ) || exit;

add_action( 'wp_ajax_nopriv_mustsee_login', 'mustsee_ajax_login' );
add_action( 'wp_ajax_mustsee_login', 'mustsee_ajax_login' );
function mustsee_ajax_login(): void {
	if ( ! check_ajax_referer( 'mustsee', 'nonce', false ) ) {
		mustsee_json_error( 'Сесія застаріла, оновіть сторінку.' );
	}
	if ( is_user_logged_in() ) {
		mustsee_json_success( 'Ви вже увійшли.', array( 'redirect' => home_url( '/cabinet/' ) ) );
	}
	// Honeypot bot → behave like a normal failed login (don't reveal the trap).
	if ( mustsee_is_spam() ) {
		mustsee_log( 'Login blocked (honeypot)', 'warning', array( 'ip' => mustsee_client_ip() ) );
		mustsee_json_error( 'Невірний логін або пароль.' );
	}
	// Throttle credential stuffing per IP.
	if ( ! mustsee_rate_limit( 'login', 8, 60 ) ) {
		mustsee_log( 'Login throttled', 'warning', array( 'ip' => mustsee_client_ip() ) );
		mustsee_json_error( 'Забагато спроб входу. Спробуйте за хвилину.' );
	}
	$login    = sanitize_text_field( wp_unslash( $_POST['log'] ?? '' ) );
	$password = (string) ( $_POST['pwd'] ?? '' );
	if ( '' === $login || '' === $password ) {
		mustsee_json_error( 'Вкажіть логін і пароль.' );
	}
	$user = wp_signon(
		array(
			'user_login'    => $login,
			'user_password' => $password,
			'remember'      => ! empty( $_POST['remember'] ),
		),
		is_ssl()
	);
	if ( is_wp_error( $user ) ) {
		mustsee_log( 'Login failed', 'warning', array( 'ip' => mustsee_client_ip(), 'login' => $login ) );
		mustsee_json_error( 'Невірний логін або пароль.' );
	}
	mustsee_json_success( 'Вхід виконано.', array( 'redirect' => home_url( '/cabinet/' ) ) );
}

add_action( 'wp_ajax_nopriv_mustsee_register', 'mustsee_ajax_register' );
add_action( 'wp_ajax_mustsee_register', 'mustsee_ajax_register' );
function mustsee_ajax_register(): void {
	if ( ! check_ajax_referer( 'mustsee', 'nonce', false ) ) {
		mustsee_json_error( 'Сесія застаріла, оновіть сторінку.' );
	}
	if ( mustsee_is_spam() ) {
		mustsee_log( 'Spam blocked (register)', 'warning' );
		mustsee_json_success( 'Дякуємо! Заявку надіслано.' );
	}
	if ( ! mustsee_rate_limit( 'register', 5, 300 ) ) {
		mustsee_log( 'Register throttled', 'warning', array( 'ip' => mustsee_client_ip() ) );
		mustsee_json_error( 'Забагато спроб. Спробуйте трохи пізніше.' );
	}

	$raw   = isset( $_POST['field'] ) && is_array( $_POST['field'] ) ? wp_unslash( $_POST['field'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.Missing
	$clean = array();
	foreach ( $raw as $label => $value ) {
		$value = sanitize_text_field( $value );
		if ( '' !== trim( $value ) ) {
			$clean[ sanitize_text_field( $label ) ] = $value;
		}
	}
	if ( count( $clean ) < 2 ) {
		mustsee_json_error( 'Заповніть, будь ласка, форму.' );
	}

	$title = $clean['Повна юридична назва (ФОП…; ТОВ…)'] ?? ( $clean['Комерційна назва'] ?? 'Заявка партнера' );
	$id    = mustsee_store_lead( $title, array( '_lead_type' => 'registration', '_lead_fields' => wp_json_encode( $clean, JSON_UNESCAPED_UNICODE ) ) );
	mustsee_log( 'New partner application', 'success', array( 'id' => $id ) );

	$body = "Нова заявка партнера:\n\n";
	foreach ( $clean as $label => $value ) {
		$body .= "{$label}: {$value}\n";
	}
	mustsee_queue_email( get_option( 'admin_email' ), 'Нова заявка партнера', $body );

	mustsee_json_success( 'Дякуємо! Заявку надіслано — ми звʼяжемося з вами.' );
}
