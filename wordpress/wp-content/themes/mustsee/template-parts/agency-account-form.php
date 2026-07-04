<?php
/**
 * Agency account form — four field groups (used by register + cabinet).
 */
defined( 'ABSPATH' ) || exit;

$agency_account = array(
	'Повна юридична назва (ФОП…; ТОВ…)', 'Комерційна назва',
	'Юридична адреса', 'Адреса офісу (якщо без офісу — ONLINE)',
	'Код ЄДРПОУ', 'Система оподаткування компанії',
	'ПІП директора / ФОП', 'Телефон директора',
	'E-mail', 'Telegram (нікнейм)',
	'Web (за наявності)', 'Instagram агенції',
);
$bank_requisites = array( 'IBAN', 'Юридична назва банку' );
$bank_guarantee  = array( 'Номер та дата банківської гарантії', 'Юридична назва банку' );
$agent_account   = array(
	'E-mail представника агенції (логін)', 'Комерційна назва',
	'Особистий телефон представника', 'ПІБ представника агенції',
	'Посада представника агенції', 'Екстрений телефон (телеграм)',
	'Instagram',
);
?>
<div class="space-y-6">
	<?php
	get_template_part( 'template-parts/field-group', null, array( 'title' => 'Обліковий запис Агенції', 'fields' => $agency_account ) );
	get_template_part( 'template-parts/field-group', null, array( 'title' => 'Банківські реквізити', 'fields' => $bank_requisites ) );
	get_template_part( 'template-parts/field-group', null, array( 'title' => 'Банківська гарантія', 'fields' => $bank_guarantee ) );
	get_template_part( 'template-parts/field-group', null, array( 'title' => 'Обліковий запис Турагента', 'fields' => $agent_account ) );
	?>
</div>
