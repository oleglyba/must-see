<?php
/**
 * Routing for API-backed tour pages: /tours/{slug}/ → tour-single.php.
 * The catalog itself is a WP page at /tours/ (template-tours.php).
 *
 * @package MustSee_Travel
 */
defined( 'ABSPATH' ) || exit;

add_action(
	'init',
	function () {
		add_rewrite_rule( '^tours/([^/]+)/?$', 'index.php?mustsee_tour=$matches[1]', 'top' );
	}
);

add_filter(
	'query_vars',
	function ( array $vars ): array {
		$vars[] = 'mustsee_tour';
		return $vars;
	}
);

add_filter(
	'template_include',
	function ( string $template ): string {
		$slug = get_query_var( 'mustsee_tour' );
		if ( ! $slug ) {
			return $template;
		}
		if ( ! mustsee_tour( (string) $slug ) ) {
			global $wp_query;
			$wp_query->set_404();
			status_header( 404 );
			return get_404_template();
		}
		status_header( 200 );
		return get_stylesheet_directory() . '/tour-single.php';
	}
);

// Flush rewrite rules once per theme version so the /tours/{slug} rule is registered.
add_action(
	'init',
	function () {
		if ( get_option( 'mustsee_rewrites' ) !== MUSTSEE_VERSION ) {
			flush_rewrite_rules();
			update_option( 'mustsee_rewrites', MUSTSEE_VERSION );
		}
	},
	99
);
