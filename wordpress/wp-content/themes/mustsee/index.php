<?php
/**
 * Fallback template (blog / archive / search).
 *
 * @package MustSee_Travel
 */
defined( 'ABSPATH' ) || exit;

get_header();
?>
<div class="container-site py-10">
	<?php if ( have_posts() ) : ?>
		<h1 class="ty-h1 text-gray-900">
			<?php
			if ( is_home() ) {
				echo esc_html( get_the_title( get_option( 'page_for_posts' ) ) ?: 'Новини' );
			} else {
				the_archive_title();
			}
			?>
		</h1>
		<div class="mt-6 grid gap-5 md:grid-cols-3">
			<?php while ( have_posts() ) : the_post(); ?>
				<a href="<?php the_permalink(); ?>" class="group block overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm transition hover:shadow-md">
					<?php if ( has_post_thumbnail() ) : ?>
						<div class="relative aspect-[16/10] overflow-hidden"><?php the_post_thumbnail( 'medium_large', array( 'class' => 'h-full w-full object-cover' ) ); ?></div>
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
		<h1 class="ty-h1 text-gray-900">Нічого не знайдено</h1>
		<p class="ty-text mt-3 text-gray-500">На жаль, за вашим запитом немає результатів.</p>
	<?php endif; ?>
</div>
<?php
get_footer();
