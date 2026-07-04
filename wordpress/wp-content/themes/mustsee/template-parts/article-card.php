<?php
/**
 * Article / news card.
 *
 * @var array $args { href, date, title, id?: int }
 */
defined( 'ABSPATH' ) || exit;
$a   = $args;
$pid = isset( $a['id'] ) ? (int) $a['id'] : 0;
?>
<a href="<?php echo esc_url( mustsee_url( $a['href'] ) ); ?>" class="group block overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm transition hover:shadow-md">
	<?php if ( $pid && has_post_thumbnail( $pid ) ) : ?>
		<div class="relative aspect-[16/10] overflow-hidden"><?php echo get_the_post_thumbnail( $pid, 'medium_large', array( 'class' => 'absolute inset-0 h-full w-full object-cover' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
	<?php else : ?>
		<div class="media-placeholder relative aspect-[16/10]"></div>
	<?php endif; ?>
	<div class="p-4">
		<p class="ty-13 text-gray-400"><?php echo esc_html( $a['date'] ); ?></p>
		<h3 class="ty-opys-bold mt-1 text-gray-800"><?php echo esc_html( $a['title'] ); ?></h3>
	</div>
</a>
