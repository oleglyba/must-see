<?php
/**
 * Must See Travel — theme functions.
 *
 * @package MustSee_Travel
 */

defined( 'ABSPATH' ) || exit;

define( 'MUSTSEE_VERSION', '4.0.0' );

function mustsee_setup(): void {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'customize-selective-refresh-widgets' );

	register_nav_menus(
		array(
			'primary'   => __( 'Головне меню', 'mustsee' ),
			'secondary' => __( 'Додаткове меню', 'mustsee' ),
			'info'      => __( 'Меню «Інфо»', 'mustsee' ),
			'cities'    => __( 'Популярні міста', 'mustsee' ),
			'footer_1'  => __( 'Підвал — колонка 1', 'mustsee' ),
			'footer_2'  => __( 'Підвал — колонка 2', 'mustsee' ),
			'footer_3'  => __( 'Підвал — колонка 3', 'mustsee' ),
		)
	);
}
add_action( 'after_setup_theme', 'mustsee_setup' );

/**
 * Critical CSS is inlined in <head>; the full bundle and theme style are
 * deferred (see mustsee_defer_styles). Fonts are self-hosted (see mustsee_fonts).
 * All JS is deferred in the footer. Assets are cache-busted by filemtime.
 */
function mustsee_assets(): void {
	$dir = get_stylesheet_directory();
	$uri = get_stylesheet_directory_uri();

	$ver = static function ( string $rel ) use ( $dir ) {
		return file_exists( $dir . $rel ) ? filemtime( $dir . $rel ) : MUSTSEE_VERSION;
	};

	wp_enqueue_style( 'mustsee-tailwind', $uri . '/assets/css/tailwind.css', array(), $ver( '/assets/css/tailwind.css' ) );
	wp_enqueue_style( 'mustsee-style', get_stylesheet_uri(), array( 'mustsee-tailwind' ), MUSTSEE_VERSION );

	wp_enqueue_script(
		'mustsee-theme',
		$uri . '/assets/js/theme.js',
		array(),
		$ver( '/assets/js/theme.js' ),
		array( 'in_footer' => true, 'strategy' => 'defer' )
	);
	wp_localize_script(
		'mustsee-theme',
		'MustSeeData',
		array( 'ajax' => admin_url( 'admin-ajax.php' ), 'nonce' => wp_create_nonce( 'mustsee' ) )
	);
}
add_action( 'wp_enqueue_scripts', 'mustsee_assets' );

function mustsee_inline_critical_css(): void {
	$file = get_stylesheet_directory() . '/assets/css/critical.css';
	if ( ! file_exists( $file ) ) {
		return;
	}
	echo '<style id="mustsee-critical">' . file_get_contents( $file ) . "</style>\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
}
add_action( 'wp_head', 'mustsee_inline_critical_css', 2 );

/**
 * Self-hosted Inter (variable woff2, weights 100–900, latin + cyrillic).
 * @font-face is inlined in <head> with absolute URLs so it is discovered
 * immediately, independent of the deferred stylesheets; the upright file is
 * preloaded. Italic loads on demand.
 */
function mustsee_fonts(): void {
	$fonts = get_stylesheet_directory_uri() . '/assets/fonts';
	printf(
		'<link rel="preload" href="%s/inter-var.woff2" as="font" type="font/woff2" crossorigin>' . "\n",
		esc_url( $fonts )
	);
	echo '<style id="mustsee-fonts">'
		. '@font-face{font-family:"Inter";font-style:normal;font-weight:100 900;font-display:swap;'
		. 'src:url(' . esc_url( $fonts . '/inter-var.woff2' ) . ') format("woff2")}'
		. '@font-face{font-family:"Inter";font-style:italic;font-weight:100 900;font-display:swap;'
		. 'src:url(' . esc_url( $fonts . '/inter-italic-var.woff2' ) . ') format("woff2")}'
		. "</style>\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
add_action( 'wp_head', 'mustsee_fonts', 1 );

function mustsee_defer_styles( string $tag, string $handle ): string {
	$deferred = array( 'mustsee-tailwind', 'mustsee-style' );
	if ( ! in_array( $handle, $deferred, true ) ) {
		return $tag;
	}
	$count        = 0;
	$deferred_tag = preg_replace( "/media=('|\")[^'\"]*('|\")/", "media='print' onload=\"this.media='all'\"", $tag, 1, $count );
	if ( ! $count ) {
		$deferred_tag = str_replace( '/>', "media='print' onload=\"this.media='all'\" />", $tag );
	}
	return $deferred_tag . '<noscript>' . $tag . '</noscript>' . "\n";
}
add_filter( 'style_loader_tag', 'mustsee_defer_styles', 10, 2 );

/**
 * Theme config — single source of truth for static labels and nav fallbacks.
 */
function mustsee_config( string $key ) {
	static $config = null;

	if ( null === $config ) {
		$config = array(
			'name'        => 'Must See Travel',
			'phone'       => '+380 (67) 777 55 33',
			'currency'    => 'EUR (51.30 ГРН)*',
			'primary_nav' => array(
				array( 'title' => 'Пошук туру', 'href' => '/' ),
				array( 'title' => 'Автобусні тури', 'href' => '/tours/', 'dropdown' => 'tours' ),
				array( 'title' => 'Авіатури', 'href' => '/tours/' ),
			),
			'secondary_nav' => array(
				array( 'title' => 'Інфо', 'href' => '/info/', 'dropdown' => 'info' ),
				array( 'title' => 'Новини', 'href' => '/news/' ),
				array( 'title' => 'Club', 'href' => '/club/' ),
				array( 'title' => 'Контакти', 'href' => '/contacts/' ),
			),
			'tour_categories' => array(
				array( 'title' => 'Без нічних переїздів', 'href' => '/tours/' ),
				array( 'title' => 'З 1 нічним переїздом', 'href' => '/tours/' ),
				array( 'title' => 'Тури вихідного дня', 'href' => '/tours/' ),
				array( 'title' => 'Пляжний відпочинок', 'href' => '/tours/' ),
				array( 'title' => 'Авіатури', 'href' => '/tours/' ),
				array( 'title' => 'Релакс тури', 'href' => '/tours/' ),
				array( 'title' => 'Тури на канікули', 'href' => '/tours/' ),
				array( 'title' => 'Для шкільних груп', 'href' => '/tours/' ),
				array( 'title' => 'Тури до парків розваг', 'href' => '/tours/' ),
				array( 'title' => 'Тури на випускний', 'href' => '/tours/' ),
				array( 'title' => 'Найпопулярніші тури', 'href' => '/tours/' ),
				array( 'title' => 'Інші тури', 'href' => '/tours/' ),
			),
			'countries' => array(
				'Албанія', 'Словенія', 'Італія', 'Іспанія', 'Польща',
				'Чехія', 'Болгарія', 'Франція', 'Угорщина', 'Хорватія',
			),
			'info_menu' => array(
				array( 'title' => 'Агентам', 'href' => '/info/' ),
				array( 'title' => 'Туристам', 'href' => '/info/' ),
				array( 'title' => 'Вакансії', 'href' => '/info/' ),
				array( 'title' => 'Політика конфіденційності', 'href' => '/info/' ),
				array( 'title' => 'Договір публічної оферти', 'href' => '/info/' ),
				array( 'title' => 'Подарунковий сертифікат', 'href' => '/info/' ),
				array( 'title' => 'Блог', 'href' => '/news/' ),
				array( 'title' => 'Карта сайту', 'href' => '/' ),
			),
			'footer_columns' => array(
				array(
					array( 'title' => 'Політика конфіденційності', 'href' => '/info/' ),
					array( 'title' => 'Договір публічної оферти', 'href' => '/info/' ),
				),
				array(
					array( 'title' => 'Контакти', 'href' => '/contacts/' ),
					array( 'title' => 'Подарунковий сертифікат', 'href' => '/info/' ),
					array( 'title' => 'Блог', 'href' => '/news/' ),
					array( 'title' => 'Карта сайту', 'href' => '/' ),
				),
				array(
					array( 'title' => 'Агентам', 'href' => '/info/' ),
					array( 'title' => 'Туристам', 'href' => '/info/' ),
					array( 'title' => 'Вакансії', 'href' => '/info/' ),
				),
			),
			'cabinet_nav' => array(
				'Користувач', 'Заявки', 'Агенція', 'Куратор',
				'Документи', 'Програма лояльності', 'Поширені запитання', 'Навчання',
			),
			// Social links — replace '#' with real profile URLs.
			'socials' => array(
				array( 'label' => 'Telegram', 'icon' => 'tg', 'url' => '#' ),
				array( 'label' => 'Viber', 'icon' => 'vb', 'url' => '#' ),
				array( 'label' => 'Facebook', 'icon' => 'fb', 'url' => '#' ),
				array( 'label' => 'Instagram', 'icon' => 'ig', 'url' => '#' ),
				array( 'label' => 'TikTok', 'icon' => 'tt', 'url' => '#' ),
			),
		);
		$config = apply_filters( 'mustsee_config', $config );
	}

	return $config[ $key ] ?? null;
}

function mustsee_url( string $href ): string {
	if ( preg_match( '#^(https?:)?//#', $href ) || str_starts_with( $href, 'tel:' ) || str_starts_with( $href, 'mailto:' ) || str_starts_with( $href, '#' ) ) {
		return $href;
	}
	return home_url( $href );
}

function mustsee_tel( string $phone ): string {
	return preg_replace( '/[^+\d]/', '', $phone );
}

/**
 * Whether $href (theme-relative or absolute) points at the current request.
 */
function mustsee_is_current( string $href ): bool {
	global $wp;
	$current = home_url( '/' . ltrim( (string) ( $wp->request ?? '' ), '/' ) );
	return untrailingslashit( mustsee_url( $href ) ) === untrailingslashit( $current );
}

function mustsee_logo( string $class = 'h-8 w-auto' ): void {
	get_template_part( 'template-parts/logo', null, array( 'class' => $class ) );
}

/**
 * Generate WebP for all resized images by default (originals keep their format).
 */
add_filter(
	'image_editor_output_format',
	function ( array $formats ): array {
		$formats['image/jpeg'] = 'image/webp';
		$formats['image/png']  = 'image/webp';
		return $formats;
	}
);
add_filter(
	'wp_editor_set_quality',
	function ( $quality, $mime = '' ) {
		return 'image/webp' === $mime ? 82 : (int) $quality;
	},
	10,
	2
);

/**
 * Single source for the agent commission rate (filterable).
 */
function mustsee_commission_rate(): float {
	return (float) apply_filters( 'mustsee_commission_rate', 0.12 );
}

/**
 * Menu items for a location as [['title','href','children'=>[...]], ...];
 * falls back to $fallback when no menu is assigned there. Second-level
 * menu items become the parent's 'children' (rendered as a dropdown).
 */
function mustsee_menu_items( string $location, array $fallback = array() ): array {
	$locations = get_nav_menu_locations();
	if ( empty( $locations[ $location ] ) ) {
		return $fallback;
	}
	$items = wp_get_nav_menu_items( $locations[ $location ] );
	if ( ! $items ) {
		return $fallback;
	}
	$out = array();
	foreach ( $items as $item ) {
		if ( 0 === (int) $item->menu_item_parent ) {
			$out[ $item->ID ] = array( 'title' => $item->title, 'href' => $item->url );
		}
	}
	foreach ( $items as $item ) {
		$parent = (int) $item->menu_item_parent;
		if ( $parent && isset( $out[ $parent ] ) ) {
			$out[ $parent ]['children'][] = array( 'title' => $item->title, 'href' => $item->url );
		}
	}
	return $out ? array_values( $out ) : $fallback;
}

require get_stylesheet_directory() . '/inc/api.php';
require get_stylesheet_directory() . '/inc/tours.php';
require get_stylesheet_directory() . '/inc/routing.php';
require get_stylesheet_directory() . '/inc/cpt.php';
require get_stylesheet_directory() . '/inc/services.php';
require get_stylesheet_directory() . '/inc/forms.php';
require get_stylesheet_directory() . '/inc/auth.php';
