<?php
/**
 * Template Name: Каталог турів
 *
 * @package MustSee_Travel
 */
defined( 'ABSPATH' ) || exit;

get_header();

$paged    = max( 1, (int) get_query_var( 'paged' ), isset( $_GET['paged'] ) ? (int) $_GET['paged'] : 1 ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$cat      = isset( $_GET['cat'] ) ? sanitize_title( wp_unslash( $_GET['cat'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$country  = isset( $_GET['country'] ) ? sanitize_text_field( wp_unslash( $_GET['country'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$base_url = home_url( '/tours/' );

$result = mustsee_tours(
	array(
		'category' => $cat,
		'country'  => $country,
		'page'     => $paged,
		'per_page' => 6,
	)
);
$tours     = $result['items'];
$terms     = mustsee_tour_categories();
$countries = mustsee_config( 'countries' );
?>
<?php get_template_part( 'template-parts/hero' ); ?>
<?php get_template_part( 'template-parts/search-bar' ); ?>

<div class="container-site py-10">
	<h2 class="ty-h2 text-center text-brand">Каталог турів</h2>

	<div class="mt-6 flex flex-col gap-8 lg:flex-row">
		<aside class="w-full shrink-0 lg:w-64">
			<button type="button" data-toggle="#catalog-filters" class="btn-accent w-full lg:hidden">Фільтри</button>
			<div id="catalog-filters" class="hidden space-y-6 lg:block">
				<div class="rounded-2xl border border-gray-100 p-4">
					<h3 class="ty-menu-vkladky text-brand">Тип туру</h3>
					<ul class="mt-3 space-y-2">
						<li><a href="<?php echo esc_url( $base_url ); ?>" class="ty-text flex items-center gap-2 <?php echo '' === $cat ? 'font-semibold text-accent' : 'text-gray-700 hover:text-brand'; ?>">Всі тури</a></li>
						<?php foreach ( $terms as $term ) : ?>
							<li><a href="<?php echo esc_url( add_query_arg( 'cat', $term['slug'], $base_url ) ); ?>" class="ty-text flex items-center gap-2 <?php echo $cat === $term['slug'] ? 'font-semibold text-accent' : 'text-gray-700 hover:text-brand'; ?>"><?php echo esc_html( $term['name'] ); ?></a></li>
						<?php endforeach; ?>
					</ul>
				</div>
				<div class="rounded-2xl border border-gray-100 p-4">
					<h3 class="ty-menu-vkladky text-brand">Країна</h3>
					<ul class="mt-3 space-y-2">
						<?php foreach ( $countries as $c ) : ?>
							<li><a href="<?php echo esc_url( add_query_arg( 'country', $c, $base_url ) ); ?>" class="ty-text flex items-center gap-2 <?php echo $country === $c ? 'font-semibold text-accent' : 'text-gray-700 hover:text-brand'; ?>"><?php echo esc_html( $c ); ?></a></li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
		</aside>

		<div class="min-w-0 flex-1">
			<?php if ( $tours ) : ?>
				<div class="space-y-5">
					<?php foreach ( $tours as $tour ) : ?>
						<article class="flex flex-col gap-4 rounded-2xl bg-accent-50 p-4 sm:flex-row">
							<?php mustsee_tour_image( $tour, 'h-44 w-full shrink-0 rounded-xl sm:h-40 sm:w-56' ); ?>
							<div class="flex min-w-0 flex-1 flex-col">
								<div class="flex items-start justify-between gap-3">
									<h3 class="ty-h7 text-gray-900"><?php echo esc_html( $tour['title'] ?? '' ); ?></h3>
									<?php if ( ! empty( $tour['date_range'] ) ) : ?><span class="ty-13 shrink-0 text-gray-500"><?php echo esc_html( $tour['date_range'] ); ?></span><?php endif; ?>
								</div>
								<dl class="ty-13 mt-2 space-y-1 text-gray-600">
									<?php if ( ! empty( $tour['departure_city'] ) ) : ?><p><span class="font-semibold">Виїзд:</span> <?php echo esc_html( $tour['departure_city'] ); ?></p><?php endif; ?>
									<?php if ( ! empty( $tour['places'] ) ) : ?><p><span class="font-semibold">Місце:</span> <?php echo esc_html( $tour['places'] ); ?></p><?php endif; ?>
									<?php if ( ! empty( $tour['days'] ) ) : ?><p><span class="font-semibold">Тривалість:</span> <?php echo esc_html( $tour['days'] ); ?></p><?php endif; ?>
								</dl>
								<div class="mt-auto flex items-end justify-between pt-3">
									<span class="ty-h6 text-accent"><?php echo esc_html( mustsee_tour_price( $tour ) ); ?></span>
									<a href="<?php echo esc_url( mustsee_tour_url( $tour ) ); ?>" class="btn-accent">Замовити</a>
								</div>
							</div>
						</article>
					<?php endforeach; ?>
				</div>

				<?php
				$pagination_args          = array_filter( array( 'cat' => $cat, 'country' => $country ) );
				$pagination_args['paged'] = '%#%';
				get_template_part(
					'template-parts/pagination',
					null,
					array(
						'total'   => (int) $result['pages'],
						'current' => $paged,
						'base'    => esc_url_raw( add_query_arg( $pagination_args, $base_url ) ),
					)
				);
				?>
			<?php else : ?>
				<?php mustsee_empty_notice( 'За вибраними фільтрами турів не знайдено.' ); ?>
			<?php endif; ?>
		</div>
	</div>
</div>

<?php
get_template_part( 'template-parts/lead-form' );
get_footer();
