<?php
/**
 * Template Name: Новини (список)
 *
 * @package MustSee_Travel
 */
defined( 'ABSPATH' ) || exit;

get_header();

$items = array(
	array( 'href' => '#', 'date' => '11 / 01 / 2026', 'title' => 'Self-care-подорож до Шарм-ель-Шейху' ),
	array( 'href' => '#', 'date' => '11 / 01 / 2026', 'title' => 'Екскурсія у подарунок на Шрі-Ланці' ),
	array( 'href' => '#', 'date' => '11 / 01 / 2026', 'title' => 'Не встигли помандрувати на Новий рік?' ),
	array( 'href' => '#', 'date' => '08 / 01 / 2026', 'title' => 'Топ напрямків весни 2026' ),
	array( 'href' => '#', 'date' => '05 / 01 / 2026', 'title' => 'Як зібратися в автобусний тур' ),
	array( 'href' => '#', 'date' => '02 / 01 / 2026', 'title' => 'Карнавали Європи: куди поїхати' ),
);

// If real posts exist, prefer them.
$paged = max( 1, (int) get_query_var( 'paged' ), isset( $_GET['paged'] ) ? (int) $_GET['paged'] : 1 ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$query = new WP_Query( array( 'post_type' => 'post', 'posts_per_page' => 9, 'paged' => $paged, 'ignore_sticky_posts' => true ) );
?>
<div class="container-site py-10">
	<h1 class="ty-h1 text-gray-900">Новини</h1>
	<div class="mt-6 grid gap-5 md:grid-cols-3">
		<?php if ( $query->have_posts() ) : ?>
			<?php
			while ( $query->have_posts() ) :
				$query->the_post();
				get_template_part(
					'template-parts/article-card',
					null,
					array( 'href' => get_permalink(), 'date' => get_the_date( 'd / m / Y' ), 'title' => get_the_title() )
				);
			endwhile;
			wp_reset_postdata();
			?>
		<?php else : ?>
			<?php foreach ( $items as $a ) { get_template_part( 'template-parts/article-card', null, $a ); } ?>
		<?php endif; ?>
	</div>

	<?php
	if ( $query->have_posts() ) {
		get_template_part(
			'template-parts/pagination',
			null,
			array(
				'total'   => $query->max_num_pages,
				'current' => $paged,
				'base'    => esc_url_raw( add_query_arg( 'paged', '%#%', home_url( '/news/' ) ) ),
			)
		);
	}
	?>
</div>

<?php
get_template_part( 'template-parts/lead-form' );
get_template_part( 'template-parts/newsletter' );
get_footer();
