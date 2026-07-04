<?php
/**
 * Panel with a grid of labelled form fields.
 *
 * @var array $args { title: string, fields: string[] }
 */
defined( 'ABSPATH' ) || exit;
$title  = isset( $args['title'] ) ? $args['title'] : '';
$fields = isset( $args['fields'] ) ? $args['fields'] : array();
?>
<section class="rounded-2xl bg-brand-50 p-5">
	<?php if ( $title ) : ?><h2 class="ty-h2 text-brand"><?php echo esc_html( $title ); ?></h2><?php endif; ?>
	<div class="mt-4 grid gap-4 sm:grid-cols-2">
		<?php foreach ( $fields as $f ) : ?>
			<label class="flex flex-col gap-1.5">
				<span class="ty-text-bold text-gray-800"><?php echo esc_html( $f ); ?></span>
				<input type="text" name="field[<?php echo esc_attr( $f ); ?>]" aria-label="<?php echo esc_attr( $f ); ?>" placeholder="<?php echo esc_attr( $f ); ?>" class="field" />
			</label>
		<?php endforeach; ?>
	</div>
</section>
