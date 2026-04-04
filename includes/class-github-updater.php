<?php

namespace FoundationElementorPlus;

use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Github_Updater {
	/**
	 * Singleton instance.
	 *
	 * @var Github_Updater|null
	 */
	private static $instance = null;

	/**
	 * Cached release payload key.
	 */
	const RELEASE_CACHE_KEY = 'foundation_elementor_plus_github_release';

	/**
	 * Cached readme section payload key.
	 */
	const README_CACHE_KEY = 'foundation_elementor_plus_github_readme_sections';

	/**
	 * Default cache lifetime.
	 */
	const CACHE_TTL = 6 * HOUR_IN_SECONDS;

	/**
	 * Get singleton instance.
	 *
	 * @return Github_Updater
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Register updater hooks.
	 */
	private function __construct() {
		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'inject_update' ) );
		add_filter( 'plugins_api', array( $this, 'filter_plugin_information' ), 20, 3 );
		add_filter( 'upgrader_pre_download', array( $this, 'download_private_package' ), 10, 4 );
		add_filter( 'upgrader_source_selection', array( $this, 'normalize_package_source' ), 10, 4 );
		add_action( 'upgrader_process_complete', array( $this, 'clear_cached_release' ), 10, 2 );
	}

	/**
	 * Inject update metadata into the plugin update transient.
	 *
	 * @param object $transient Update transient.
	 * @return object
	 */
	public function inject_update( $transient ) {
		if ( ! is_object( $transient ) || empty( $transient->checked ) || ! is_array( $transient->checked ) ) {
			return $transient;
		}

		$plugin_basename = $this->get_plugin_basename();

		if ( empty( $transient->checked[ $plugin_basename ] ) ) {
			return $transient;
		}

		$release = $this->get_release_data();

		if ( is_wp_error( $release ) || empty( $release['version'] ) ) {
			return $transient;
		}

		$plugin_data = $this->get_plugin_header_data();
		$update_item = (object) array(
			'id'            => $this->get_update_uri(),
			'slug'          => $this->get_plugin_slug(),
			'plugin'        => $plugin_basename,
			'new_version'   => $release['version'],
			'url'           => $release['homepage'],
			'package'       => $release['package'],
			'tested'        => ! empty( $plugin_data['tested'] ) ? $plugin_data['tested'] : '',
			'requires_php'  => ! empty( $plugin_data['requires_php'] ) ? $plugin_data['requires_php'] : '',
			'requires'      => ! empty( $plugin_data['requires_at_least'] ) ? $plugin_data['requires_at_least'] : '',
		);

		if ( version_compare( (string) $release['version'], (string) $transient->checked[ $plugin_basename ], '>' ) ) {
			$transient->response[ $plugin_basename ] = $update_item;
		} else {
			$transient->no_update[ $plugin_basename ] = $update_item;
		}

		return $transient;
	}

	/**
	 * Provide plugin information for the WordPress update modal.
	 *
	 * @param false|object|array $result Current result.
	 * @param string             $action Requested action.
	 * @param object             $args Plugin API arguments.
	 * @return false|object|array
	 */
	public function filter_plugin_information( $result, $action, $args ) {
		if ( 'plugin_information' !== $action || ! is_object( $args ) ) {
			return $result;
		}

		$requested_slug = isset( $args->slug ) ? (string) $args->slug : '';

		if ( $this->get_plugin_slug() !== $requested_slug ) {
			return $result;
		}

		$release = $this->get_release_data();

		if ( is_wp_error( $release ) ) {
			return $result;
		}

		$plugin_data = $this->get_plugin_header_data();
		$sections    = $this->get_readme_sections();

		return (object) array(
			'name'          => ! empty( $plugin_data['name'] ) ? $plugin_data['name'] : 'Foundation Elementor Plus',
			'slug'          => $this->get_plugin_slug(),
			'plugin_name'   => ! empty( $plugin_data['name'] ) ? $plugin_data['name'] : 'Foundation Elementor Plus',
			'version'       => $release['version'],
			'author'        => ! empty( $plugin_data['author'] ) ? $plugin_data['author'] : 'Sonny x Inkfire',
			'homepage'      => $release['homepage'],
			'requires'      => ! empty( $plugin_data['requires_at_least'] ) ? $plugin_data['requires_at_least'] : '',
			'requires_php'  => ! empty( $plugin_data['requires_php'] ) ? $plugin_data['requires_php'] : '',
			'tested'        => ! empty( $plugin_data['tested'] ) ? $plugin_data['tested'] : '',
			'last_updated'  => ! empty( $release['published_at'] ) ? $release['published_at'] : '',
			'download_link' => $release['package'],
			'sections'      => $sections,
			'banners'       => array(),
		);
	}

	/**
	 * Ensure GitHub zipball updates install back into the expected plugin folder.
	 *
	 * @param string        $source Source directory.
	 * @param string        $remote_source Remote source base path.
	 * @param \WP_Upgrader  $upgrader Upgrader instance.
	 * @param array         $hook_extra Upgrade context.
	 * @return string|WP_Error
	 */
	public function normalize_package_source( $source, $remote_source, $upgrader, $hook_extra ) {
		if ( is_wp_error( $source ) ) {
			return $source;
		}

		if ( empty( $hook_extra['plugin'] ) || $this->get_plugin_basename() !== $hook_extra['plugin'] ) {
			return $source;
		}

		$expected_directory = basename( dirname( FOUNDATION_ELEMENTOR_PLUS_FILE ) );

		if ( $expected_directory === basename( $source ) ) {
			return $source;
		}

		global $wp_filesystem;

		if ( ! $wp_filesystem ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
			WP_Filesystem();
		}

		if ( ! $wp_filesystem ) {
			return $source;
		}

		$corrected_source = trailingslashit( $remote_source ) . $expected_directory;

		if ( $wp_filesystem->exists( $corrected_source ) ) {
			$wp_filesystem->delete( $corrected_source, true );
		}

		if ( ! $wp_filesystem->move( $source, $corrected_source, true ) ) {
			return new WP_Error(
				'foundation_elementor_plus_github_update_move_failed',
				__( 'Foundation Elementor Plus could not place the GitHub package into the expected plugin directory.', 'foundation-elementor-plus' )
			);
		}

		return $corrected_source;
	}

	/**
	 * Download private GitHub packages with authentication when needed.
	 *
	 * @param false|mixed   $reply Existing download reply.
	 * @param string        $package Package URL.
	 * @param \WP_Upgrader  $upgrader Upgrader instance.
	 * @param array         $hook_extra Upgrade context.
	 * @return false|string|WP_Error
	 */
	public function download_private_package( $reply, $package, $upgrader, $hook_extra ) {
		if ( false !== $reply ) {
			return $reply;
		}

		$token = $this->get_auth_token();

		if ( '' === $token ) {
			return $reply;
		}

		if ( empty( $hook_extra['plugin'] ) || $this->get_plugin_basename() !== $hook_extra['plugin'] ) {
			return $reply;
		}

		if ( false === strpos( (string) $package, $this->get_repo_api_base() . '/zipball/' ) ) {
			return $reply;
		}

		$temp_file = wp_tempnam( 'foundation-elementor-plus.zip' );

		if ( ! $temp_file ) {
			return new WP_Error(
				'foundation_elementor_plus_github_tempfile_failed',
				__( 'Foundation Elementor Plus could not create a temporary file for the GitHub update package.', 'foundation-elementor-plus' )
			);
		}

		$response = wp_remote_get(
			$package,
			array(
				'headers' => array(
					'Accept'        => 'application/vnd.github+json',
					'Authorization' => 'Bearer ' . $token,
					'User-Agent'    => 'Foundation Elementor Plus/' . FOUNDATION_ELEMENTOR_PLUS_VERSION,
				),
				'timeout' => 60,
				'stream'  => true,
				'filename' => $temp_file,
			)
		);

		if ( is_wp_error( $response ) ) {
			@unlink( $temp_file ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			return $response;
		}

		if ( 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
			@unlink( $temp_file ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			return new WP_Error(
				'foundation_elementor_plus_github_package_failed',
				__( 'Foundation Elementor Plus could not download the authenticated GitHub update package.', 'foundation-elementor-plus' )
			);
		}

		return $temp_file;
	}

	/**
	 * Clear cached metadata after plugin updates.
	 *
	 * @param \WP_Upgrader $upgrader Upgrader instance.
	 * @param array        $hook_extra Upgrade context.
	 * @return void
	 */
	public function clear_cached_release( $upgrader, $hook_extra ) {
		if ( empty( $hook_extra['type'] ) || 'plugin' !== $hook_extra['type'] ) {
			return;
		}

		$plugins = array();

		if ( ! empty( $hook_extra['plugins'] ) && is_array( $hook_extra['plugins'] ) ) {
			$plugins = $hook_extra['plugins'];
		} elseif ( ! empty( $hook_extra['plugin'] ) ) {
			$plugins = array( $hook_extra['plugin'] );
		}

		if ( ! in_array( $this->get_plugin_basename(), $plugins, true ) ) {
			return;
		}

		delete_site_transient( self::RELEASE_CACHE_KEY );
		delete_site_transient( self::README_CACHE_KEY );
	}

	/**
	 * Fetch and cache release metadata from GitHub.
	 *
	 * @return array<string, string>|WP_Error
	 */
	private function get_release_data() {
		$cached = get_site_transient( self::RELEASE_CACHE_KEY );

		if ( is_array( $cached ) && ! empty( $cached['version'] ) ) {
			return $cached;
		}

		$release_data = $this->request_json( $this->get_repo_api_base() . '/releases/latest' );

		if ( is_wp_error( $release_data ) ) {
			$release_data = $this->get_tag_fallback_data();
		}

		if ( is_wp_error( $release_data ) ) {
			return $release_data;
		}

		set_site_transient( self::RELEASE_CACHE_KEY, $release_data, self::CACHE_TTL );

		return $release_data;
	}

	/**
	 * Build release data from the latest available GitHub tag.
	 *
	 * @return array<string, string>|WP_Error
	 */
	private function get_tag_fallback_data() {
		$response = $this->request_json( $this->get_repo_api_base() . '/tags?per_page=1' );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		if ( empty( $response[0]['name'] ) ) {
			return new WP_Error(
				'foundation_elementor_plus_github_no_release',
				__( 'Foundation Elementor Plus could not find a GitHub release or tag to update from.', 'foundation-elementor-plus' )
			);
		}

		$tag_name = (string) $response[0]['name'];

		return array(
			'version'      => ltrim( $tag_name, "vV \t\n\r\0\x0B" ),
			'tag_name'     => $tag_name,
			'homepage'     => $this->get_repo_html_url() . '/releases/tag/' . rawurlencode( $tag_name ),
			'package'      => ! empty( $response[0]['zipball_url'] ) ? (string) $response[0]['zipball_url'] : $this->get_repo_api_base() . '/zipball/' . rawurlencode( $tag_name ),
			'published_at' => gmdate( 'Y-m-d\TH:i:s\Z' ),
			'body'         => '',
		);
	}

	/**
	 * Read and cache sections from the packaged readme.
	 *
	 * @return array<string, string>
	 */
	private function get_readme_sections() {
		$cached = get_site_transient( self::README_CACHE_KEY );

		if ( is_array( $cached ) && ! empty( $cached ) ) {
			return $cached;
		}

		$sections = array(
			'description' => '',
			'installation' => '',
			'changelog'   => '',
		);

		$readme_path = FOUNDATION_ELEMENTOR_PLUS_PATH . 'readme.txt';

		if ( ! file_exists( $readme_path ) || ! is_readable( $readme_path ) ) {
			return $sections;
		}

		$contents = file_get_contents( $readme_path );

		if ( false === $contents || '' === trim( $contents ) ) {
			return $sections;
		}

		$matches = array();
		preg_match_all( '/==\s*(.+?)\s*==\s*(.*?)(?=\n==\s*.+?\s*==|\z)/si', $contents, $matches, PREG_SET_ORDER );

		foreach ( $matches as $match ) {
			$section_name = strtolower( trim( $match[1] ) );
			$section_body = trim( $match[2] );

			if ( 'description' === $section_name ) {
				$sections['description'] = wp_kses_post( nl2br( esc_html( $section_body ) ) );
			}

			if ( 'installation' === $section_name ) {
				$sections['installation'] = wp_kses_post( nl2br( esc_html( $section_body ) ) );
			}

			if ( 'changelog' === $section_name ) {
				$sections['changelog'] = wp_kses_post( nl2br( esc_html( $section_body ) ) );
			}
		}

		set_site_transient( self::README_CACHE_KEY, $sections, self::CACHE_TTL );

		return $sections;
	}

	/**
	 * Fetch plugin headers used in the update UI.
	 *
	 * @return array<string, string>
	 */
	private function get_plugin_header_data() {
		$data = get_file_data(
			FOUNDATION_ELEMENTOR_PLUS_FILE,
			array(
				'name'             => 'Plugin Name',
				'author'           => 'Author',
				'tested'           => 'Tested up to',
				'requires_php'     => 'Requires PHP',
				'requires_at_least' => 'Requires at least',
			),
			'plugin'
		);

		return is_array( $data ) ? $data : array();
	}

	/**
	 * Make a GitHub API request and decode JSON responses.
	 *
	 * @param string $url GitHub API URL.
	 * @return array<string, mixed>|array<int, mixed>|WP_Error
	 */
	private function request_json( $url ) {
		$headers = array(
			'Accept'     => 'application/vnd.github+json',
			'User-Agent' => 'Foundation Elementor Plus/' . FOUNDATION_ELEMENTOR_PLUS_VERSION,
		);

		$token = $this->get_auth_token();

		if ( '' !== $token ) {
			$headers['Authorization'] = 'Bearer ' . $token;
		}

		$response = wp_remote_get(
			$url,
			array(
				'headers' => $headers,
				'timeout' => 15,
			)
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$status_code = (int) wp_remote_retrieve_response_code( $response );
		$body        = (string) wp_remote_retrieve_body( $response );

		if ( 200 !== $status_code || '' === $body ) {
			return new WP_Error(
				'foundation_elementor_plus_github_request_failed',
				sprintf(
					/* translators: %d: HTTP status code. */
					__( 'Foundation Elementor Plus GitHub update request failed with status %d.', 'foundation-elementor-plus' ),
					$status_code
				)
			);
		}

		$data = json_decode( $body, true );

		if ( ! is_array( $data ) ) {
			return new WP_Error(
				'foundation_elementor_plus_github_invalid_json',
				__( 'Foundation Elementor Plus received an invalid GitHub response.', 'foundation-elementor-plus' )
			);
		}

		if ( isset( $data['tag_name'] ) ) {
			return array(
				'version'      => ltrim( (string) $data['tag_name'], "vV \t\n\r\0\x0B" ),
				'tag_name'     => (string) $data['tag_name'],
				'homepage'     => ! empty( $data['html_url'] ) ? (string) $data['html_url'] : $this->get_repo_html_url(),
				'package'      => ! empty( $data['zipball_url'] ) ? (string) $data['zipball_url'] : $this->get_repo_api_base() . '/zipball/' . rawurlencode( (string) $data['tag_name'] ),
				'published_at' => ! empty( $data['published_at'] ) ? (string) $data['published_at'] : '',
				'body'         => ! empty( $data['body'] ) ? (string) $data['body'] : '',
			);
		}

		return $data;
	}

	/**
	 * Get the configured GitHub auth token.
	 *
	 * @return string
	 */
	private function get_auth_token() {
		$token = '';

		if ( defined( 'FOUNDATION_ELEMENTOR_PLUS_GITHUB_TOKEN' ) && is_string( FOUNDATION_ELEMENTOR_PLUS_GITHUB_TOKEN ) ) {
			$token = trim( FOUNDATION_ELEMENTOR_PLUS_GITHUB_TOKEN );
		}

		$token = apply_filters( 'foundation_elementor_plus_github_token', $token );

		return is_string( $token ) ? trim( $token ) : '';
	}

	/**
	 * Get the configured repository slug.
	 *
	 * @return string
	 */
	private function get_repository() {
		$repository = apply_filters( 'foundation_elementor_plus_github_repository', 'hawks010/foundation-elementor-plus' );

		return is_string( $repository ) ? trim( $repository ) : 'hawks010/foundation-elementor-plus';
	}

	/**
	 * Get the GitHub API base URL.
	 *
	 * @return string
	 */
	private function get_repo_api_base() {
		return 'https://api.github.com/repos/' . $this->get_repository();
	}

	/**
	 * Get the GitHub repository homepage URL.
	 *
	 * @return string
	 */
	private function get_repo_html_url() {
		return 'https://github.com/' . $this->get_repository();
	}

	/**
	 * Get the plugin update URI.
	 *
	 * @return string
	 */
	private function get_update_uri() {
		return $this->get_repo_html_url();
	}

	/**
	 * Get the plugin basename.
	 *
	 * @return string
	 */
	private function get_plugin_basename() {
		return plugin_basename( FOUNDATION_ELEMENTOR_PLUS_FILE );
	}

	/**
	 * Get the plugin slug.
	 *
	 * @return string
	 */
	private function get_plugin_slug() {
		return dirname( $this->get_plugin_basename() );
	}
}
