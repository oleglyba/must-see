<?php
/**
 * Template Name: Інформація
 *
 * @package MustSee_Travel
 */
defined( 'ABSPATH' ) || exit;

get_header();

$agent_sub = array( 'Умови співпраці', 'Умови бронювання', 'Умови оплати', 'Умови ануляції', 'Реквізити компанії' );
$sidebar   = array( 'Туристам', 'Вакансії', 'Політика конфіденційності', 'Договір публічної оферти', 'Подарунковий сертифікат', 'Блог', 'Карта сайту' );
?>
<div class="container-site py-10">
	<h1 class="ty-h1 text-gray-900">Інформація</h1>

	<div class="mt-6 flex flex-col gap-10 lg:flex-row">
		<aside class="w-full shrink-0 lg:w-64">
			<nav class="flex flex-col gap-2">
				<span class="ty-menu-vkladky text-accent">Агентам</span>
				<div class="flex flex-col gap-1.5 pl-4">
					<?php foreach ( $agent_sub as $s ) : ?>
						<a href="#" class="ty-text text-brand hover:text-accent"><?php echo esc_html( $s ); ?></a>
					<?php endforeach; ?>
				</div>
				<?php foreach ( $sidebar as $s ) : ?>
					<a href="#" class="ty-menu-vkladky text-brand hover:text-accent"><?php echo esc_html( $s ); ?></a>
				<?php endforeach; ?>
			</nav>
		</aside>

		<div class="min-w-0 flex-1">
			<h2 class="ty-h1-sm text-gray-900">Шановні партнери, турагенти!</h2>
			<div class="ty-text mt-4 space-y-3 text-gray-600">
				<p>Туроператор запрошує вас до співпраці з реалізації туристичних послуг нашої компанії.</p>
				<p>У цьому розділі ви знайдете всю необхідну інформацію для роботи: умови співпраці, бронювання, оплати та ануляції, а також реквізити компанії.</p>
			</div>
		</div>
	</div>
</div>

<?php
get_template_part( 'template-parts/lead-form' );
get_template_part( 'template-parts/newsletter' );
get_footer();
