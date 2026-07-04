<?php
/**
 * Template Name: Готелі
 *
 * Placeholder page: hotels are not part of the tour API contract yet.
 *
 * @package MustSee_Travel
 */
defined( 'ABSPATH' ) || exit;

get_header();
?>
<div class="container-site py-10">
	<h1 class="ty-h1 text-gray-900">Готелі</h1>
	<div class="mt-6"><?php mustsee_empty_notice( 'Готелі зʼявляться незабаром.' ); ?></div>
</div>

<?php
get_template_part( 'template-parts/lead-form' );
get_template_part( 'template-parts/newsletter' );
get_footer();
