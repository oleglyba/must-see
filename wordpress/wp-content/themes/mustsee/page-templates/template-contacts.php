<?php
/**
 * Template Name: Контакти
 *
 * @package MustSee_Travel
 */
defined( 'ABSPATH' ) || exit;

get_header();

$phones = array( '+38 (032) 232-88-01', '+38 (098) 027-76-02', '+38 (095) 051-37-03' );
$tabs   = array( 'ТОП ТИЖНЯ', 'РЕКОМЕНДАЦІЇ', 'ГАРЯЧІ ПРОПОЗИЦІЇ' );
?>
<div class="container-site py-10">
	<h1 class="ty-h1 text-gray-900">Контакти</h1>

	<div class="mt-6 grid gap-10 lg:grid-cols-[1fr_22rem]">
		<div class="space-y-6">
			<div>
				<h2 class="ty-h1-sm text-gray-900">Головний офіс</h2>
				<p class="ty-text mt-1 text-gray-500">Туроператор. Ліцензія АЕ № 00054559</p>
			</div>
			<div>
				<h3 class="ty-h4-caps text-accent">Контактні телефони</h3>
				<ul class="ty-text mt-2 space-y-1 text-gray-700">
					<?php foreach ( $phones as $p ) : ?>
						<li><a href="tel:<?php echo esc_attr( mustsee_tel( $p ) ); ?>" class="hover:text-brand"><?php echo esc_html( $p ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</div>
			<div>
				<h3 class="ty-h4-caps text-accent">Гаряча лінія (для туристів за кордоном)</h3>
				<p class="ty-text mt-2 text-gray-700"><a href="tel:+380630250104" class="hover:text-brand">+38 (063) 025-01-04</a></p>
			</div>
			<div>
				<h3 class="ty-h4-caps text-accent">Графік роботи</h3>
				<p class="ty-text mt-2 text-gray-600">Понеділок – П'ятниця: 10:00 – 19:00<br>Субота: 10:00 – 15:00</p>
			</div>
			<div>
				<p class="ty-text text-gray-800">Ми в соцмережах:</p>
				<?php get_template_part( 'template-parts/socials', null, array( 'class' => 'mt-2' ) ); ?>
			</div>
		</div>

		<div class="space-y-4">
			<?php foreach ( $tabs as $t ) : ?>
				<a href="<?php echo esc_url( home_url( '/tours/' ) ); ?>" class="media-placeholder relative flex h-24 items-center overflow-hidden rounded-2xl px-5 text-white">
					<span class="absolute inset-0 bg-brand/55"></span>
					<span class="ty-h4-caps relative"><?php echo esc_html( $t ); ?></span>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</div>

<?php
get_template_part( 'template-parts/lead-form' );
get_template_part( 'template-parts/newsletter' );
get_footer();
