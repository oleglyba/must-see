<?php
/**
 * Template Name: Кабінет
 *
 * @package MustSee_Travel
 */
defined( 'ABSPATH' ) || exit;

// Partner cabinet is private — send guests to the login page.
if ( ! is_user_logged_in() ) {
	wp_safe_redirect( home_url( '/login/' ) );
	exit;
}

get_header();

$nav   = mustsee_config( 'cabinet_nav' );
$stats = array(
	array( 'label' => 'К-ть турів за рік', 'value' => '20' ),
	array( 'label' => 'К-ть туристів за рік', 'value' => '154' ),
	array( 'label' => 'К-ть ануляцій за рік', 'value' => '5' ),
	array( 'label' => 'Комісія за рік, грн', 'value' => '205 456' ),
);
$requests = array(
	array( 'id' => '#10109', 'tour' => 'Карнавал емоцій', 'date' => '14.04.2026', 'status' => 'Підтверджено', 'sum' => '7 852 ₴' ),
	array( 'id' => '#10231', 'tour' => 'Замки Баварії', 'date' => '12.04.2026', 'status' => 'Не оплачено', 'sum' => '12 856 ₴' ),
	array( 'id' => '#10228', 'tour' => 'Морське Око', 'date' => '20.04.2026', 'status' => 'Оплачено', 'sum' => '6 856 ₴' ),
);
$filter_groups = array(
	array( 'title' => 'Тип заявки', 'items' => array( 'Актуальні', 'Всі', 'Не підтверджені', 'Попередні', 'Скасовані' ) ),
	array( 'title' => 'Статус оплати', 'items' => array( 'З простроченим платежем', 'Не оплачені', 'Оплачено', 'Переплачені', 'Частково сплачені' ) ),
	array( 'title' => 'Країна перебування', 'items' => array( 'Болгарія', 'Греція', 'Єгипет', 'Іспанія', 'Кіпр' ) ),
);
$announcements = array(
	array( 'href' => '#', 'date' => '11 / 01 / 2026', 'title' => 'Зміна візового законодавства' ),
	array( 'href' => '#', 'date' => '11 / 01 / 2026', 'title' => 'Карантин' ),
	array( 'href' => '#', 'date' => '11 / 01 / 2026', 'title' => 'Важливо врахувати!' ),
);
?>
<div class="container-site py-10">
	<h1 class="ty-h1 text-gray-900">Кабінет</h1>

	<div class="mt-6 flex flex-col gap-8 lg:flex-row" data-tabs data-tab-active="text-accent" data-tab-inactive="text-brand hover:bg-gray-50">
		<aside class="w-full shrink-0 lg:w-56">
			<select data-tab-select class="field w-full lg:hidden">
				<?php foreach ( $nav as $i => $n ) : ?><option value="<?php echo (int) $i; ?>"><?php echo esc_html( $n ); ?></option><?php endforeach; ?>
			</select>
			<nav class="hidden flex-col gap-1 lg:flex">
				<?php foreach ( $nav as $i => $n ) : ?>
					<button type="button" data-tab-btn="<?php echo (int) $i; ?>" class="ty-menu-vkladky rounded-lg px-3 py-2 text-left transition"><?php echo esc_html( $n ); ?></button>
				<?php endforeach; ?>
			</nav>
		</aside>

		<div class="min-w-0 flex-1">
			<div data-tab-panel="0">
				<div class="space-y-8">
					<div>
						<h2 class="ty-h6 text-gray-900">ОЛЕНА КУПРІЄНКО</h2>
						<dl class="ty-text mt-2 space-y-1 text-gray-700">
							<p><span class="font-semibold">Посада:</span> менеджер</p>
							<p><span class="font-semibold">Агенція:</span> ФОП Назаренко С.М.</p>
							<p><span class="font-semibold">Договір:</span> 98451 від 25.01.26</p>
							<p><span class="font-semibold">Куратор:</span> Притула Валентина</p>
						</dl>
					</div>
					<div>
						<h3 class="ty-h2 text-brand">Ваша статистика:</h3>
						<div class="mt-4 grid grid-cols-2 gap-4 lg:grid-cols-4">
							<?php foreach ( $stats as $s ) : ?>
								<div class="rounded-2xl bg-gray-50 p-4">
									<p class="ty-text text-gray-600"><?php echo esc_html( $s['label'] ); ?></p>
									<p class="ty-h2 mt-1 text-gray-900"><?php echo esc_html( $s['value'] ); ?></p>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
				</div>
			</div>

			<div data-tab-panel="1" class="hidden">
				<div class="grid gap-6 rounded-2xl bg-brand-50 p-5 sm:grid-cols-3">
					<?php foreach ( $filter_groups as $g ) : ?>
						<div>
							<p class="ty-text-bold text-gray-800"><?php echo esc_html( $g['title'] ); ?></p>
							<ul class="mt-2 space-y-1.5">
								<?php foreach ( $g['items'] as $it ) : ?>
									<li><label class="ty-text flex items-center gap-2 text-gray-700"><input type="checkbox" class="accent-accent" /><?php echo esc_html( $it ); ?></label></li>
								<?php endforeach; ?>
							</ul>
						</div>
					<?php endforeach; ?>
				</div>
				<button type="button" class="btn-accent mt-5">Шукати</button>
				<div class="mt-6 overflow-x-auto rounded-2xl border border-gray-100">
					<table class="w-full text-left">
						<thead class="bg-brand-300 text-white"><tr class="ty-text-bold"><th class="px-4 py-3">№ заявки</th><th class="px-4 py-3">Тур</th><th class="px-4 py-3">Дата</th><th class="px-4 py-3">Статус</th><th class="px-4 py-3">Сума</th></tr></thead>
						<tbody class="ty-text text-gray-700">
							<?php foreach ( $requests as $r ) : ?>
								<tr class="border-t border-gray-100">
									<td class="px-4 py-3"><?php echo esc_html( $r['id'] ); ?></td>
									<td class="px-4 py-3"><?php echo esc_html( $r['tour'] ); ?></td>
									<td class="px-4 py-3"><?php echo esc_html( $r['date'] ); ?></td>
									<td class="px-4 py-3"><?php echo esc_html( $r['status'] ); ?></td>
									<td class="px-4 py-3 font-semibold text-accent"><?php echo esc_html( $r['sum'] ); ?></td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>

			<div data-tab-panel="2" class="hidden">
				<h2 class="ty-h1-sm text-gray-900">Агенція</h2>
				<div class="mt-4"><?php get_template_part( 'template-parts/agency-account-form' ); ?></div>
				<button type="button" class="btn-accent mt-6">Зберегти</button>
			</div>

			<div data-tab-panel="3" class="hidden">
				<h2 class="ty-h6 text-gray-900">Куратор</h2>
				<p class="ty-h7 mt-2 text-gray-900">Притула Валентина</p>
				<dl class="ty-text mt-2 space-y-1 text-gray-700">
					<p><span class="font-semibold">Моб:</span> <a href="tel:+380508899666" class="hover:text-brand">38-050-88-99-66</a></p>
					<p><span class="font-semibold">E-mail:</span> <a href="mailto:valentyna@mustsee.travel" class="hover:text-brand">valentyna@mustsee.travel</a></p>
					<p><span class="font-semibold">Чат:</span> <a href="https://t.me/curator" class="hover:text-brand">t.me/curator</a></p>
				</dl>
			</div>

			<?php for ( $i = 4; $i < count( $nav ); $i++ ) : ?>
				<div data-tab-panel="<?php echo (int) $i; ?>" class="hidden">
					<p class="ty-text text-gray-500">Розділ «<?php echo esc_html( $nav[ $i ] ); ?>» — у розробці.</p>
				</div>
			<?php endfor; ?>

			<div class="mt-10">
				<h3 class="ty-h2 text-center text-brand">Оголошення</h3>
				<div class="mt-4 grid gap-5 md:grid-cols-3">
					<?php foreach ( $announcements as $a ) { get_template_part( 'template-parts/article-card', null, $a ); } ?>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
get_footer();
