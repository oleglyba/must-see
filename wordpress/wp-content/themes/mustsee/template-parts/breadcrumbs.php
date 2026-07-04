<?php
/**
 * Breadcrumbs: "home / [items…] / current".
 *
 * Args: 'items' ([['title','href'],...]), 'current' (string).
 *
 * @package MustSee_Travel
 */
defined( 'ABSPATH' ) || exit;
?>
<nav class="ty-13 text-gray-400">
	<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="hover:text-brand">Головна</a>
	<?php foreach ( $args['items'] ?? array() as $crumb ) : ?>
		/ <a href="<?php echo esc_url( mustsee_url( $crumb['href'] ) ); ?>" class="hover:text-brand"><?php echo esc_html( $crumb['title'] ); ?></a>
	<?php endforeach; ?>
	/ <span class="text-gray-600"><?php echo esc_html( $args['current'] ?? '' ); ?></span>
</nav>
