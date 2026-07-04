<?php
/**
 * Theme footer.
 *
 * @package MustSee_Travel
 */
defined( 'ABSPATH' ) || exit;

$config_cols    = mustsee_config( 'footer_columns' );
$footer_columns = array(
	mustsee_menu_items( 'footer_1', $config_cols[0] ),
	mustsee_menu_items( 'footer_2', $config_cols[1] ),
	mustsee_menu_items( 'footer_3', $config_cols[2] ),
);
?>
</main>

<footer class="mt-12 bg-brand text-white">
	<div class="container-site py-10">
		<div class="grid gap-8 md:grid-cols-[1.4fr_1fr_1fr_1fr]">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="Must See Travel" class="shrink-0">
				<?php mustsee_logo( 'h-9 w-auto' ); ?>
			</a>
			<?php foreach ( $footer_columns as $col ) : ?>
				<nav class="flex flex-col gap-2">
					<?php foreach ( $col as $l ) : ?>
						<a href="<?php echo esc_url( mustsee_url( $l['href'] ) ); ?>" class="ty-menu-2 text-white/85 hover:text-white"><?php echo esc_html( $l['title'] ); ?></a>
					<?php endforeach; ?>
				</nav>
			<?php endforeach; ?>
		</div>

		<div class="mt-8 flex flex-col items-center justify-between gap-4 border-t border-white/15 pt-6 md:flex-row">
			<p class="ty-copyright text-white/70">&copy;Must See Travel, <?php echo esc_html( gmdate( 'Y' ) ); ?>. All Rights Reserved.</p>
			<div class="flex items-center gap-3">
				<span class="ty-text text-white/85">Ми в соцмережах:</span>
				<?php get_template_part( 'template-parts/socials', null, array( 'dot' => 'bg-white/15', 'icon' => 'text-white' ) ); ?>
			</div>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
