<?php
/**
 * Single post (news article).
 *
 * @package MustSee_Travel
 */
defined( 'ABSPATH' ) || exit;

get_header();
?>
<div class="container-site py-10">
	<?php while ( have_posts() ) : the_post(); ?>
		<?php
		get_template_part(
			'template-parts/breadcrumbs',
			null,
			array(
				'items'   => array( array( 'title' => 'Новини', 'href' => '/news/' ) ),
				'current' => get_the_title(),
			)
		);
		?>
		<article class="mt-4 max-w-3xl">
			<h1 class="ty-h1-sm text-gray-900"><?php the_title(); ?></h1>
			<p class="ty-13 mt-2 text-gray-400"><?php echo esc_html( get_the_date() ); ?></p>
			<?php if ( has_post_thumbnail() ) : ?>
				<div class="relative mt-5 aspect-[16/8] overflow-hidden rounded-2xl"><?php the_post_thumbnail( 'large', array( 'class' => 'h-full w-full object-cover' ) ); ?></div>
			<?php else : ?>
				<div class="media-placeholder relative mt-5 aspect-[16/8] overflow-hidden rounded-2xl"></div>
			<?php endif; ?>
			<div class="ty-text prose mt-6 max-w-none space-y-4 text-gray-600"><?php the_content(); ?></div>
		</article>
	<?php endwhile; ?>
</div>
<?php
get_template_part( 'template-parts/lead-form' );
get_template_part( 'template-parts/newsletter' );
get_footer();
