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
				'title'       => 'Mobile Header',
				'description' => 'Editable Inkfire mobile header with logo, quick actions, accordion panels, and cleaner controls.',
			),
			'foundation-selector-stack' => array(
				'class'       => Widgets\Selector_Stack_Widget::class,
				'title'       => 'Selector Stack',
				'description' => 'Sticky stacked cards with a pinned left-hand navigation.',
			),
			'foundation-y-hero' => array(
				'class'       => Widgets\Y_Hero_Widget::class,
				'title'       => 'Y Hero',
				'description' => 'Split hero with stacked slider cards, stats, and CTA controls.',
			),
			'foundation-bounce-rail' => array(
				'class'       => Widgets\Bounce_Rail_Widget::class,
				'title'       => 'Social Wall',
				'description' => 'Scrollable rail with mixed blog stacks and YouTube reel columns.',
			),
			'foundation-process-carousel' => array(
				'class'       => Widgets\Process_Carousel_Widget::class,
				'title'       => 'Process Carousel',
				'description' => 'Tabbed process section with a card carousel and editable checklist steps.',
			),
			'foundation-portfolio-mosaic' => array(
				'class'       => Widgets\Portfolio_Mosaic_Widget::class,
				'title'       => 'Portfolio Grid',
				'description' => 'Editorial portfolio grid with responsive auto-fit columns, compact related mode, video support, and CTA insertion.',
			),
			'foundation-portfolio-mega-menu' => array(
				'class'       => Widgets\Portfolio_Mega_Menu_Widget::class,
				'title'       => 'Portfolio Menu',
				'description' => 'Compact portfolio mega-menu panel with latest work, menu links, and a project CTA card.',
			),
			'foundation-awards-recognition-wall' => array(
				'class'       => Widgets\Awards_Recognition_Wall_Widget::class,
				'title'       => 'Awards Wall',
				'description' => 'Static editorial awards wall with alternating feature blocks, image cards, and premium glass surfaces.',
			),
			'foundation-live-roles' => array(
				'class'       => Widgets\Live_Roles_Widget::class,
				'title'       => 'Live Roles',
				'description' => 'Mini careers hero with filterable live role rows, traffic-light status, and expandable details.',
			),
			'foundation-team-loop' => array(
				'class'       => Widgets\Team_Loop_Widget::class,
				'title'       => 'Team Grid',
				'description' => 'Filterable team grid with glass staff cards, department views, hierarchy ordering, and an optional live-roles feature card.',
			),
			'foundation-inkfire-linktree' => array(
				'class'       => Widgets\Inkfire_Linktree_Widget::class,
				'title'       => 'Linktree',
				'description' => 'Inkfire quicklinks hub with video, news, newsletter signup, reviews, and awards.',
			),
			'foundation-rubiks-gallery' => array(
				'class'       => Widgets\Rubiks_Gallery_Widget::class,
				'title'       => "Rubik's Gallery",
				'description' => 'Animated 2x2 mixed-media gallery with image, SVG, video, and Lottie support.',
			),
			'foundation-sender-newsletter' => array(
				'class'       => Widgets\Sender_Newsletter_Widget::class,
				'title'       => 'Newsletter Form',
				'description' => 'Newsletter signup form wired to the live Sender account and groups.',
			),
		);
	}

	/**
	 * File map for widget classes.
	 *
	 * @return array<string, string>
	 */
	public static function get_widget_file_map() {
		return array(
			'foundation-dark-animated-hero'      => 'includes/widgets/class-dark-animated-hero-widget.php',
			'foundation-mobile-header'           => 'includes/widgets/class-mobile-header-widget.php',
			'foundation-selector-stack'          => 'includes/widgets/class-selector-stack-widget.php',
			'foundation-y-hero'                  => 'includes/widgets/class-y-hero-widget.php',
			'foundation-bounce-rail'             => 'includes/widgets/class-bounce-rail-widget.php',
			'foundation-process-carousel'        => 'includes/widgets/class-process-carousel-widget.php',
			'foundation-portfolio-mosaic'        => 'includes/widgets/class-portfolio-mosaic-widget.php',
			'foundation-portfolio-mega-menu'     => 'includes/widgets/class-portfolio-mega-menu-widget.php',
			'foundation-awards-recognition-wall' => 'includes/widgets/class-awards-recognition-wall-widget.php',
			'foundation-live-roles'              => 'includes/widgets/class-live-roles-widget.php',
			'foundation-team-loop'               => 'includes/widgets/class-team-loop-widget.php',
			'foundation-inkfire-linktree'        => 'includes/widgets/class-inkfire-linktree-widget.php',
			'foundation-rubiks-gallery'          => 'includes/widgets/class-rubiks-gallery-widget.php',
			'foundation-sender-newsletter'       => 'includes/widgets/class-sender-newsletter-widget.php',
		);
	}

	/**
	 * Asset handle map keyed by widget id.
	 *
	 * @return array<string, array<string, array<int, string>>>
	 */
	public static function get_widget_asset_handles() {
		return array(
			'foundation-dark-animated-hero' => array(
				'styles'  => array( 'foundation-elementor-plus-core', 'foundation-elementor-plus-dark-animated-hero' ),
				'scripts' => array( 'foundation-elementor-plus-fluid-core', 'foundation-elementor-plus-dark-animated-hero' ),
			),
			'foundation-mobile-header' => array(
				'styles'  => array( 'foundation-elementor-plus-core', 'foundation-elementor-plus-mobile-header' ),
				'scripts' => array( 'foundation-elementor-plus-mobile-header' ),
			),
			'foundation-selector-stack' => array(
				'styles'  => array( 'foundation-elementor-plus-core', 'foundation-elementor-plus-selector-stack' ),
				'scripts' => array( 'foundation-elementor-plus-selector-stack' ),
			),
			'foundation-y-hero' => array(
				'styles'  => array( 'foundation-elementor-plus-core', 'foundation-elementor-plus-y-hero' ),
				'scripts' => array( 'foundation-elementor-plus-y-hero' ),
			),
			'foundation-bounce-rail' => array(
				'styles'  => array( 'foundation-elementor-plus-core', 'foundation-elementor-plus-bounce-rail' ),
				'scripts' => array( 'foundation-elementor-plus-bounce-rail' ),
			),
			'foundation-process-carousel' => array(
				'styles'  => array( 'foundation-elementor-plus-core', 'foundation-elementor-plus-process-carousel' ),
				'scripts' => array( 'foundation-elementor-plus-process-carousel' ),
			),
			'foundation-portfolio-mosaic' => array(
				'styles'  => array( 'foundation-elementor-plus-core', 'foundation-elementor-plus-portfolio-mosaic' ),
				'scripts' => array( 'foundation-elementor-plus-portfolio-mosaic' ),
			),
			'foundation-portfolio-mega-menu' => array(
				'styles'  => array( 'foundation-elementor-plus-core', 'foundation-elementor-plus-portfolio-mega-menu' ),
				'scripts' => array( 'foundation-elementor-plus-portfolio-mega-menu' ),
			),
			'foundation-awards-recognition-wall' => array(
				'styles'  => array( 'foundation-elementor-plus-core', 'foundation-elementor-plus-awards-wall' ),
				'scripts' => array( 'foundation-elementor-plus-awards-wall' ),
			),
			'foundation-live-roles' => array(
				'styles'  => array( 'foundation-elementor-plus-core', 'foundation-elementor-plus-live-roles' ),
				'scripts' => array( 'foundation-elementor-plus-live-roles' ),
			),
			'foundation-team-loop' => array(
				'styles'  => array( 'foundation-elementor-plus-core', 'foundation-elementor-plus-team-loop' ),
				'scripts' => array( 'foundation-elementor-plus-team-loop' ),
			),
			'foundation-inkfire-linktree' => array(
				'styles'  => array(),
				'scripts' => array(),
			),
			'foundation-rubiks-gallery' => array(
				'styles'  => array( 'foundation-elementor-plus-core', 'foundation-elementor-plus-rubiks-gallery' ),
				'scripts' => array( 'foundation-elementor-plus-lottie', 'foundation-elementor-plus-rubiks-gallery' ),
			),
			'foundation-sender-newsletter' => array(
				'styles'  => array( 'foundation-elementor-plus-core', 'foundation-elementor-plus-sender-newsletter' ),
				'scripts' => array( 'foundation-elementor-plus-sender-newsletter' ),
			),
		);
	}

	/**
	 * Collect the asset handles needed for the supplied widgets.
	 *
	 * @param array<int, string> $widget_ids Widget ids.
	 * @return array<string, array<int, string>>
	 */
	public static function get_asset_handles_for_widgets( array $widget_ids ) {
		$asset_map = self::get_widget_asset_handles();
		$handles   = array(
			'styles'  => array(),
			'scripts' => array(),
		);

		foreach ( $widget_ids as $widget_id ) {
			if ( empty( $asset_map[ $widget_id ] ) ) {
				continue;
			}

			$handles['styles']  = array_merge( $handles['styles'], $asset_map[ $widget_id ]['styles'] ?? array() );
			$handles['scripts'] = array_merge( $handles['scripts'], $asset_map[ $widget_id ]['scripts'] ?? array() );
		}

		$handles['styles']  = array_values( array_unique( array_filter( $handles['styles'] ) ) );
		$handles['scripts'] = array_values( array_unique( array_filter( $handles['scripts'] ) ) );

		return $handles;
	}

	/**
	 * Load widget class files on demand.
	 *
	 * @param array<int, string> $widget_ids Widget ids to load. Empty loads all.
	 * @return void
	 */
	public static function load_widget_class_files( array $widget_ids = array() ) {
		require_once FOUNDATION_ELEMENTOR_PLUS_PATH . 'includes/widgets/class-base-widget.php';

		$file_map = self::get_widget_file_map();

		if ( empty( $widget_ids ) ) {
			$widget_ids = array_keys( $file_map );
		}

		foreach ( $widget_ids as $widget_id ) {
			if ( 'foundation-dark-animated-hero' === $widget_id ) {
				require_once FOUNDATION_ELEMENTOR_PLUS_PATH . 'includes/class-dark-animated-hero-renderer.php';
			}

			if ( isset( $file_map[ $widget_id ] ) ) {
				require_once FOUNDATION_ELEMENTOR_PLUS_PATH . $file_map[ $widget_id ];
			}
		}
	}

	/**
	 * List of widget classes in this suite.
	 *
	 * @return array<int, string>
	 */
	public static function get_widget_classes() {
		self::load_widget_class_files();

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
	 * Shared asset registry.
	 *
	 * @return array<string, array<string, string>>
	 */
	public static function get_asset_map() {
		return array(
			'styles'  => array(
				'foundation-elementor-plus-core' => 'assets/css/foundation-core.css',
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
