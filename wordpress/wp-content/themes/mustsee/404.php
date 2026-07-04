<?php
/**
 * 404 — page not found.
 *
 * @package MustSee_Travel
 */
defined( 'ABSPATH' ) || exit;

get_header();
?>
<div class="container-site py-20 text-center">
	<p class="ty-h1 text-brand">404</p>
	<h1 class="ty-h1-sm mt-2 text-gray-900">Сторінку не знайдено</h1>
	<p class="ty-text mx-auto mt-3 max-w-md text-gray-600">Можливо, її переміщено або видалено. Спробуйте пошук або поверніться на головну.</p>

	<form method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="mx-auto mt-6 flex max-w-md gap-3">
		<input type="search" name="s" placeholder="Пошук по сайту…" aria-label="Пошук" class="field flex-1" />
		<button type="submit" class="btn-accent">Знайти</button>
	</form>

	<div class="mt-6 flex flex-wrap justify-center gap-3">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn-accent">На головну</a>
		<a href="<?php echo esc_url( home_url( '/tours/' ) ); ?>" class="btn-outline">Каталог турів</a>
	</div>
</div>
<?php
get_footer();
