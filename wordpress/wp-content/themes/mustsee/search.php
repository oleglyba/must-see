<?php
/**
 * Search results.
 *
 * @package MustSee_Travel
 */
defined( 'ABSPATH' ) || exit;

get_header();
$q = get_search_query();
?>
<div class="container-site py-10">
	<h1 class="ty-h1 text-gray-900">Результати пошуку</h1>
	<?php if ( $q ) : ?>
		<p class="ty-text mt-2 text-gray-500">за запитом: «<?php echo esc_html( $q ); ?>»</p>
	<?php endif; ?>

	<form method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="mt-6 flex max-w-md gap-3">
		<input type="search" name="s" value="<?php echo esc_attr( $q ); ?>" placeholder="Пошук…" aria-label="Пошук" class="field flex-1" />
		<button type="submit" class="btn-accent">Знайти</button>
	</form>

	<?php if ( have_posts() ) : ?>
		<div class="mt-8 grid gap-5 md:grid-cols-3">
			<?php while ( have_posts() ) : the_post(); ?>
				<a href="<?php the_permalink(); ?>" class="group block overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm transition hover:shadow-md">
					<?php if ( has_post_thumbnail() ) : ?>
						<div class="relative aspect-[16/10] overflow-hidden"><?php the_post_thumbnail( 'medium_large', array( 'class' => 'absolute inset-0 h-full w-full object-cover' ) ); ?></div>
					<?php else : ?>
						<div class="media-placeholder relative aspect-[16/10]"></div>
					<?php endif; ?>
					<div class="p-4">
						<p class="ty-13 text-gray-400"><?php echo esc_html( get_the_date() ); ?></p>
						<h3 class="ty-opys-bold mt-1 text-gray-800"><?php the_title(); ?></h3>
					</div>
				</a>
			<?php endwhile; ?>
		</div>
		<?php
		get_template_part(
			'template-parts/pagination',
			null,
			array(
				'total'   => $GLOBALS['wp_query']->max_num_pages,
				'current' => max( 1, (int) get_query_var( 'paged' ) ),
			)
		);
		?>
	<?php else : ?>
		<p class="ty-text mt-8 text-gray-500">Нічого не знайдено. Спробуйте інший запит.</p>
	<?php endif; ?>
</div>
<?php
get_footer();
