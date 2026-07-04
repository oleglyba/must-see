<?php
/**
 * Single tour page, rendered from API data (routed via inc/routing.php).
 *
 * @package MustSee_Travel
 */
defined( 'ABSPATH' ) || exit;

$tour = mustsee_tour( (string) get_query_var( 'mustsee_tour' ) );
if ( ! $tour ) {
	get_header();
	get_template_part( '404' );
	get_footer();
	return;
}

$title       = $tour['title'] ?? '';
$price       = mustsee_tour_price( $tour );
$booking_url = esc_url( add_query_arg( 'tour', $tour['slug'], home_url( '/booking/' ) ) );
$images      = (array) ( $tour['images'] ?? array() );
$departures  = (array) ( $tour['departures'] ?? array() );
$program     = (array) ( $tour['program'] ?? array() );
$route       = (array) ( $tour['route'] ?? array() );
$tabs        = array( 'Опис', 'Програма', 'Калькулятор цін', 'Вартість для груп', 'Акції', 'Маршрут', 'Готелі', 'Галерея' );

$meta_rows = array();
foreach ( array(
	'Країни'     => implode( ', ', (array) ( $tour['countries'] ?? array() ) ),
	'Виїзд'      => $departures[0]['date'] ?? '',
	'Місце'      => $tour['places'] ?? '',
	'Тривалість' => $tour['days'] ?? '',
	'Місто'      => $tour['departure_city'] ?? '',
	'Зірки'      => $tour['stars'] ?? '',
) as $label => $v ) {
	if ( '' !== $v ) {
		$meta_rows[] = array( 'label' => $label, 'value' => $v );
	}
}

$price_fields = array(
	array( 'label' => 'Дата туру', 'value' => $departures[0]['date'] ?? '—' ),
	array( 'label' => 'К-сть дорослих', 'value' => '1' ),
	array( 'label' => 'К-сть дітей', 'value' => '0' ),
	array( 'label' => 'Кімнати', 'value' => 'Одномісний номер' ),
	array( 'label' => 'Тип харчування', 'value' => 'Сніданки' ),
	array( 'label' => 'Знижки та акції', 'value' => 'Раннє бронювання' ),
);
$group_rules = array(
	'Для груп понад 40 осіб необхідно писати індивідуальний запит.',
	'Керівник групи має право на безкоштовний тур та екскурсії.',
	'Окремо оплачуються вхідні квитки в туристичні об\'єкти.',
);
$promos = array(
	array( 'title' => 'Студентський квиток', 'text' => 'Студенти отримують знижку 10% від стандартної вартості туру (за пред\'явленням документа).' ),
	array( 'title' => 'Бронюй заздалегідь', 'text' => 'Бронюйте тур заздалегідь і отримайте знижку на ранні замовлення.' ),
	array( 'title' => 'Акція 3', 'text' => 'Спеціальна пропозиція — слідкуйте за оновленнями на сайті.' ),
);

$calc = function ( $value ) use ( $price_fields, $booking_url ) {
	ob_start(); ?>
	<div class="rounded-2xl border border-gray-100 p-5">
		<div class="grid gap-4 sm:grid-cols-2">
			<?php foreach ( $price_fields as $f ) : ?>
				<label class="flex flex-col gap-1.5"><span class="ty-text text-gray-500"><?php echo esc_html( $f['label'] ); ?></span><span class="field text-gray-800"><?php echo esc_html( $f['value'] ); ?></span></label>
			<?php endforeach; ?>
		</div>
		<div class="mt-5 flex flex-wrap items-center justify-between gap-4">
			<p class="ty-h6 text-accent">Вартість туру: <?php echo esc_html( $value ); ?></p>
			<a href="<?php echo $booking_url; ?>" class="btn-accent">Замовити тур</a>
		</div>
	</div>
	<?php return ob_get_clean();
};

get_header();
?>
<div class="container-site py-8">
	<?php
	get_template_part(
		'template-parts/breadcrumbs',
		null,
		array(
			'items'   => array( array( 'title' => 'Каталог', 'href' => '/tours/' ) ),
			'current' => $title,
		)
	);
	?>

	<h1 class="ty-h1-sm mt-3 text-gray-900">Тур «<?php echo esc_html( $title ); ?>»</h1>

	<div class="mt-6 grid gap-8 lg:grid-cols-[40%_1fr]">
		<div>
			<?php mustsee_tour_image( $tour, 'aspect-[4/3] rounded-2xl' ); ?>
			<?php if ( count( $images ) > 1 ) : ?>
				<div class="mt-3 grid grid-cols-4 gap-3">
					<?php foreach ( array_slice( $images, 1, 4 ) as $img ) : ?>
						<div class="relative aspect-square overflow-hidden rounded-xl"><img src="<?php echo esc_url( $img ); ?>" alt="" loading="lazy" class="absolute inset-0 h-full w-full object-cover" /></div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>

		<div>
			<?php if ( $meta_rows ) : ?>
				<dl class="space-y-2">
					<?php foreach ( $meta_rows as $m ) : ?>
						<div class="ty-text flex gap-2"><dt class="shrink-0 font-semibold text-gray-800"><?php echo esc_html( $m['label'] ); ?>:</dt><dd class="text-gray-600"><?php echo esc_html( $m['value'] ); ?></dd></div>
					<?php endforeach; ?>
				</dl>
			<?php endif; ?>

			<p class="ty-h2 mt-4 text-accent">Ціна: <?php echo esc_html( $price ); ?></p>

			<div class="mt-5 flex flex-wrap gap-3">
				<a href="<?php echo $booking_url; ?>" class="btn-accent">Замовити тур</a>
				<a href="#" class="btn-outline">Версія PDF</a>
			</div>

			<?php if ( $departures ) : ?>
				<div class="mt-6">
					<p class="ty-text-bold text-gray-800">Найближчі дати виїзду</p>
					<div class="mt-3 flex flex-wrap gap-3">
						<?php foreach ( $departures as $d ) : ?>
							<div class="rounded-xl border border-gray-200 px-4 py-3 text-center"><p class="ty-text text-gray-500"><?php echo esc_html( $d['date'] ?? '' ); ?></p><?php if ( ! empty( $d['price'] ) ) : ?><p class="ty-h7 text-brand"><?php echo esc_html( $d['price'] ); ?></p><?php endif; ?></div>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>

	<div class="mt-8" data-tabs data-tab-active="bg-brand text-white" data-tab-inactive="text-gray-600 hover:bg-gray-100">
		<div class="border-y border-gray-200 bg-gray-50 py-3">
			<select data-tab-select class="field w-full md:hidden">
				<?php foreach ( $tabs as $i => $t ) : ?><option value="<?php echo (int) $i; ?>"><?php echo esc_html( $t ); ?></option><?php endforeach; ?>
			</select>
			<div class="hidden gap-2 overflow-x-auto md:flex">
				<?php foreach ( $tabs as $i => $t ) : ?><button type="button" data-tab-btn="<?php echo (int) $i; ?>" class="ty-menu-vkladky shrink-0 rounded-full px-4 py-2 transition"><?php echo esc_html( $t ); ?></button><?php endforeach; ?>
			</div>
		</div>

		<div class="py-10">
			<section data-tab-panel="0">
				<h2 class="ty-h2 text-brand">Опис</h2>
				<div class="ty-text mt-4 max-w-none space-y-4 text-gray-600"><?php echo wp_kses_post( wpautop( $tour['description'] ?? '' ) ); ?></div>
			</section>

			<section data-tab-panel="1" class="hidden">
				<h2 class="ty-h2 text-brand">Програма туру</h2>
				<?php if ( $program ) : ?>
					<div class="mt-4 space-y-4">
						<?php foreach ( $program as $d ) : ?>
							<div class="rounded-2xl border border-gray-100 p-5">
								<h3 class="ty-h7 text-brand"><?php echo esc_html( $d['day'] ?? '' ); ?></h3>
								<ul class="ty-text mt-2 space-y-1 text-gray-600"><?php foreach ( (array) ( $d['items'] ?? array() ) as $it ) : ?><li><?php echo esc_html( $it ); ?></li><?php endforeach; ?></ul>
							</div>
						<?php endforeach; ?>
					</div>
				<?php else : ?>
					<p class="ty-text mt-4 text-gray-500">Програму буде додано найближчим часом.</p>
				<?php endif; ?>
			</section>

			<section data-tab-panel="2" class="hidden">
				<h2 class="ty-h2 text-brand">Калькулятор цін</h2>
				<div class="mt-4"><?php echo $calc( $price ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
			</section>

			<section data-tab-panel="3" class="hidden">
				<h2 class="ty-h2 text-brand">Вартість для груп</h2>
				<ul class="ty-text mt-4 list-disc space-y-1 pl-5 text-gray-600"><?php foreach ( $group_rules as $r ) : ?><li><?php echo esc_html( $r ); ?></li><?php endforeach; ?></ul>
				<div class="mt-4"><?php echo $calc( $price ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
			</section>

			<section data-tab-panel="4" class="hidden">
				<h2 class="ty-h2 text-brand">Діючі акції</h2>
				<p class="ty-text-bold mt-3 text-gray-700">Важливо: знижки по різних акціях не сумуються в одному турі.</p>
				<div class="mt-4 grid gap-4 md:grid-cols-3">
					<?php foreach ( $promos as $p ) : ?>
						<div class="rounded-2xl bg-accent-50 p-5"><h3 class="ty-h7 text-accent"><?php echo esc_html( $p['title'] ); ?></h3><p class="ty-text mt-2 text-gray-600"><?php echo esc_html( $p['text'] ); ?></p></div>
					<?php endforeach; ?>
				</div>
			</section>

			<section data-tab-panel="5" class="hidden">
				<h2 class="ty-h2 text-brand">Маршрут туру</h2>
				<?php if ( $route ) : ?>
					<p class="ty-text mt-3 text-gray-600"><span class="font-semibold">Міста:</span> <?php echo esc_html( implode( ', ', $route ) ); ?></p>
					<ol class="mt-4 space-y-2">
						<?php foreach ( $route as $i => $c ) : ?>
							<li class="ty-text flex items-center gap-3 text-gray-700"><span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-brand text-white"><?php echo (int) ( $i + 1 ); ?></span><?php echo esc_html( $c ); ?></li>
						<?php endforeach; ?>
					</ol>
				<?php else : ?>
					<p class="ty-text mt-4 text-gray-500">Маршрут уточнюється.</p>
				<?php endif; ?>
			</section>

			<section data-tab-panel="6" class="hidden">
				<h2 class="ty-h2 text-brand">Готелі</h2>
				<p class="ty-text mt-4 text-gray-500">Готелі за цим туром уточнюються.</p>
			</section>

			<section data-tab-panel="7" class="hidden">
				<h2 class="ty-h2 text-brand">Фотогалерея туру</h2>
				<?php if ( $images ) : ?>
					<div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
						<?php foreach ( $images as $img ) : ?>
							<div class="relative aspect-square overflow-hidden rounded-xl"><img src="<?php echo esc_url( $img ); ?>" alt="" loading="lazy" class="absolute inset-0 h-full w-full object-cover" /></div>
						<?php endforeach; ?>
					</div>
				<?php else : ?>
					<p class="ty-text mt-4 text-gray-500">Фотогалерея додається.</p>
				<?php endif; ?>
			</section>
		</div>
	</div>
</div>

<?php
get_template_part( 'template-parts/lead-form' );
get_template_part( 'template-parts/newsletter' );
get_footer();
