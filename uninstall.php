<?php
/**
 * Uninstall routine for Foundation Elementor Plus.
 *
 * Removes plugin-owned settings, counters, and transient caches without
 * touching Elementor content or saved widget data embedded in posts.
 *
 * To preserve plugin settings during uninstall, define the following in
 * wp-config.php before deleting the plugin:
 *
 * define( 'FOUNDATION_ELEMENTOR_PLUS_PRESERVE_DATA', true );
 *
 * @package FoundationElementorPlus
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

if ( defined( 'FOUNDATION_ELEMENTOR_PLUS_PRESERVE_DATA' ) && FOUNDATION_ELEMENTOR_PLUS_PRESERVE_DATA ) {
	return;
}

/**
 * Remove plugin-owned options and transients for the current site.
 *
 * @return void
 */
function foundation_elementor_plus_cleanup_site() {
	global $wpdb;

	$option_keys = array(
		'foundation_elementor_plus_settings',
		'foundation_elementor_plus_version',
		'foundation_dark_animated_hero_settings',
		'foundation_header_banner_settings',
		'foundation_team_inline_images',
		'amh_quicklinks_stats_v1',
	);

	foreach ( $option_keys as $option_key ) {
		delete_option( $option_key );
	}

	$transient_prefixes = array(
		'foundation_remote_media_',
		'foundation_youtube_feed_',
		'foundation_youtube_channel_',
		'foundation_instagram_feed_',
		'foundation_sender_newsletter_',
	);

	foreach ( $transient_prefixes as $prefix ) {
		$transient_like = $wpdb->esc_like( '_transient_' . $prefix ) . '%';
		$timeout_like   = $wpdb->esc_like( '_transient_timeout_' . $prefix ) . '%';

		$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s", $transient_like, $timeout_like ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	}
}

/**
 * Remove plugin-owned network transient caches.
 *
 * @return void
 */
function foundation_elementor_plus_cleanup_network_cache() {
	delete_site_transient( 'foundation_elementor_plus_github_release' );
	delete_site_transient( 'foundation_elementor_plus_github_readme_sections' );
}

if ( is_multisite() ) {
	$site_ids = get_sites(
		array(
			'fields' => 'ids',
			'number' => 0,
		)
	);

	foreach ( $site_ids as $site_id ) {
		switch_to_blog( (int) $site_id );
		foundation_elementor_plus_cleanup_site();
		restore_current_blog();
	}

	foundation_elementor_plus_cleanup_network_cache();
} else {
	foundation_elementor_plus_cleanup_site();
}
