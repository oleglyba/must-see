<?php
/**
 * Template Name: Реєстрація партнера
 *
 * @package MustSee_Travel
 */
defined( 'ABSPATH' ) || exit;

get_header();

$tabs      = array( 'Нова реєстрація (Агенція)', 'Реєстрація представника (Турагент)' );
$rep_agent = array(
	'E-mail представника агенції (логін)', 'Комерційна назва',
	'ПІБ представника агенції', 'Посада представника агенції',
	'Екстрений телефон (телеграм)', 'Instagram',
);
$rep_agency = array( 'Повна юридична назва (ФОП…; ТОВ…)', 'Номер договору' );
?>
<div class="container-site py-10">
	<h1 class="ty-h1 text-gray-900">Реєстрація партнера</h1>

	<div class="mt-6" data-tabs data-tab-active="bg-brand text-white" data-tab-inactive="text-gray-600 hover:bg-gray-100">
		<select data-tab-select class="field w-full md:hidden">
			<?php foreach ( $tabs as $i => $t ) : ?><option value="<?php echo (int) $i; ?>"><?php echo esc_html( $t ); ?></option><?php endforeach; ?>
		</select>
		<div class="hidden gap-2 overflow-x-auto md:flex">
			<?php foreach ( $tabs as $i => $t ) : ?>
				<button type="button" data-tab-btn="<?php echo (int) $i; ?>" class="ty-menu-vkladky shrink-0 rounded-full px-4 py-2 transition"><?php echo esc_html( $t ); ?></button>
			<?php endforeach; ?>
		</div>

		<form data-form="register" class="mt-6 space-y-6">
			<div style="position:absolute;left:-9999px;" aria-hidden="true">
				<label>Не заповнюйте це поле<input type="text" name="company" tabindex="-1" autocomplete="off" /></label>
			</div>
			<div data-tab-panel="0">
				<?php get_template_part( 'template-parts/agency-account-form' ); ?>
			</div>
			<div data-tab-panel="1" class="hidden space-y-6">
				<?php
				get_template_part( 'template-parts/field-group', null, array( 'title' => 'Обліковий запис Турагента (представника Агенції)', 'fields' => $rep_agent ) );
				get_template_part( 'template-parts/field-group', null, array( 'title' => 'Обліковий запис Агенції', 'fields' => $rep_agency ) );
				?>
			</div>

			<div class="flex flex-col items-end gap-3">
				<button type="submit" class="btn-accent">Зареєструватись</button>
				<p data-form-msg class="ty-13 text-brand" role="status" aria-live="polite"></p>
			</div>
		</form>
	</div>
</div>
<?php
get_footer();
