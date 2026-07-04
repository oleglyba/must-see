<?php
/**
 * Custom post type — tourist reviews.
 *
 * @package MustSee_Travel
 */
defined( 'ABSPATH' ) || exit;

add_action(
	'init',
	function () {
		register_post_type(
			'review',
			array(
				'labels'       => array(
					'name'          => 'Відгуки',
					'singular_name' => 'Відгук',
					'add_new_item'  => 'Додати відгук',
					'edit_item'     => 'Редагувати відгук',
					'menu_name'     => 'Відгуки',
				),
				'public'       => false,
				'show_ui'      => true,
				'show_in_menu' => true,
				'menu_icon'    => 'dashicons-format-quote',
				'supports'     => array( 'title', 'editor', 'thumbnail' ),
				'has_archive'  => false,
			)
		);
	}
);

function mustsee_get_reviews( int $limit = 3 ): array {
	return get_posts(
		array(
			'post_type'      => 'review',
			'posts_per_page' => $limit,
			'post_status'    => 'publish',
		)
	);
}
