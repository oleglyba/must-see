<?php
/**
 * Hero featured tour — first featured tour from the API (fallback: static copy).
 *
 * @package MustSee_Travel
 */
defined( 'ABSPATH' ) || exit;

$hero = mustsee_tours( array( 'featured' => 1, 'per_page' => 1 ) )['items'][0] ?? null;

if ( $hero ) {
	$title     = $hero['title'] ?? '';
	$days      = $hero['days'] ?? '';
	$departure = ! empty( $hero['departure_city'] ) ? 'Виїзд: ' . $hero['departure_city'] : '';
	$price     = mustsee_tour_price( $hero );
	$kicker    = implode( ', ', (array) ( $hero['countries'] ?? array() ) ) ?: 'Автобусний тур | екскурсійний';
	$href      = mustsee_tour_url( $hero );
	$image     = $hero['images'][0] ?? '';
} else {
	$title     = 'Рим, Венеція, Флоренція';
	$days      = '5 днів';
	$departure = 'Виїзд зі Львова';
	$price     = 'від 7 852 ₴';
	$kicker    = 'Автобусний тур | екскурсійний';
	$href      = home_url( '/tours/' );
	$image     = '';
}
?>
<div class="container-site pt-6">
	<section class="relative isolate overflow-hidden rounded-2xl">
		<?php if ( $image ) : ?>
			<img src="<?php echo esc_url( $image ); ?>" alt="" class="absolute inset-0 -z-10 h-full w-full object-cover" loading="eager" fetchpriority="high" decoding="async" />
		<?php else : ?>
			<div class="absolute inset-0 -z-10 media-placeholder"></div>
		<?php endif; ?>
		<div class="absolute inset-0 -z-10 bg-black/35"></div>
		<div class="flex min-h-[440px] flex-col items-center justify-center px-6 py-20 text-center text-white">
			<p class="ty-h4-caps text-white/85"><?php echo esc_html( $kicker ); ?></p>
			<h1 class="ty-h1 mt-3 text-white"><?php echo esc_html( $title ); ?></h1>
			<div class="ty-h7 mt-5 flex flex-wrap items-center justify-center gap-3 text-white/90">
				<?php if ( $days ) : ?><span><?php echo esc_html( $days ); ?></span><span class="opacity-40">|</span><?php endif; ?>
				<?php if ( $departure ) : ?><span><?php echo esc_html( $departure ); ?></span><span class="opacity-40">|</span><?php endif; ?>
				<span>Ціна: <?php echo esc_html( $price ); ?></span>
			</div>
			<a href="<?php echo esc_url( $href ); ?>" class="btn-accent mt-8">Детальніше</a>
		</div>
	</section>
</div>
