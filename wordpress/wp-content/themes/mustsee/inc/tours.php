<?php
/**
 * Tour data layer — thin wrappers over the FastAPI client + rendering helpers.
 *
 * @package MustSee_Travel
 */
defined( 'ABSPATH' ) || exit;

/** @param array $filters { category?, country?, page?, per_page?, featured? } */
function mustsee_tours( array $filters = array() ): array {
	$data = mustsee_api_get( '/tours', array_filter( $filters ) );
	return is_array( $data )
		? $data + array( 'items' => array(), 'total' => 0, 'pages' => 0 )
		: array( 'items' => array(), 'total' => 0, 'pages' => 0 );
}

function mustsee_tour( string $slug ): ?array {
	$tour = mustsee_api_get( '/tours/' . rawurlencode( $slug ) );
	return is_array( $tour ) && ! empty( $tour['slug'] ) ? $tour : null;
}

function mustsee_tour_categories(): array {
	$cats = mustsee_api_get( '/categories' );
	return is_array( $cats ) ? $cats : array();
}

function mustsee_tour_url( array $tour ): string {
	return home_url( '/tours/' . ( $tour['slug'] ?? '' ) . '/' );
}

function mustsee_tour_price( array $tour ): string {
	return isset( $tour['price_eur'] ) && '' !== (string) $tour['price_eur'] ? $tour['price_eur'] . ' EUR' : '';
}

/** Media box for WP posts (news, reviews). */
function mustsee_media( int $post_id, string $classes, string $size = 'medium_large', string $inner = '' ): void {
	$has = $post_id && has_post_thumbnail( $post_id );
	// Add `relative` only when the caller doesn't already set positioning.
	$position = preg_match( '/\b(absolute|fixed|sticky)\b/', $classes ) ? '' : 'relative ';
	printf( '<div class="%soverflow-hidden %s%s">', $position, esc_attr( $classes ), $has ? '' : ' media-placeholder' );
	if ( $has ) {
		echo get_the_post_thumbnail( $post_id, $size, array( 'class' => 'absolute inset-0 h-full w-full object-cover', 'loading' => 'lazy', 'decoding' => 'async' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	echo $inner; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo '</div>';
}

/** Same media box, driven by a tour's first image URL. */
function mustsee_tour_image( array $tour, string $classes, string $inner = '' ): void {
	$src      = $tour['images'][0] ?? '';
	$position = preg_match( '/\b(absolute|fixed|sticky)\b/', $classes ) ? '' : 'relative ';
	printf( '<div class="%soverflow-hidden %s%s">', $position, esc_attr( $classes ), $src ? '' : ' media-placeholder' );
	if ( $src ) {
		printf(
			'<img src="%s" alt="%s" loading="lazy" decoding="async" class="absolute inset-0 h-full w-full object-cover" />',
			esc_url( $src ),
			esc_attr( $tour['title'] ?? '' )
		);
	}
	echo $inner; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo '</div>';
}

function mustsee_tour_card( array $tour ): void {
	$days      = $tour['days'] ?? '';
	$price     = mustsee_tour_price( $tour );
	$countries = implode( ', ', (array) ( $tour['countries'] ?? array() ) );
	$city      = $tour['departure_city'] ?? '';
	$badges    = '';
	if ( $days ) {
		$badges .= '<span class="ty-text-bold absolute left-3 top-3 z-10 rounded-full bg-accent px-3 py-1 text-white">' . esc_html( $days ) . '</span>';
	}
	if ( $price ) {
		$badges .= '<span class="ty-text-bold absolute right-3 top-3 z-10 rounded-full bg-white/95 px-3 py-1 text-accent">' . esc_html( $price ) . '</span>';
	}
	?>
	<a href="<?php echo esc_url( mustsee_tour_url( $tour ) ); ?>" class="group block overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm transition hover:shadow-md">
		<?php mustsee_tour_image( $tour, 'aspect-[4/3]', $badges ); ?>
		<div class="space-y-1 p-4">
			<p class="ty-opys-bold text-gray-800"><?php echo esc_html( $countries ?: ( $tour['title'] ?? '' ) ); ?></p>
			<?php if ( $city ) : ?>
				<p class="ty-text flex items-center gap-1 text-gray-500">
					<svg aria-hidden="true" viewBox="0 0 24 24" class="h-4 w-4 fill-current text-brand"><path d="M12 2a7 7 0 0 0-7 7c0 5 7 13 7 13s7-8 7-13a7 7 0 0 0-7-7Zm0 9.5A2.5 2.5 0 1 1 12 6a2.5 2.5 0 0 1 0 5.5Z" /></svg>
					<?php echo esc_html( $city ); ?>
				</p>
			<?php endif; ?>
		</div>
	</a>
	<?php
}

function mustsee_empty_notice( string $text = 'Тури тимчасово недоступні.' ): void {
	echo '<p class="ty-text text-gray-500">' . esc_html( $text ) . '</p>';
}
