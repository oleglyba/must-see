<?php
/**
 * Newsletter subscribe block (AJAX → mustsee_newsletter).
 */
defined( 'ABSPATH' ) || exit;
?>
<div class="container-site py-10">
	<div class="rounded-2xl bg-gray-50 p-8 text-center md:p-12">
		<h2 class="ty-h2 text-brand">Хочете першими дізнаватися про знижки?</h2>
		<form data-form="newsletter" class="mx-auto mt-6 max-w-xl">
			<div style="position:absolute;left:-9999px;" aria-hidden="true">
				<label>Не заповнюйте це поле<input type="text" name="company" tabindex="-1" autocomplete="off" /></label>
			</div>
			<div class="flex flex-wrap justify-center gap-3">
				<input name="email" type="email" required aria-label="Ваш E-mail" placeholder="Ваш E-mail" class="field min-w-[220px] flex-1" />
				<button type="submit" class="btn-accent">Підписатись</button>
			</div>
			<label class="ty-13 mt-3 flex items-start gap-2 text-left text-gray-500">
				<input name="consent" type="checkbox" value="1" class="mt-0.5" />
				Погоджуюсь з умовами підписки і даю згоду на отримання маркетингових пропозицій від Must See Travel
			</label>
			<p data-form-msg class="ty-13 mt-2 text-gray-700" role="status" aria-live="polite"></p>
		</form>
	</div>
</div>
