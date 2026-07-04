<?php
/**
 * Lead form banner (AJAX → mustsee_lead).
 */
defined( 'ABSPATH' ) || exit;
?>
<div class="container-site py-8">
	<div class="banner-brand relative">
		<div class="max-w-2xl">
			<h2 class="ty-h1 text-white">Хочете підібрати тур швидко та без зайвого клопоту?</h2>
			<p class="ty-text mt-3 text-white/90">Залиште заявку і вам допоможе один з наших експертів!</p>
			<form data-form="lead" class="mt-6 flex flex-wrap items-start gap-3">
				<div style="position:absolute;left:-9999px;" aria-hidden="true">
					<label>Не заповнюйте це поле<input type="text" name="company" tabindex="-1" autocomplete="off" /></label>
				</div>
				<div class="min-w-[180px] flex-1">
					<input name="name" required aria-label="Ваше ім'я" placeholder="Ваше ім'я" class="ty-text w-full rounded-lg bg-white px-4 py-3 text-gray-800 outline-none" />
				</div>
				<div class="min-w-[180px] flex-1">
					<input name="phone" type="tel" inputmode="tel" required aria-label="Телефон" placeholder="+380 (__) ___ __ __" class="ty-text w-full rounded-lg bg-white px-4 py-3 text-gray-800 outline-none" />
				</div>
				<button type="submit" class="btn-accent">Відправити заявку</button>
				<p data-form-msg class="ty-13 mt-1 w-full text-white" role="status" aria-live="polite"></p>
			</form>
		</div>
	</div>
</div>
