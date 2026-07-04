<?php
/**
 * Theme header — top bar + navigation.
 *
 * @package MustSee_Travel
 */
defined( 'ABSPATH' ) || exit;

$phone       = mustsee_config( 'phone' );
$currency    = mustsee_config( 'currency' );
$primary     = mustsee_menu_items( 'primary', mustsee_config( 'primary_nav' ) );
$secondary   = mustsee_menu_items( 'secondary', mustsee_config( 'secondary_nav' ) );
$info_menu   = mustsee_menu_items( 'info', mustsee_config( 'info_menu' ) );

$tour_cats = mustsee_config( 'tour_categories' );
$api_cats  = function_exists( 'mustsee_tour_categories' ) ? mustsee_tour_categories() : array();
if ( $api_cats ) {
	$tour_cats = array();
	foreach ( $api_cats as $c ) {
		$tour_cats[] = array( 'title' => $c['name'], 'href' => add_query_arg( 'cat', $c['slug'], home_url( '/tours/' ) ) );
	}
}

// Config fallback items carry a 'dropdown' key; menu items get 'children' from submenus.
foreach ( $primary as &$item ) {
	if ( 'tours' === ( $item['dropdown'] ?? '' ) ) {
		$item['children'] = $tour_cats;
	}
}
unset( $item );
foreach ( $secondary as &$item ) {
	if ( 'info' === ( $item['dropdown'] ?? '' ) ) {
		$item['children'] = $info_menu;
	}
}
unset( $item );

$icons = array(
	'pin'     => 'M12 21s-7-4.5-7-9.5A3.5 3.5 0 0 1 12 7a3.5 3.5 0 0 1 7 .5C19 16.5 12 21 12 21Z',
	'routes'  => 'M12 3v18M4 7h16M6 7l-3 6h6L6 7Zm12 0l-3 6h6l-3-6Z',
	'cabinet' => 'M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm-7 8a7 7 0 0 1 14 0',
);
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'flex min-h-screen flex-col font-sans' ); ?>>
<?php wp_body_open(); ?>

<header>
	<div class="bg-brand text-white">
		<div class="mx-auto flex h-14 w-full max-w-6xl items-center gap-5 px-4">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( mustsee_config( 'name' ) ); ?>" class="shrink-0">
				<?php mustsee_logo( 'h-8 w-auto' ); ?>
			</a>

			<button class="ty-text hidden items-center gap-1 text-white/90 lg:inline-flex">
				<?php echo esc_html( $currency ); ?>
				<span class="text-white/60">&#9662;</span>
			</button>

			<a href="tel:<?php echo esc_attr( mustsee_tel( $phone ) ); ?>" class="ty-text-bold mx-auto hidden lg:block">
				<?php echo esc_html( $phone ); ?>
			</a>

			<a href="<?php echo esc_url( home_url( '/booking/' ) ); ?>" class="ty-menu-vkladky hidden rounded-full bg-accent px-5 py-2.5 text-white transition hover:bg-accent-600 lg:inline-block">
				Швидке замовлення
			</a>

			<div class="ml-auto hidden items-center gap-3 lg:flex">
				<?php foreach ( $icons as $d ) : ?>
					<svg aria-hidden="true" viewBox="0 0 24 24" class="h-5 w-5 fill-none stroke-current" stroke-width="1.6">
						<path d="<?php echo esc_attr( $d ); ?>" stroke-linecap="round" stroke-linejoin="round" />
					</svg>
				<?php endforeach; ?>
			</div>

			<div class="ml-auto flex items-center gap-3 lg:hidden">
				<a href="<?php echo esc_url( home_url( '/cabinet/' ) ); ?>" aria-label="Кабінет">
					<svg aria-hidden="true" viewBox="0 0 24 24" class="h-6 w-6 fill-none stroke-current" stroke-width="1.6">
						<path d="<?php echo esc_attr( $icons['cabinet'] ); ?>" stroke-linecap="round" stroke-linejoin="round" />
					</svg>
				</a>
				<button type="button" data-menu-open aria-label="Відкрити меню" class="flex h-9 w-9 items-center justify-center">
					<svg aria-hidden="true" viewBox="0 0 24 24" class="h-6 w-6 stroke-current" stroke-width="2">
						<path d="M4 7h16M4 12h16M4 17h16" stroke-linecap="round" />
					</svg>
				</button>
			</div>
		</div>
	</div>

	<div class="border-b border-white/10 bg-brand text-white lg:hidden">
		<div class="mx-auto flex h-11 w-full max-w-6xl items-center justify-center gap-5 px-4">
			<?php foreach ( $primary as $item ) : ?>
				<a href="<?php echo esc_url( mustsee_url( $item['href'] ) ); ?>" class="ty-menu-vkladky"><?php echo esc_html( $item['title'] ); ?></a>
			<?php endforeach; ?>
		</div>
	</div>

	<div class="hidden border-b border-gray-200 bg-white lg:block">
		<div class="mx-auto flex h-12 w-full max-w-6xl items-center justify-between px-4">
			<nav class="flex items-center gap-6">
				<?php foreach ( $primary as $item ) : ?>
					<?php if ( ! empty( $item['children'] ) ) : ?>
						<?php
						get_template_part(
							'template-parts/nav-dropdown',
							null,
							array(
								'item'         => $item,
								'button_class' => 'ty-menu-vkladky text-gray-800',
								'panel_class'  => 'left-0 grid w-[34rem] grid-cols-2 gap-1',
							)
						);
						?>
					<?php else : ?>
						<a href="<?php echo esc_url( mustsee_url( $item['href'] ) ); ?>" class="ty-menu-vkladky <?php echo mustsee_is_current( $item['href'] ) ? 'text-brand' : 'text-gray-800 hover:text-brand'; ?>"><?php echo esc_html( $item['title'] ); ?></a>
					<?php endif; ?>
				<?php endforeach; ?>
			</nav>
			<nav class="flex items-center gap-6">
				<?php foreach ( $secondary as $item ) : ?>
					<?php if ( ! empty( $item['children'] ) ) : ?>
						<?php
						get_template_part(
							'template-parts/nav-dropdown',
							null,
							array(
								'item'         => $item,
								'button_class' => 'ty-menu-2 text-gray-500',
								'panel_class'  => 'right-0 min-w-56',
							)
						);
						?>
					<?php else : ?>
						<a href="<?php echo esc_url( mustsee_url( $item['href'] ) ); ?>" class="ty-menu-2 text-gray-500 hover:text-brand"><?php echo esc_html( $item['title'] ); ?></a>
					<?php endif; ?>
				<?php endforeach; ?>
			</nav>
		</div>
	</div>
</header>

<div data-menu hidden class="fixed inset-0 z-50 lg:hidden">
	<div data-menu-close class="absolute inset-0 bg-black/40"></div>
	<nav class="absolute right-0 top-0 flex h-full w-72 flex-col gap-5 overflow-y-auto bg-brand-50 p-6 text-gray-800">
		<button type="button" data-menu-close aria-label="Закрити меню" class="self-end">
			<svg aria-hidden="true" viewBox="0 0 24 24" class="h-6 w-6 stroke-current" stroke-width="2"><path d="M6 6l12 12M18 6L6 18" stroke-linecap="round" /></svg>
		</button>
		<div class="flex flex-col gap-2">
			<?php foreach ( $primary as $item ) : ?>
				<a href="<?php echo esc_url( mustsee_url( $item['href'] ) ); ?>" class="ty-menu-vkladky text-brand"><?php echo esc_html( $item['title'] ); ?></a>
			<?php endforeach; ?>
		</div>
		<div>
			<p class="ty-text-bold">Інфо</p>
			<div class="mt-2 flex flex-col gap-2 pl-3">
				<?php foreach ( $info_menu as $item ) : ?>
					<a href="<?php echo esc_url( mustsee_url( $item['href'] ) ); ?>" class="ty-text text-gray-600"><?php echo esc_html( $item['title'] ); ?></a>
				<?php endforeach; ?>
			</div>
		</div>
		<div class="flex flex-col gap-2">
			<?php foreach ( $secondary as $item ) : ?>
				<?php // Items with children are already rendered in the info dropdown block above. ?>
				<?php if ( ! empty( $item['children'] ) ) { continue; } ?>
				<a href="<?php echo esc_url( mustsee_url( $item['href'] ) ); ?>" class="ty-text-bold"><?php echo esc_html( $item['title'] ); ?></a>
			<?php endforeach; ?>
		</div>
		<p class="ty-text text-gray-500"><?php echo esc_html( $currency ); ?></p>
		<?php get_template_part( 'template-parts/socials' ); ?>
	</nav>
</div>

<main class="flex-1">
