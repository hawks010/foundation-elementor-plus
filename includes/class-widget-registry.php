<?php

namespace FoundationElementorPlus;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Widget_Registry {
	/**
	 * Widget manifest for the plugin suite.
	 *
	 * @return array<string, array<string, string>>
	 */
	public static function get_widget_manifest() {
		return array(
			'foundation-dark-animated-hero' => array(
				'class'       => Widgets\Dark_Animated_Hero_Widget::class,
				'title'       => 'Dark Animated Hero',
				'description' => 'Fluid animated hero with preset-driven Inkfire layout controls and Elementor overrides.',
			),
			'foundation-mobile-header' => array(
				'class'       => Widgets\Mobile_Header_Widget::class,
				'title'       => 'Mobile Glass Header',
				'description' => 'Editable Inkfire mobile header with logo, quick actions, accordion panels, and menu links.',
			),
			'foundation-selector-stack' => array(
				'class'       => Widgets\Selector_Stack_Widget::class,
				'title'       => 'Selector Stack',
				'description' => 'Sticky stacked cards with a pinned left-hand navigation.',
			),
			'foundation-y-hero'         => array(
				'class'       => Widgets\Y_Hero_Widget::class,
				'title'       => 'Y Hero',
				'description' => 'Split hero with stacked slider cards, stats, and CTA controls.',
			),
			'foundation-bounce-rail'   => array(
				'class'       => Widgets\Bounce_Rail_Widget::class,
				'title'       => 'Bounce Rail',
				'description' => 'Scrollable rail with mixed blog stacks and YouTube reel columns.',
			),
			'foundation-process-carousel' => array(
				'class'       => Widgets\Process_Carousel_Widget::class,
				'title'       => 'Process Carousel',
				'description' => 'Tabbed process section with a card carousel and editable checklist steps.',
			),
			'foundation-portfolio-mosaic' => array(
				'class'       => Widgets\Portfolio_Mosaic_Widget::class,
				'title'       => 'Dynamic Grid',
				'description' => 'Flexible editorial grid for portfolio items, blog posts, archive/search results, and manual cards.',
			),
			'foundation-portfolio-mega-menu' => array(
				'class'       => Widgets\Portfolio_Mega_Menu_Widget::class,
				'title'       => 'Inkfire In Action Mega Menu',
				'description' => 'Compact mega-menu panel for the Inkfire In Action hub, mixed activity links, and a project CTA card.',
			),
			'foundation-awards-recognition-wall' => array(
				'class'       => Widgets\Awards_Recognition_Wall_Widget::class,
				'title'       => 'Awards Recognition Wall',
				'description' => 'Static editorial awards wall with alternating feature blocks, image cards, and premium glass surfaces.',
			),
			'foundation-live-roles' => array(
				'class'       => Widgets\Live_Roles_Widget::class,
				'title'       => 'Live Roles',
				'description' => 'Mini careers hero with filterable live role rows, traffic-light status, and expandable details.',
			),
			'foundation-live-events' => array(
				'class'       => Widgets\Live_Events_Widget::class,
				'title'       => 'Live Events',
				'description' => 'Expandable full-width glass event cards split into upcoming and past event groups.',
			),
			'foundation-team-loop' => array(
				'class'       => Widgets\Team_Loop_Widget::class,
				'title'       => 'Foundation Team Loop',
				'description' => 'Filterable team member loop with glass staff cards, department views, hierarchy ordering, and an optional live-roles feature card.',
			),
			'foundation-inkfire-linktree' => array(
				'class'       => Widgets\Inkfire_Linktree_Widget::class,
				'title'       => 'Inkfire Linktree',
				'description' => 'Inkfire quicklinks hub with video, news, newsletter signup, reviews, and awards.',
			),
			'foundation-rubiks-gallery' => array(
				'class'       => Widgets\Rubiks_Gallery_Widget::class,
				'title'       => 'Rubiks Gallery',
				'description' => 'Animated 2x2 mixed-media gallery with image, SVG, video, and Lottie support.',
			),
			'foundation-sender-newsletter' => array(
				'class'       => Widgets\Sender_Newsletter_Widget::class,
				'title'       => 'Sender Newsletter',
				'description' => 'Newsletter signup form wired to the live Sender account and groups.',
			),
		);
	}

	/**
	 * List of widget classes in this suite.
	 *
	 * @return array<int, string>
	 */
	public static function get_widget_classes() {
		require_once FOUNDATION_ELEMENTOR_PLUS_PATH . 'includes/class-dark-animated-hero-renderer.php';
		require_once FOUNDATION_ELEMENTOR_PLUS_PATH . 'includes/widgets/class-base-widget.php';
		require_once FOUNDATION_ELEMENTOR_PLUS_PATH . 'includes/widgets/class-dark-animated-hero-widget.php';
		require_once FOUNDATION_ELEMENTOR_PLUS_PATH . 'includes/widgets/class-mobile-header-widget.php';
		require_once FOUNDATION_ELEMENTOR_PLUS_PATH . 'includes/widgets/class-selector-stack-widget.php';
		require_once FOUNDATION_ELEMENTOR_PLUS_PATH . 'includes/widgets/class-y-hero-widget.php';
		require_once FOUNDATION_ELEMENTOR_PLUS_PATH . 'includes/widgets/class-bounce-rail-widget.php';
		require_once FOUNDATION_ELEMENTOR_PLUS_PATH . 'includes/widgets/class-process-carousel-widget.php';
		require_once FOUNDATION_ELEMENTOR_PLUS_PATH . 'includes/widgets/class-portfolio-mosaic-widget.php';
		require_once FOUNDATION_ELEMENTOR_PLUS_PATH . 'includes/widgets/class-portfolio-mega-menu-widget.php';
		require_once FOUNDATION_ELEMENTOR_PLUS_PATH . 'includes/widgets/class-awards-recognition-wall-widget.php';
		require_once FOUNDATION_ELEMENTOR_PLUS_PATH . 'includes/widgets/class-live-roles-widget.php';
		require_once FOUNDATION_ELEMENTOR_PLUS_PATH . 'includes/widgets/class-live-events-widget.php';
		require_once FOUNDATION_ELEMENTOR_PLUS_PATH . 'includes/widgets/class-team-loop-widget.php';
		require_once FOUNDATION_ELEMENTOR_PLUS_PATH . 'includes/widgets/class-inkfire-linktree-widget.php';
		require_once FOUNDATION_ELEMENTOR_PLUS_PATH . 'includes/widgets/class-rubiks-gallery-widget.php';
		require_once FOUNDATION_ELEMENTOR_PLUS_PATH . 'includes/widgets/class-sender-newsletter-widget.php';

		return array_values(
			array_map(
				static function( $widget ) {
					return $widget['class'];
				},
				self::get_widget_manifest()
			)
		);
	}

	/**
	 * Back-compat loader for older plugin bootstrap code paths.
	 *
	 * @return void
	 */
	public static function load_widget_class_files() {
		self::get_widget_classes();
	}

	/**
	 * Shared asset registry.
	 *
	 * @return array<string, array<string, string>>
	 */
	public static function get_asset_map() {
		return array(
			'styles'  => array(
				'foundation-elementor-plus-dark-animated-hero' => 'assets/css/dark-animated-hero.css',
				'foundation-elementor-plus-mobile-header' => 'assets/css/mobile-header.css',
				'foundation-elementor-plus-selector-stack' => 'assets/css/selector-stack.css',
				'foundation-elementor-plus-y-hero'         => 'assets/css/y-hero.css',
				'foundation-elementor-plus-bounce-rail'    => 'assets/css/bounce-rail.css',
				'foundation-elementor-plus-process-carousel' => 'assets/css/process-carousel.css',
				'foundation-elementor-plus-portfolio-mosaic' => 'assets/css/portfolio-mosaic.css',
				'foundation-elementor-plus-portfolio-mega-menu' => 'assets/css/portfolio-mega-menu.css',
				'foundation-elementor-plus-awards-wall'      => 'assets/css/awards-wall.css',
				'foundation-elementor-plus-live-roles'       => 'assets/css/live-roles.css',
				'foundation-elementor-plus-live-events'      => 'assets/css/live-events.css',
				'foundation-elementor-plus-team-loop'        => 'assets/css/team-loop.css',
				'foundation-elementor-plus-rubiks-gallery'   => 'assets/css/rubiks-gallery.css',
				'foundation-elementor-plus-sender-newsletter' => 'assets/css/sender-newsletter.css',
			),
			'scripts' => array(
				'foundation-elementor-plus-fluid-core' => 'assets/vendor/fluid-core.js',
				'foundation-elementor-plus-lottie'     => 'assets/vendor/lottie.min.js',
				'foundation-elementor-plus-dark-animated-hero' => array(
					'path' => 'assets/js/dark-animated-hero.js',
					'deps' => array( 'foundation-elementor-plus-fluid-core' ),
				),
				'foundation-elementor-plus-mobile-header' => 'assets/js/mobile-header.js',
				'foundation-elementor-plus-selector-stack' => 'assets/js/selector-stack.js',
				'foundation-elementor-plus-y-hero'         => 'assets/js/y-hero.js',
				'foundation-elementor-plus-bounce-rail'    => 'assets/js/bounce-rail.js',
				'foundation-elementor-plus-process-carousel' => 'assets/js/process-carousel.js',
				'foundation-elementor-plus-portfolio-mosaic' => 'assets/js/portfolio-mosaic.js',
				'foundation-elementor-plus-portfolio-mega-menu' => 'assets/js/portfolio-mega-menu.js',
				'foundation-elementor-plus-awards-wall'      => 'assets/js/awards-wall.js',
				'foundation-elementor-plus-live-roles'       => 'assets/js/live-roles.js',
				'foundation-elementor-plus-live-events'      => 'assets/js/live-events.js',
				'foundation-elementor-plus-team-loop'        => 'assets/js/team-loop.js',
				'foundation-elementor-plus-rubiks-gallery'   => array(
					'path' => 'assets/js/rubiks-gallery.js',
					'deps' => array( 'foundation-elementor-plus-lottie' ),
				),
				'foundation-elementor-plus-sender-newsletter' => 'assets/js/sender-newsletter.js',
			),
		);
	}
}
