<?php
/**
 * FastAPI client: wp_remote_get + transient cache + local fixture fallback.
 * Define MUSTSEE_API_URL in wp-config.php to use the live API.
 *
 * @package MustSee_Travel
 */
defined( 'ABSPATH' ) || exit;

function mustsee_api_url(): string {
	$base = defined( 'MUSTSEE_API_URL' ) ? MUSTSEE_API_URL : '';
	return untrailingslashit( (string) apply_filters( 'mustsee_api_url', $base ) );
}

/**
 * GET {base}{path}?{query} as a decoded array; transient-cached.
 * Falls back to bundled fixtures when no API is configured or the call fails.
 */
function mustsee_api_get( string $path, array $query = array() ): ?array {
	$base = mustsee_api_url();
	if ( '' === $base ) {
		return mustsee_api_fixture( $path, $query );
	}

	$key    = 'mustsee_api_' . md5( $path . wp_json_encode( $query ) );
	$cached = get_transient( $key );
	if ( false !== $cached ) {
		return $cached;
	}

	$response = wp_remote_get(
		add_query_arg( $query, $base . $path ),
		array( 'timeout' => 5, 'headers' => array( 'Accept' => 'application/json' ) )
	);
	if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
		return mustsee_api_fixture( $path, $query );
	}
	$data = json_decode( wp_remote_retrieve_body( $response ), true );
	if ( ! is_array( $data ) ) {
		return mustsee_api_fixture( $path, $query );
	}

	set_transient( $key, $data, (int) apply_filters( 'mustsee_api_cache_ttl', 10 * MINUTE_IN_SECONDS ) );
	return $data;
}

/** Serve the same API contract from assets/fixtures/*.json. */
function mustsee_api_fixture( string $path, array $query = array() ): ?array {
	if ( '/categories' === $path ) {
		return mustsee_api_fixture_file( 'categories' );
	}
	if ( preg_match( '#^/tours/([^/]+)$#', $path, $m ) ) {
		foreach ( (array) mustsee_api_fixture_file( 'tours' ) as $tour ) {
			if ( ( $tour['slug'] ?? '' ) === $m[1] ) {
				return $tour;
			}
		}
		return null;
	}
	if ( '/tours' === $path ) {
		$items = (array) mustsee_api_fixture_file( 'tours' );
		if ( ! empty( $query['featured'] ) ) {
			$items = array_values( array_filter( $items, fn( $t ) => ! empty( $t['featured'] ) ) );
		}
		if ( ! empty( $query['category'] ) ) {
			$items = array_values( array_filter( $items, fn( $t ) => in_array( $query['category'], (array) ( $t['categories'] ?? array() ), true ) ) );
		}
		if ( ! empty( $query['country'] ) ) {
			$items = array_values( array_filter( $items, fn( $t ) => in_array( $query['country'], (array) ( $t['countries'] ?? array() ), true ) ) );
		}
		$per   = max( 1, (int) ( $query['per_page'] ?? 6 ) );
		$page  = max( 1, (int) ( $query['page'] ?? 1 ) );
		$total = count( $items );
		return array(
			'items' => array_slice( $items, ( $page - 1 ) * $per, $per ),
			'total' => $total,
			'pages' => (int) ceil( $total / $per ),
		);
	}
	return null;
}

function mustsee_api_fixture_file( string $name ): ?array {
	static $cache = array();
	if ( ! array_key_exists( $name, $cache ) ) {
		$file           = get_stylesheet_directory() . '/assets/fixtures/' . $name . '.json';
		$cache[ $name ] = file_exists( $file ) ? json_decode( (string) file_get_contents( $file ), true ) : null; // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	}
	return $cache[ $name ];
}
