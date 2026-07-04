<?php
/**
 * Hover dropdown for a nav item with children.
 *
 * Args: 'item' (['title','children'=>[['title','href'],...]]),
 * 'button_class', 'panel_class' — Tailwind classes for trigger and panel.
 *
 * @package MustSee_Travel
 */
defined( 'ABSPATH' ) || exit;

$item         = $args['item'] ?? array();
$button_class = $args['button_class'] ?? 'ty-menu-vkladky text-gray-800';
$panel_class  = $args['panel_class'] ?? 'left-0 min-w-56';
?>
<div class="dropdown group relative">
	<button type="button" data-dropdown aria-haspopup="true" aria-expanded="false" class="<?php echo esc_attr( $button_class ); ?> inline-flex items-center gap-1 hover:text-brand">
		<?php echo esc_html( $item['title'] ?? '' ); ?>
		<svg aria-hidden="true" viewBox="0 0 24 24" class="h-3 w-3 fill-current opacity-70"><path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" fill="none" /></svg>
	</button>
	<div class="dropdown-menu invisible absolute top-full z-40 mt-2 rounded-xl border border-gray-100 bg-white p-2 opacity-0 shadow-lg transition group-hover:visible group-hover:opacity-100 <?php echo esc_attr( $panel_class ); ?>">
		<?php foreach ( $item['children'] ?? array() as $c ) : ?>
			<a href="<?php echo esc_url( mustsee_url( $c['href'] ) ); ?>" class="ty-text block rounded-lg px-3 py-2 text-gray-700 transition hover:bg-gray-50 hover:text-brand"><?php echo esc_html( $c['title'] ); ?></a>
		<?php endforeach; ?>
	</div>
</div>
