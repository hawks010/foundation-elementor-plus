<?php
/**
 * Plugin Name: Foundation Elementor Plus
 * Description: Modular custom Elementor widgets for Foundation sites.
 * Version: 1.3.28
 * Author: Sonny x Inkfire
 * Text Domain: foundation-elementor-plus
 * Requires at least: 6.4
 * Requires PHP: 7.4
 * Requires Plugins: elementor
 * Update URI: https://github.com/hawks010/foundation-elementor-plus
 * Elementor tested up to: 4.0.1
 * Elementor Pro tested up to: 4.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'FOUNDATION_ELEMENTOR_PLUS_VERSION', '1.3.28' );
define( 'FOUNDATION_ELEMENTOR_PLUS_FILE', __FILE__ );
define( 'FOUNDATION_ELEMENTOR_PLUS_PATH', plugin_dir_path( __FILE__ ) );
define( 'FOUNDATION_ELEMENTOR_PLUS_URL', plugin_dir_url( __FILE__ ) );

require_once FOUNDATION_ELEMENTOR_PLUS_PATH . 'includes/class-header-banner.php';
require_once FOUNDATION_ELEMENTOR_PLUS_PATH . 'includes/class-hero-autoplay-toggle.php';
require_once FOUNDATION_ELEMENTOR_PLUS_PATH . 'includes/class-github-updater.php';
require_once FOUNDATION_ELEMENTOR_PLUS_PATH . 'includes/class-plugin.php';
require_once FOUNDATION_ELEMENTOR_PLUS_PATH . 'includes/class-sender-newsletter.php';
require_once FOUNDATION_ELEMENTOR_PLUS_PATH . 'includes/class-team-inline-images.php';
require_once FOUNDATION_ELEMENTOR_PLUS_PATH . 'includes/shortcodes/inkfire-linktree-shortcode.php';

\FoundationElementorPlus\Github_Updater::instance();
\FoundationElementorPlus\Plugin::instance();
( new \FoundationElementorPlus\Header_Banner() )->hooks();
( new \FoundationElementorPlus\Hero_Autoplay_Toggle() )->hooks();
( new \FoundationElementorPlus\Sender_Newsletter() )->hooks();
( new \FoundationElementorPlus\Team_Inline_Images() )->hooks();
