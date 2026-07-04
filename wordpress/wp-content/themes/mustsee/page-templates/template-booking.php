<?php
/**
 * Template Name: Бронювання туру
 *
 * @package MustSee_Travel
 */
defined( 'ABSPATH' ) || exit;

get_header();

$tour_slug = isset( $_GET['tour'] ) ? sanitize_title( wp_unslash( $_GET['tour'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$tour      = $tour_slug ? mustsee_tour( $tour_slug ) : null;
$tour_name = $tour['title'] ?? 'Вікенд у Будапешт + Відень';
$price     = $tour ? mustsee_tour_price( $tour ) : '99 EUR';

$steps = array( 'Місто виїзду', 'Вибір місць', 'Дані туристів' );

// Demo rows shown when the API has no departures for the tour.
$departures = array(
	array( 'flight' => 'TK000003', 'from' => 'Львів, Двірцева площа 1', 'dep' => '17:00 19.07', 'to' => 'Будапешт', 'arr' => '08:00 19.07', 'extra' => '—' ),
	array( 'flight' => 'TK000003', 'from' => 'Стрий, Двірцева площа 1', 'dep' => '17:00 19.07', 'to' => 'Будапешт', 'arr' => '08:00 19.07', 'extra' => '+15 EUR' ),
	array( 'flight' => 'TK000003', 'from' => 'Мукачево, автовокзал', 'dep' => '16:30 19.07', 'to' => 'Будапешт', 'arr' => '08:00 19.07', 'extra' => '+10 EUR' ),
);
if ( ! empty( $tour['departures'] ) ) {
	$departures = array();
	foreach ( (array) $tour['departures'] as $i => $d ) {
		$departures[] = array(
			'flight' => sprintf( 'MS%06d', $i + 1 ),
			'from'   => $tour['departure_city'] ?? '',
			'dep'    => $d['date'] ?? '',
			'to'     => $tour['places'] ?? '',
			'arr'    => '',
			'extra'  => $d['price'] ?? '—',
		);
	}
}
$accommodation = array(
	array( 'type' => 'Базовий', 'price' => '99 EUR', 'hotel' => 'Готель 3-4*', 'room' => 'Підселення', 'meal' => 'BB — bed & breakfast' ),
	array( 'type' => 'Базовий', 'price' => '90 EUR', 'hotel' => 'Готель 3-4*', 'room' => 'Single', 'meal' => 'BB — bed & breakfast' ),
);
$services = array(
	array( 'name' => 'Туристичне страхування', 'adult' => '✓', 'child' => '✓', 'entry' => '—' ),
	array( 'name' => 'Оглядова прогулянка', 'adult' => '✓', 'child' => '✓', 'entry' => '—' ),
);
$unavailable = array( 5, 9, 22, 33, 41 );
$rows        = array( range( 1, 13 ), range( 14, 26 ), range( 27, 39 ), range( 40, 52 ) );
?>
<div class="container-site py-10">
	<h1 class="ty-h1-sm text-gray-900">Бронювання туру «<?php echo esc_html( $tour_name ); ?>»</h1>

	<div class="mt-6" data-booking data-tour="<?php echo esc_attr( $tour_slug ); ?>">
		<div class="flex flex-wrap gap-2">
			<?php foreach ( $steps as $i => $s ) : ?>
				<div data-step-chip class="ty-menu-vkladky rounded-full px-4 py-2 <?php echo 0 === $i ? 'bg-brand text-white' : 'bg-gray-100 text-gray-500'; ?>"><?php echo (int) ( $i + 1 ); ?>. <?php echo esc_html( $s ); ?></div>
			<?php endforeach; ?>
		</div>

		<div class="mt-6">
			<div data-step-panel class="space-y-8">
				<div class="flex flex-wrap gap-6 rounded-2xl bg-brand-50 p-5">
					<label class="flex flex-col gap-1.5"><span class="ty-text text-gray-600">К-сть дорослих</span><input type="number" min="1" value="1" class="field w-28" /></label>
					<label class="flex flex-col gap-1.5"><span class="ty-text text-gray-600">К-сть дітей</span><input type="number" min="0" value="0" class="field w-28" /></label>
				</div>

				<div>
					<h2 class="ty-h4-caps text-gray-800">Місто виїзду</h2>
					<div class="mt-3 overflow-x-auto rounded-2xl border border-gray-100">
						<table class="w-full text-left">
							<thead class="bg-brand-300 text-white"><tr class="ty-13"><th class="px-4 py-3 font-semibold">№ рейсу</th><th class="px-4 py-3 font-semibold">Відправлення</th><th class="px-4 py-3 font-semibold">Прибуття</th><th class="px-4 py-3 font-semibold">Доплата</th><th class="px-4 py-3 font-semibold"> </th></tr></thead>
							<tbody class="ty-13 text-gray-700">
								<?php foreach ( $departures as $i => $d ) : ?>
									<tr class="border-t border-gray-100">
										<td class="px-4 py-3 font-semibold"><?php echo esc_html( $d['flight'] ); ?></td>
										<td class="px-4 py-3"><?php echo esc_html( $d['from'] ); ?><br><?php echo esc_html( $d['dep'] ); ?></td>
										<td class="px-4 py-3 font-semibold"><?php echo esc_html( $d['to'] ); ?><br><?php echo esc_html( $d['arr'] ); ?></td>
										<td class="px-4 py-3"><?php echo esc_html( $d['extra'] ); ?></td>
										<td class="px-4 py-3"><input type="radio" name="dep" value="<?php echo esc_attr( $d['from'] . ' — ' . $d['dep'] . ( '—' !== $d['extra'] ? ' (' . $d['extra'] . ')' : '' ) ); ?>" <?php checked( 0, $i ); ?> class="accent-accent" /></td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				</div>

				<div>
					<h2 class="ty-h4-caps text-gray-800">Тип розміщення</h2>
					<div class="mt-3 overflow-x-auto rounded-2xl border border-gray-100">
						<table class="w-full text-left">
							<thead class="bg-brand-300 text-white"><tr class="ty-13"><th class="px-4 py-3 font-semibold">Тип туру</th><th class="px-4 py-3 font-semibold">Ціна</th><th class="px-4 py-3 font-semibold">Тип готелю</th><th class="px-4 py-3 font-semibold">Тип номеру</th><th class="px-4 py-3 font-semibold">Харчування</th><th class="px-4 py-3 font-semibold"> </th></tr></thead>
							<tbody class="ty-13 text-gray-700">
								<?php foreach ( $accommodation as $i => $a ) : ?>
									<tr class="border-t border-gray-100">
										<td class="px-4 py-3 font-semibold"><?php echo esc_html( $a['type'] ); ?></td>
										<td class="px-4 py-3 font-semibold text-accent"><?php echo esc_html( $a['price'] ); ?></td>
										<td class="px-4 py-3"><?php echo esc_html( $a['hotel'] ); ?></td>
										<td class="px-4 py-3"><?php echo esc_html( $a['room'] ); ?></td>
										<td class="px-4 py-3"><?php echo esc_html( $a['meal'] ); ?></td>
										<td class="px-4 py-3"><input type="radio" name="room" value="<?php echo esc_attr( $a['type'] . ', ' . $a['room'] . ' — ' . $a['price'] ); ?>" <?php checked( 0, $i ); ?> class="accent-accent" /></td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				</div>

				<div>
					<h2 class="ty-h4-caps text-gray-800">Додаткові послуги</h2>
					<div class="mt-3 overflow-x-auto rounded-2xl border border-gray-100">
						<table class="w-full text-left">
							<thead class="bg-brand-300 text-white"><tr class="ty-13"><th class="px-4 py-3 font-semibold">Вкл.</th><th class="px-4 py-3 font-semibold">Найменування</th><th class="px-4 py-3 font-semibold">Дорослий</th><th class="px-4 py-3 font-semibold">Дитина</th><th class="px-4 py-3 font-semibold">Вхідний квиток</th></tr></thead>
							<tbody class="ty-13 text-gray-700">
								<?php foreach ( $services as $s ) : ?>
									<tr class="border-t border-gray-100">
										<td class="px-4 py-3 text-brand"><?php echo esc_html( $s['adult'] ); ?></td>
										<td class="px-4 py-3 font-semibold"><?php echo esc_html( $s['name'] ); ?></td>
										<td class="px-4 py-3"><?php echo esc_html( $s['adult'] ); ?></td>
										<td class="px-4 py-3"><?php echo esc_html( $s['child'] ); ?></td>
										<td class="px-4 py-3"><?php echo esc_html( $s['entry'] ); ?></td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>

			<div data-step-panel class="hidden">
				<p class="ty-13 text-gray-500">Місця у перших рядах автобуса (1–16) — доплата 20 EUR / особа.</p>
				<h2 class="ty-h4-caps mt-3 text-gray-800">Вибір місць</h2>
				<div class="mt-4 overflow-x-auto rounded-2xl bg-brand-50 p-5">
					<div class="flex items-center gap-4">
						<svg aria-hidden="true" viewBox="0 0 24 24" class="h-10 w-10 shrink-0 fill-none stroke-gray-400" stroke-width="1.5"><circle cx="12" cy="12" r="9" /><circle cx="12" cy="12" r="2.5" /></svg>
						<div class="space-y-2">
							<?php foreach ( $rows as $r => $seats ) : ?>
								<div class="flex gap-2 <?php echo 2 === $r ? 'mt-4' : ''; ?>">
									<?php
									foreach ( $seats as $n ) :
										$off = in_array( $n, $unavailable, true );
										?>
										<button type="button" data-seat="<?php echo (int) $n; ?>" <?php disabled( $off ); ?> class="ty-13 flex h-9 w-9 shrink-0 items-center justify-center rounded-md border <?php echo $off ? 'cursor-not-allowed border-gray-200 bg-gray-200 text-gray-400' : 'border-gray-300 bg-white text-gray-700 hover:border-brand'; ?>"><?php echo (int) $n; ?></button>
									<?php endforeach; ?>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
				</div>
				<div class="mt-4 flex flex-wrap gap-5">
					<span class="ty-13 flex items-center gap-2 text-gray-600"><span class="h-4 w-4 rounded border border-gray-300 bg-white"></span> Доступне</span>
					<span class="ty-13 flex items-center gap-2 text-gray-600"><span class="h-4 w-4 rounded bg-gray-200"></span> Не доступне</span>
					<span class="ty-13 flex items-center gap-2 text-gray-600"><span class="h-4 w-4 rounded bg-brand"></span> Вибране</span>
				</div>
				<p class="ty-text mt-4 text-gray-600">Обрано місць: <span class="font-semibold text-brand" data-seat-count>0</span></p>
			</div>

			<div data-step-panel class="hidden">
				<h2 class="ty-h4-caps text-gray-800">Дані туристів</h2>
				<p class="ty-text mt-3 text-gray-500" data-tourist-empty>Спершу оберіть місця на попередньому кроці.</p>
				<div class="mt-4 space-y-6" data-tourist-forms></div>
				<template data-tourist-template>
					<div class="rounded-2xl bg-brand-50 p-5">
						<div class="flex flex-wrap items-center gap-5">
							<label class="ty-text flex items-center gap-2 text-gray-700"><input type="radio" name="type-__SEAT__" checked class="accent-accent" /> Дорослий</label>
							<label class="ty-text flex items-center gap-2 text-gray-700"><input type="radio" name="type-__SEAT__" class="accent-accent" /> Дитина</label>
							<span class="ty-text-bold text-brand">Місце __SEAT__</span>
						</div>
						<div class="mt-4 grid gap-4 sm:grid-cols-2">
							<label class="flex flex-col gap-1.5"><span class="ty-text-bold text-gray-800">Прізвище*</span><input type="text" class="field" /></label>
							<label class="flex flex-col gap-1.5"><span class="ty-text-bold text-gray-800">Ім'я*</span><input type="text" class="field" /></label>
							<label class="flex flex-col gap-1.5"><span class="ty-text-bold text-gray-800">Дата народження*</span><input type="text" placeholder="дд.мм.рррр" class="field" /></label>
							<label class="flex flex-col gap-1.5"><span class="ty-text-bold text-gray-800">№ паспорта*</span><input type="text" class="field" /></label>
							<label class="flex flex-col gap-1.5"><span class="ty-text-bold text-gray-800">Срок дії паспорта*</span><input type="text" placeholder="дд.мм.рррр" class="field" /></label>
							<label class="flex flex-col gap-1.5"><span class="ty-text-bold text-gray-800">Телефон*</span><input type="tel" class="field" /></label>
							<label class="flex flex-col gap-1.5"><span class="ty-text-bold text-gray-800">E-mail*</span><input type="email" class="field" /></label>
							<label class="flex flex-col gap-1.5">
								<span class="ty-text-bold text-gray-800">Стать*</span>
								<div class="flex gap-5 py-2.5">
									<label class="ty-text flex items-center gap-2 text-gray-700"><input type="radio" name="sex-__SEAT__" checked class="accent-accent" /> Чоловіча</label>
									<label class="ty-text flex items-center gap-2 text-gray-700"><input type="radio" name="sex-__SEAT__" class="accent-accent" /> Жіноча</label>
								</div>
							</label>
							<label class="flex flex-col gap-1.5 sm:col-span-2"><span class="ty-text-bold text-gray-800">Примітка</span><textarea rows="3" class="field"></textarea></label>
						</div>
					</div>
				</template>
			</div>
		</div>

		<div class="mt-8 flex flex-col gap-4 border-t border-gray-100 pt-6 sm:flex-row sm:items-center sm:justify-between">
			<div>
				<p class="ty-text text-gray-700">Загальна вартість: <span class="font-semibold"><?php echo esc_html( $price ); ?></span> · Ваша комісія: <span class="font-semibold text-accent"><?php echo esc_html( (string) round( mustsee_commission_rate() * 100 ) ); ?>%</span></p>
				<p class="ty-text mt-1 text-brand" data-booking-msg aria-live="polite"></p>
			</div>
			<div class="flex gap-3">
				<button type="button" data-step-prev disabled class="btn-outline disabled:opacity-40">Назад</button>
				<button type="button" data-step-next class="btn-accent">Далі</button>
				<button type="button" data-step-submit class="btn-accent hidden">Забронювати</button>
			</div>
		</div>
	</div>
</div>
<?php
get_footer();
