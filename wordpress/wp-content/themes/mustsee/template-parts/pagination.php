<?php
/**
 * Styled numeric pagination (brand pills). Shared by catalog / news / archive.
 *
 * @var array $args { total: int, current: int, base?: string }
 */
defined( 'ABSPATH' ) || exit;

$total   = (int) ( $args['total'] ?? 1 );
$current = max( 1, (int) ( $args['current'] ?? 1 ) );
if ( $total < 2 ) {
	return;
}

$pl_args = array(
	'format'    => '',
	'current'   => $current,
	'total'     => $total,
	'type'      => 'array',
	'prev_next' => false,
);
if ( ! empty( $args['base'] ) ) {
	$pl_args['base'] = $args['base'];
}
$links = paginate_links( $pl_args );
if ( ! $links ) {
	return;
}
?>
<div class="mt-8 flex items-center justify-center gap-2">
	<?php
	foreach ( $links as $link ) :
		$is_current = false !== strpos( $link, 'current' );
		$href       = '';
		if ( preg_match( '/href=[\'"]([^\'"]+)[\'"]/', $link, $m ) ) {
			$href = $m[1];
		}
		$label = trim( wp_strip_all_tags( $link ) );
		$cls   = 'ty-text flex h-9 min-w-[2.25rem] items-center justify-center rounded-lg px-2 ' . ( $is_current ? 'bg-brand text-white' : 'border border-gray-200 text-gray-700 hover:border-brand' );
		?>
		<?php if ( $href ) : ?>
			<a href="<?php echo esc_url( $href ); ?>" class="<?php echo esc_attr( $cls ); ?>"><?php echo esc_html( $label ); ?></a>
		<?php else : ?>
			<span class="<?php echo esc_attr( $cls ); ?>"><?php echo esc_html( $label ); ?></span>
		<?php endif; ?>
	<?php endforeach; ?>
</div>
