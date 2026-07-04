<?php
/**
 * Social links with brand icons. URLs come from mustsee_config('socials').
 *
 * @var array $args { class?: string, dot?: string, icon?: string }
 */
defined( 'ABSPATH' ) || exit;

$wrap = isset( $args['class'] ) ? $args['class'] : '';
$dot  = isset( $args['dot'] ) ? $args['dot'] : 'bg-brand/15';
$icon = isset( $args['icon'] ) ? $args['icon'] : 'text-brand';

$icons = array(
	'tg' => 'M9.5 14.3l-.4 4c.5 0 .8-.2 1-.5l2.4-2.3 5 3.7c.9.5 1.6.2 1.8-.8l3.3-15.6c.3-1.2-.5-1.7-1.3-1.4L1.2 9.3C0 9.8 0 10.5 1 10.8l5 1.6L17.6 5c.5-.3 1-.2.6.2',
	'vb' => 'M12 2C6.9 2 3 5.3 3 9.7c0 2.3 1.1 4.3 3 5.7v3.6l3-1.7c1 .2 2 .4 3 .4 5.1 0 9-3.3 9-7.9C24 5.3 17.1 2 12 2Z',
	'fb' => 'M13 22v-8h2.7l.4-3H13V9c0-.9.3-1.5 1.6-1.5H16V4.9c-.3 0-1.2-.1-2.3-.1-2.3 0-3.7 1.3-3.7 3.8V11H7.5v3H10v8h3Z',
	'ig' => 'M12 2.2c3.2 0 3.6 0 4.8.1 3.3.1 4.8 1.7 4.9 4.9.1 1.2.1 1.6.1 4.8s0 3.6-.1 4.8c-.1 3.2-1.6 4.8-4.9 4.9-1.2.1-1.6.1-4.8.1s-3.6 0-4.8-.1c-3.3-.1-4.8-1.7-4.9-4.9C2.2 15.6 2.2 15.2 2.2 12s0-3.6.1-4.8C2.4 4 3.9 2.4 7.2 2.3 8.4 2.2 8.8 2.2 12 2.2Zm0 3.2A6.6 6.6 0 1 0 18.6 12 6.6 6.6 0 0 0 12 5.4Zm0 10.9A4.3 4.3 0 1 1 16.3 12 4.3 4.3 0 0 1 12 16.3Zm6.8-11.1a1.5 1.5 0 1 0 1.6 1.5 1.5 1.5 0 0 0-1.6-1.5Z',
	'tt' => 'M16.6 2c.3 2.3 1.6 3.7 3.8 3.9v2.6c-1.3.1-2.5-.3-3.8-1v6.1c0 4.7-3.6 6.9-7 5.7-2.6-1-3.5-4-2.3-6.4.9-1.7 2.7-2.6 4.8-2.4v2.7c-.4-.1-.8-.2-1.2-.1-1 .1-1.7.9-1.6 2 .1 1 .9 1.6 2 1.5 1.1-.1 1.8-1 1.8-2.2V2h3.5Z',
);
$socials = mustsee_config( 'socials' );
?>
<div class="flex gap-2 <?php echo esc_attr( $wrap ); ?>">
	<?php foreach ( $socials as $s ) : ?>
		<a href="<?php echo esc_url( $s['url'] ); ?>" aria-label="<?php echo esc_attr( $s['label'] ); ?>"
			<?php echo '#' === $s['url'] ? '' : 'target="_blank" rel="noopener"'; ?>
			class="flex h-8 w-8 items-center justify-center rounded-full transition hover:opacity-80 <?php echo esc_attr( $dot . ' ' . $icon ); ?>">
			<svg viewBox="0 0 24 24" aria-hidden="true" class="h-4 w-4 fill-current"><path d="<?php echo esc_attr( $icons[ $s['icon'] ] ?? '' ); ?>" /></svg>
		</a>
	<?php endforeach; ?>
</div>
