<?php
/**
 * Uninstall routine for Foundation Elementor Plus.
 *
 * @package FoundationElementorPlus
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'foundation_elementor_plus_settings' );
