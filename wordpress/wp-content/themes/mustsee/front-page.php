<?php
/**
 * Front page — Must See Travel home (tour data from the API).
 *
 * @package MustSee_Travel
 */
defined( 'ABSPATH' ) || exit;

get_header();

$featured_tabs = array(
	array( 'label' => 'ТОП ТИЖНЯ', 'href' => '/tours/' ),
	array( 'label' => 'РЕКОМЕНДАЦІЇ', 'href' => '/tours/' ),
	array( 'label' => 'ГАРЯЧІ ПРОПОЗИЦІЇ', 'href' => '/tours/' ),
);
$city_fallback = array_map(
	function ( $c ) {
		return array( 'title' => $c, 'href' => '/tours/' );
	},
	array( 'Стамбул', 'Прага', 'Барселона', 'Рим', 'Будапешт', 'Краків', 'Відень', 'Венеція', 'Мюнхен', 'Афіни', 'Дубровник', 'Париж' )
);
$cities      = mustsee_menu_items( 'cities', $city_fallback );
$cert_url    = home_url( '/info/' );
$tours_url   = home_url( '/tours/' );
$tour_terms  = mustsee_tour_categories();
$seo_columns = array(
	array(
		'title' => 'Чому обирають наші автобусні тури',
		'body'  => 'Наш туроператор створює автобусні тури з урахуванням реальних потреб туристів: зручні переїзди, оптимальні маршрути, комфортні автобуси, професійні водії та досвідчені гіди.',
	),
	array(
		'title' => 'Напрямки автобусних турів',
		'body'  => 'Ми пропонуємо автобусні тури в найпопулярніші напрямки: європейські країни, історичні міста, морські курорти, гірські регіони та екскурсійні маршрути.',
		'list'  => array( 'автобусні тури по Європі', 'автобусні тури Україною', 'тури вихідного дня', 'екскурсійні автобусні тури', 'багатоденні автобусні подорожі', 'комбіновані тури' ),
	),
	array(
		'title' => 'Переваги роботи з нами',
		'body'  => 'Обираючи нас, ви отримуєте:',
		'list'  => array( 'професійну організацію автобусних турів', 'офіційного туроператора', 'ліцензовані перевезення', 'сучасні туристичні автобуси', 'страхування туристів', 'підтримку клієнтів 24/7', 'прозорі ціни без прихованих доплат', 'гарантію якості туристичних послуг' ),
	),
);
?>

<?php get_template_part( 'template-parts/hero' ); ?>
<?php get_template_part( 'template-parts/search-bar' ); ?>

<div class="container-site py-8">
	<div class="banner-brand">
		<p class="ty-h4-caps text-white/80">Подарунковий сертифікат</p>
		<h2 class="ty-h1 mt-2 text-white">Подарунковий Сертифікат</h2>
		<p class="ty-text mt-4 max-w-3xl text-white/90">Хочете отримати подарунковий сертифікат або знижку? Це доступно лише для підписників нашого Telegram-каналу! Завантажуйте сертифікат, обирайте тур — і вирушайте у подорож. Кількість сертифікатів обмежена.</p>
		<div class="mt-6 flex flex-wrap items-center gap-4">
			<span class="ty-h6 rounded-xl bg-white/15 px-4 py-2">500 / 1000 / 2000 / 3000 ₴</span>
			<a href="<?php echo esc_url( $cert_url ); ?>" class="btn-accent">Завантажити сертифікат</a>
		</div>
	</div>
</div>

<div class="container-site py-6">
	<div class="grid gap-4 md:grid-cols-3">
		<?php foreach ( $featured_tabs as $t ) : ?>
			<a href="<?php echo esc_url( mustsee_url( $t['href'] ) ); ?>" class="media-placeholder relative flex h-28 items-center justify-center overflow-hidden rounded-2xl text-white transition hover:opacity-95">
				<span class="absolute inset-0 bg-brand/55"></span>
				<span class="ty-h4-caps relative"><?php echo esc_html( $t['label'] ); ?></span>
				<span class="ty-text absolute bottom-2 right-3 text-white/85">переглянути всі →</span>
			</a>
		<?php endforeach; ?>
	</div>
</div>

<?php
$nearest = mustsee_tours( array( 'featured' => 1, 'per_page' => 4 ) )['items'];
if ( ! $nearest ) {
	$nearest = mustsee_tours( array( 'per_page' => 4 ) )['items'];
}
if ( $nearest ) : ?>
<section>
	<div class="container-site py-10">
		<h2 class="ty-h2 text-center text-brand">Найближчі виїзди</h2>
		<div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
			<?php foreach ( $nearest as $tour ) { mustsee_tour_card( $tour ); } ?>
		</div>
		<div class="mt-8 text-center"><a href="<?php echo esc_url( $tours_url ); ?>" class="btn-accent">Дивитись всі</a></div>
	</div>
</section>
<?php endif; ?>

<section class="bg-gray-50">
	<div class="container-site py-10">
		<h2 class="ty-h2 text-center text-brand">Популярні міста</h2>
		<div class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">
			<?php foreach ( $cities as $c ) : ?>
				<a href="<?php echo esc_url( mustsee_url( $c['href'] ) ); ?>" class="ty-text flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-3 text-gray-700 transition hover:border-brand">
					<svg aria-hidden="true" viewBox="0 0 24 24" class="h-5 w-5 shrink-0 fill-current text-brand"><path d="M12 2a7 7 0 0 0-7 7c0 5 7 13 7 13s7-8 7-13a7 7 0 0 0-7-7Zm0 9.5A2.5 2.5 0 1 1 12 6a2.5 2.5 0 0 1 0 5.5Z" /></svg>
					<?php echo esc_html( $c['title'] ); ?>
				</a>
			<?php endforeach; ?>
		</div>
		<div class="mt-8 text-center"><a href="<?php echo esc_url( $tours_url ); ?>" class="btn-accent">Дивитись всі</a></div>
	</div>
</section>

<?php $sea = mustsee_tours( array( 'category' => 'sea', 'per_page' => 4 ) )['items']; ?>
<?php if ( $sea ) : ?>
<section>
	<div class="container-site py-10">
		<h2 class="ty-h2 text-center text-brand">Тури на море</h2>
		<div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
			<?php foreach ( $sea as $tour ) { mustsee_tour_card( $tour ); } ?>
		</div>
		<div class="mt-8 text-center"><a href="<?php echo esc_url( $tours_url ); ?>" class="btn-accent">Дивитись всі</a></div>
	</div>
</section>
<?php endif; ?>

<?php $events = mustsee_tours( array( 'category' => 'parks', 'per_page' => 3 ) )['items']; ?>
<?php if ( $events ) : ?>
<section class="bg-brand-50">
	<div class="container-site py-10">
		<h2 class="ty-h2 text-center text-brand">Події та свята</h2>
		<div class="mt-6 grid gap-5 md:grid-cols-3">
			<?php
			foreach ( $events as $tour ) :
				$sub     = $tour['places'] ?? '';
				$overlay = '<span class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></span><h3 class="ty-h6 relative">' . esc_html( $tour['title'] ?? '' ) . '</h3>' . ( $sub ? '<p class="ty-text relative mt-1 text-white/85">' . esc_html( $sub ) . '</p>' : '' );
				echo '<a href="' . esc_url( mustsee_tour_url( $tour ) ) . '" class="relative flex h-56 flex-col justify-end rounded-2xl p-6 text-white transition hover:opacity-95">';
				mustsee_tour_image( $tour, 'absolute inset-0 rounded-2xl' );
				echo $overlay; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo '</a>';
			endforeach;
			?>
		</div>
		<div class="mt-8 text-center"><a href="<?php echo esc_url( $tours_url ); ?>" class="btn-accent">Дивитись всі</a></div>
	</div>
</section>
<?php endif; ?>

<?php $locations = mustsee_tours( array( 'category' => 'top', 'per_page' => 4 ) )['items']; ?>
<?php if ( $locations ) : ?>
<section>
	<div class="container-site py-10">
		<h2 class="ty-h2 text-center text-brand">Популярні локації</h2>
		<div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
			<?php
			foreach ( $locations as $tour ) :
				$country = implode( ', ', (array) ( $tour['countries'] ?? array() ) );
				$overlay = '<span class="absolute inset-0 bg-gradient-to-t from-black/55 to-transparent"></span><span class="ty-h6 absolute bottom-4 left-4 text-white">' . esc_html( $tour['title'] ?? '' ) . '</span>';
				?>
				<a href="<?php echo esc_url( mustsee_tour_url( $tour ) ); ?>" class="group block overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm transition hover:shadow-md">
					<?php mustsee_tour_image( $tour, 'aspect-[4/3]', $overlay ); ?>
					<div class="space-y-1 p-4">
						<?php if ( $country ) : ?><p class="ty-opys-bold text-gray-800"><?php echo esc_html( $country ); ?></p><?php endif; ?>
						<p class="ty-text-bold text-accent"><?php echo esc_html( mustsee_tour_price( $tour ) ); ?></p>
					</div>
				</a>
				<?php
			endforeach;
			?>
		</div>
		<div class="mt-8 text-center"><a href="<?php echo esc_url( $tours_url ); ?>" class="btn-accent">Дивитись всі</a></div>
	</div>
</section>
<?php endif; ?>

<?php if ( $tour_terms ) : ?>
<section class="bg-gray-50">
	<div class="container-site py-10">
		<h2 class="ty-h2 text-center text-brand">Тури по категоріям</h2>
		<div class="mt-6 flex flex-wrap justify-center gap-3">
			<?php foreach ( $tour_terms as $term ) : ?>
				<a href="<?php echo esc_url( add_query_arg( 'cat', $term['slug'], $tours_url ) ); ?>" class="ty-text rounded-full bg-brand-300 px-5 py-2.5 text-white transition hover:bg-brand"><?php echo esc_html( $term['name'] ); ?></a>
			<?php endforeach; ?>
		</div>
		<div class="mt-8 text-center"><a href="<?php echo esc_url( $tours_url ); ?>" class="btn-accent">Дивитись всі</a></div>
	</div>
</section>
<?php endif; ?>

<?php $news = new WP_Query( array( 'post_type' => 'post', 'posts_per_page' => 3, 'ignore_sticky_posts' => true ) ); ?>
<?php if ( $news->have_posts() ) : ?>
<section>
	<div class="container-site py-10">
		<h2 class="ty-h2 text-center text-brand">Новини</h2>
		<div class="mt-6 grid gap-5 md:grid-cols-3">
			<?php
			while ( $news->have_posts() ) :
				$news->the_post();
				get_template_part( 'template-parts/article-card', null, array( 'href' => get_permalink(), 'date' => get_the_date( 'd / m / Y' ), 'title' => get_the_title(), 'id' => get_the_ID() ) );
			endwhile;
			wp_reset_postdata();
			?>
		</div>
		<div class="mt-8 text-center"><a href="<?php echo esc_url( home_url( '/news/' ) ); ?>" class="btn-accent">Дивитись всі</a></div>
	</div>
</section>
<?php endif; ?>

<?php get_template_part( 'template-parts/lead-form' ); ?>

<?php $reviews = function_exists( 'mustsee_get_reviews' ) ? mustsee_get_reviews( 3 ) : array(); ?>
<?php if ( $reviews ) : ?>
<section>
	<div class="container-site py-10">
		<h2 class="ty-h2 text-center text-brand">Відгуки туристів</h2>
		<div class="mt-6 grid gap-5 md:grid-cols-3">
			<?php foreach ( $reviews as $r ) : ?>
				<div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
					<div class="flex items-center gap-3">
						<?php if ( has_post_thumbnail( $r->ID ) ) : ?>
							<span class="relative h-12 w-12 shrink-0 overflow-hidden rounded-full"><?php echo get_the_post_thumbnail( $r->ID, 'thumbnail', array( 'class' => 'absolute inset-0 h-full w-full object-cover' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						<?php else : ?>
							<span class="h-12 w-12 shrink-0 rounded-full media-placeholder"></span>
						<?php endif; ?>
						<p class="ty-opys-bold text-gray-800"><?php echo esc_html( get_the_title( $r->ID ) ); ?></p>
					</div>
					<p class="ty-text mt-4 text-gray-600"><?php echo esc_html( wp_strip_all_tags( $r->post_content ) ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<div class="container-site py-12">
	<p class="ty-text text-gray-600">Ми — надійний туроператор автобусних турів, що спеціалізується на комфортних подорожах по Європі та Україні. Організовуємо авторські автобусні тури, групові поїздки, екскурсійні маршрути та тури вихідного дня з повним супроводом.</p>
	<h2 class="ty-h2 mt-8 text-brand">Туроператор Must See Travel</h2>
	<div class="mt-4 grid gap-6 md:grid-cols-2">
		<div>
			<h3 class="ty-h6 text-gray-800">Місія</h3>
			<p class="ty-text mt-2 text-gray-600">Ми створюємо подорожі, які дарують клієнтам більше, ніж просто тур — емоції, відкриття і відчуття справжньої Європи.</p>
		</div>
		<div>
			<h3 class="ty-h6 text-gray-800">Цінності</h3>
			<p class="ty-text mt-2 text-gray-600">Якість продукту, чесність з клієнтом, експертність, емоції від подорожей, партнерство та надійність.</p>
		</div>
	</div>
	<div class="mt-8 grid gap-6 md:grid-cols-3">
		<?php foreach ( $seo_columns as $c ) : ?>
			<div>
				<h3 class="ty-h6 text-gray-800"><?php echo esc_html( $c['title'] ); ?></h3>
				<p class="ty-text mt-2 text-gray-600"><?php echo esc_html( $c['body'] ); ?></p>
				<?php if ( ! empty( $c['list'] ) ) : ?>
					<ul class="ty-text mt-2 list-disc space-y-1 pl-5 text-gray-600">
						<?php foreach ( $c['list'] as $i ) : ?><li><?php echo esc_html( $i ); ?></li><?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
	</div>
</div>

<?php get_template_part( 'template-parts/newsletter' ); ?>

<?php
get_footer();
