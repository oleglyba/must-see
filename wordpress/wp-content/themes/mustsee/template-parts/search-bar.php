<?php
/**
 * Tour search bar.
 */
defined( 'ABSPATH' ) || exit;
$fields = array(
	array( 'label' => 'Куди', 'placeholder' => 'Напрямок, країна', 'name' => 'country' ),
	array( 'label' => 'Підсадка', 'placeholder' => 'Місто виїзду' ),
	array( 'label' => 'Дата виїзду', 'placeholder' => 'дд.мм.рррр' ),
	array( 'label' => 'Тривалість', 'placeholder' => 'будь-яка' ),
);
?>
<div class="container-site py-8">
	<h2 class="ty-h2 text-center text-brand">Пошук туру</h2>
	<form method="get" class="mt-6 flex flex-wrap items-end gap-4 rounded-2xl bg-white p-5 shadow-[0_0.5rem_2rem_rgba(48,101,207,0.10)]" action="<?php echo esc_url( home_url( '/tours/' ) ); ?>">
		<?php foreach ( $fields as $f ) : ?>
			<label class="flex min-w-[180px] flex-1 flex-col gap-1.5">
				<span class="ty-text text-gray-500"><?php echo esc_html( $f['label'] ); ?></span>
				<input <?php echo isset( $f['name'] ) ? 'name="' . esc_attr( $f['name'] ) . '"' : ''; ?> aria-label="<?php echo esc_attr( $f['label'] ); ?>" placeholder="<?php echo esc_attr( $f['placeholder'] ); ?>" class="field" />
			</label>
		<?php endforeach; ?>
		<label class="flex flex-col gap-1.5">
			<span class="ty-text text-gray-500">Осіб</span>
			<input type="number" min="1" value="1" aria-label="Кількість осіб" class="field w-20" />
		</label>
		<button type="submit" class="btn-accent">Знайти</button>
	</form>
	<div class="mt-3 text-center">
		<a href="<?php echo esc_url( home_url( '/tours/' ) ); ?>" class="ty-link text-brand">Розширений пошук</a>
	</div>
</div>
