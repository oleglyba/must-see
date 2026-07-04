<?php
/**
 * Default page template.
 *
 * @package MustSee_Travel
 */
defined( 'ABSPATH' ) || exit;

get_header();
?>
<div class="container-site py-10">
	<?php while ( have_posts() ) : the_post(); ?>
		<h1 class="ty-h1 text-gray-900"><?php the_title(); ?></h1>
		<div class="ty-text prose mt-6 max-w-none space-y-4 text-gray-600">
			<?php the_content(); ?>
		</div>
	<?php endwhile; ?>
</div>
<?php
get_footer();
