<?php
/**
 * Template Name: Вхід
 *
 * @package MustSee_Travel
 */
defined( 'ABSPATH' ) || exit;

get_header();
?>
<div class="container-site py-16">
	<div class="mx-auto max-w-md rounded-2xl border border-gray-100 p-8 shadow-sm">
		<div class="flex items-baseline gap-4">
			<h1 class="ty-h4-caps text-brand">Вхід</h1>
			<a href="<?php echo esc_url( home_url( '/register/' ) ); ?>" class="ty-text-bold text-gray-400 hover:text-brand">Реєстрація агенства</a>
		</div>
		<form data-form="login" class="mt-6 space-y-4">
			<div style="position:absolute;left:-9999px;" aria-hidden="true">
				<label>Не заповнюйте це поле<input type="text" name="company" tabindex="-1" autocomplete="off" /></label>
			</div>
			<label class="flex flex-col gap-1.5">
				<span class="ty-text-bold text-gray-800">Логін або e-mail</span>
				<input type="text" name="log" required autocomplete="username" placeholder="Ваш логін" class="field" />
			</label>
			<label class="flex flex-col gap-1.5">
				<span class="ty-text-bold text-gray-800">Пароль</span>
				<input type="password" name="pwd" required autocomplete="current-password" placeholder="••••••••" class="field" />
			</label>
			<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>" class="ty-link block text-brand">Забули пароль?</a>
			<button type="submit" class="btn-accent w-full">Увійти</button>
			<p data-form-msg class="ty-13 text-center text-red-500" role="status" aria-live="polite"></p>
		</form>
	</div>
</div>
<?php
get_footer();
