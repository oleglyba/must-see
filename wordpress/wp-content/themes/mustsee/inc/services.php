<?php
/**
 * Service layer: centralized logging, standardized AJAX responses, email queue.
 *
 * @package MustSee_Travel
 */
defined( 'ABSPATH' ) || exit;

function mustsee_log( string $message, string $level = 'info', array $context = array() ): void {
	$line = sprintf(
		"[%s] %s: %s%s\n",
		gmdate( 'Y-m-d H:i:s' ),
		strtoupper( $level ),
		$message,
		$context ? ' ' . wp_json_encode( $context, JSON_UNESCAPED_UNICODE ) : ''
	);
	error_log( $line, 3, WP_CONTENT_DIR . '/mustsee.log' ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
}

function mustsee_json_success( string $message, array $data = array() ): void {
	wp_send_json( array( 'success' => true, 'type' => 'success', 'message' => $message, 'data' => $data ) );
}

function mustsee_json_error( string $message, array $data = array(), int $status = 200 ): void {
	wp_send_json( array( 'success' => false, 'type' => 'error', 'message' => $message, 'data' => $data ), $status );
}

/**
 * Queue an email off-request via Action Scheduler (bundled with WooCommerce);
 * falls back to immediate send if the scheduler is unavailable.
 */
function mustsee_queue_email( string $to, string $subject, string $body ): void {
	if ( function_exists( 'as_enqueue_async_action' ) ) {
		as_enqueue_async_action( 'mustsee_send_email', array( $to, $subject, $body ), 'mustsee' );
	} else {
		mustsee_send_email( $to, $subject, $body );
	}
}

function mustsee_send_email( string $to, string $subject, string $body ): void {
	$sent = wp_mail( $to, $subject, $body, array( 'Content-Type: text/plain; charset=UTF-8' ) );
	mustsee_log( 'Email sent: ' . $subject, $sent ? 'success' : 'error' );
}
add_action( 'mustsee_send_email', 'mustsee_send_email', 10, 3 );

/**
 * Visitor IP for rate limiting. Uses REMOTE_ADDR only — proxy headers
 * (X-Forwarded-For) are spoofable, so sites behind a trusted proxy/CDN should
 * override via the `mustsee_client_ip` filter.
 */
function mustsee_client_ip(): string {
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? (string) wp_unslash( $_SERVER['REMOTE_ADDR'] ) : '';
	$ip = filter_var( $ip, FILTER_VALIDATE_IP ) ? $ip : '0.0.0.0';
	return (string) apply_filters( 'mustsee_client_ip', $ip );
}

/**
 * Per-IP sliding-window rate limit backed by transients.
 * Returns true while under the limit, false once $max hits occur within $window
 * seconds. Each call counts as one hit.
 */
function mustsee_rate_limit( string $action, int $max = 5, int $window = 60 ): bool {
	$key  = 'ms_rl_' . $action . '_' . md5( mustsee_client_ip() );
	$hits = (int) get_transient( $key );
	if ( $hits >= $max ) {
		return false;
	}
	set_transient( $key, $hits + 1, $window );
	return true;
}
