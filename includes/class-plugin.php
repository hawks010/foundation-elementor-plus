<?php

namespace FoundationElementorPlus;

use Elementor\Elements_Manager;
use Elementor\Widgets_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once FOUNDATION_ELEMENTOR_PLUS_PATH . 'includes/class-widget-registry.php';

final class Plugin {
	/**
	 * Singleton instance.
	 *
	 * @var Plugin|null
	 */
	private static $instance = null;

	/**
	 * Elementor category slug.
	 */
	const CATEGORY_SLUG = 'foundation-elementor-plus';
	const FOUNDATION_PARENT_SLUG = 'foundation-by-inkfire';
	const MENU_SLUG = 'foundation-elementor-plus';
	const OPTION_KEY = 'foundation_elementor_plus_settings';

	/**
	 * Get singleton instance.
	 *
	 * @return Plugin
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Register bootstrap hook.
	 */
	private function __construct() {
		add_action( 'plugins_loaded', array( $this, 'bootstrap' ) );
	}

	/**
	 * Bootstrap the plugin.
	 */
	public function bootstrap() {
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_menu', array( $this, 'register_admin_menu' ), 20 );

		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', array( $this, 'render_missing_elementor_notice' ) );
			return;
		}

		add_action( 'elementor/frontend/after_register_styles', array( $this, 'register_style_assets' ) );
		add_action( 'elementor/frontend/after_register_scripts', array( $this, 'register_script_assets' ) );
		add_action( 'elementor/elements/categories_registered', array( $this, 'register_category' ) );
		add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );
		add_filter( 'the_content', array( $this, 'normalize_live_upload_urls' ), 20 );
		add_filter( 'elementor/frontend/the_content', array( $this, 'normalize_live_upload_urls' ), 20 );
	}

	/**
	 * Register widget styles.
	 */
	public function register_style_assets() {
		$assets = Widget_Registry::get_asset_map();

		foreach ( $assets['styles'] as $handle => $relative_path ) {
			if ( wp_style_is( $handle, 'registered' ) ) {
				continue;
			}

			$file_path = FOUNDATION_ELEMENTOR_PLUS_PATH . ltrim( $relative_path, '/' );
			$file_url  = FOUNDATION_ELEMENTOR_PLUS_URL . ltrim( $relative_path, '/' );

			wp_register_style(
				$handle,
				$file_url,
				array(),
				file_exists( $file_path ) ? (string) filemtime( $file_path ) : FOUNDATION_ELEMENTOR_PLUS_VERSION
			);
		}
	}

	/**
	 * Register widget scripts.
	 */
	public function register_script_assets() {
		$assets = Widget_Registry::get_asset_map();

		foreach ( $assets['scripts'] as $handle => $relative_path ) {
			if ( wp_script_is( $handle, 'registered' ) ) {
				continue;
			}

			$script_path = is_array( $relative_path ) ? ( $relative_path['path'] ?? '' ) : $relative_path;
			$script_deps = is_array( $relative_path ) ? ( $relative_path['deps'] ?? array() ) : array();
			$file_path   = FOUNDATION_ELEMENTOR_PLUS_PATH . ltrim( $script_path, '/' );
			$file_url    = FOUNDATION_ELEMENTOR_PLUS_URL . ltrim( $script_path, '/' );

			wp_register_script(
				$handle,
				$file_url,
				$script_deps,
				file_exists( $file_path ) ? (string) filemtime( $file_path ) : FOUNDATION_ELEMENTOR_PLUS_VERSION,
				true
			);
		}
	}

	/**
	 * Normalize legacy beta-hosted upload URLs on the live homepage without mutating builder data.
	 *
	 * @param string $content Rendered content.
	 * @return string
	 */
	public function normalize_live_upload_urls( $content ) {
		if ( ! is_string( $content ) || '' === $content ) {
			return $content;
		}

		if ( is_admin() ) {
			return $content;
		}

		if ( ! is_front_page() && ! is_home() && ! is_page( 30 ) ) {
			return $content;
		}

		$live_upload_base = trailingslashit( home_url( '/wp-content/uploads' ) );

		return str_replace(
			array(
				'https://beta.inkfire.co.uk/wp-content/uploads/',
				'http://beta.inkfire.co.uk/wp-content/uploads/',
			),
			$live_upload_base,
			$content
		);
	}

	/**
	 * Register Inkfire widget category.
	 *
	 * @param Elements_Manager $elements_manager Elementor elements manager.
	 */
	public function register_category( Elements_Manager $elements_manager ) {
		$settings = $this->get_settings();

		$elements_manager->add_category(
			self::CATEGORY_SLUG,
			array(
				'title' => esc_html( $settings['widget_category_label'] ),
				'icon'  => $settings['widget_category_icon'],
			)
		);
	}

	/**
	 * Register all plugin widgets.
	 *
	 * @param Widgets_Manager $widgets_manager Elementor widgets manager.
	 */
	public function register_widgets( Widgets_Manager $widgets_manager ) {
		foreach ( Widget_Registry::get_widget_classes() as $widget_class ) {
			$widgets_manager->register( new $widget_class() );
		}
	}

	/**
	 * Render admin notice when Elementor is unavailable.
	 */
	public function render_missing_elementor_notice() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		printf(
			'<div class="notice notice-warning"><p>%s</p></div>',
			esc_html__( 'Foundation Elementor Plus requires Elementor to be installed and active.', 'foundation-elementor-plus' )
		);
	}

	/**
	 * Register plugin settings.
	 */
	public function register_settings() {
		register_setting(
			'foundation_elementor_plus',
			self::OPTION_KEY,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize_settings' ),
				'default'           => $this->get_default_settings(),
			)
		);

		add_settings_section(
			'foundation_elementor_plus_general',
			esc_html__( 'Editor Settings', 'foundation-elementor-plus' ),
			array( $this, 'render_settings_section_intro' ),
			'foundation-elementor-plus'
		);

		add_settings_field(
			'widget_category_label',
			esc_html__( 'Widget Category Label', 'foundation-elementor-plus' ),
			array( $this, 'render_text_setting_field' ),
			'foundation-elementor-plus',
			'foundation_elementor_plus_general',
			array(
				'key'         => 'widget_category_label',
				'placeholder' => 'Foundation Plus',
				'description' => esc_html__( 'Shown in Elementor when editors browse your custom Foundation widgets.', 'foundation-elementor-plus' ),
			)
		);

		add_settings_field(
			'widget_category_icon',
			esc_html__( 'Widget Category Icon', 'foundation-elementor-plus' ),
			array( $this, 'render_text_setting_field' ),
			'foundation-elementor-plus',
			'foundation_elementor_plus_general',
			array(
				'key'         => 'widget_category_icon',
				'placeholder' => 'fa fa-plug',
				'description' => esc_html__( 'Elementor panel icon class for the Foundation Plus category.', 'foundation-elementor-plus' ),
			)
		);

		add_settings_section(
			'foundation_elementor_plus_integrations',
			esc_html__( 'Feed Integrations', 'foundation-elementor-plus' ),
			array( $this, 'render_integrations_section_intro' ),
			'foundation-elementor-plus'
		);

		add_settings_field(
			'youtube_api_key',
			esc_html__( 'YouTube API Key', 'foundation-elementor-plus' ),
			array( $this, 'render_text_setting_field' ),
			'foundation-elementor-plus',
			'foundation_elementor_plus_integrations',
			array(
				'key'         => 'youtube_api_key',
				'placeholder' => 'AIza...',
				'description' => esc_html__( 'Optional but recommended for reliable YouTube feed and description fetching.', 'foundation-elementor-plus' ),
			)
		);

		add_settings_field(
			'youtube_channel_source_default',
			esc_html__( 'Default YouTube Channel', 'foundation-elementor-plus' ),
			array( $this, 'render_text_setting_field' ),
			'foundation-elementor-plus',
			'foundation_elementor_plus_integrations',
			array(
				'key'         => 'youtube_channel_source_default',
				'placeholder' => '@inkfire or UC...',
				'description' => esc_html__( 'Used by Bounce Rail widgets when a widget-level YouTube channel is not set.', 'foundation-elementor-plus' ),
			)
		);

		add_settings_field(
			'instagram_access_token',
			esc_html__( 'Instagram Access Token', 'foundation-elementor-plus' ),
			array( $this, 'render_text_setting_field' ),
			'foundation-elementor-plus',
			'foundation_elementor_plus_integrations',
			array(
				'key'         => 'instagram_access_token',
				'placeholder' => 'IGQVJ...',
				'description' => esc_html__( 'Used for Instagram Reel feed requests from a connected Business or Creator account.', 'foundation-elementor-plus' ),
			)
		);

		add_settings_field(
			'instagram_business_account_id',
			esc_html__( 'Instagram Business Account ID', 'foundation-elementor-plus' ),
			array( $this, 'render_text_setting_field' ),
			'foundation-elementor-plus',
			'foundation_elementor_plus_integrations',
			array(
				'key'         => 'instagram_business_account_id',
				'placeholder' => '1784...',
				'description' => esc_html__( 'Used by Bounce Rail widgets when Instagram feed mode is enabled.', 'foundation-elementor-plus' ),
			)
		);
	}

	/**
	 * Register the Foundation submenu.
	 */
	public function register_admin_menu() {
		global $admin_page_hooks;

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( empty( $admin_page_hooks[ self::FOUNDATION_PARENT_SLUG ] ) ) {
			add_menu_page(
				esc_html__( 'Foundation', 'foundation-elementor-plus' ),
				esc_html__( 'Foundation', 'foundation-elementor-plus' ),
				'manage_options',
				self::FOUNDATION_PARENT_SLUG,
				array( $this, 'render_admin_page' ),
				'dashicons-hammer',
				30
			);
		}

		add_submenu_page(
			self::FOUNDATION_PARENT_SLUG,
			esc_html__( 'Foundation Elementor Plus', 'foundation-elementor-plus' ),
			esc_html__( 'Elementor Plus', 'foundation-elementor-plus' ),
			'manage_options',
			self::MENU_SLUG,
			array( $this, 'render_admin_page' )
		);

		remove_submenu_page( self::FOUNDATION_PARENT_SLUG, self::FOUNDATION_PARENT_SLUG );
	}

	/**
	 * Sanitize settings input.
	 *
	 * @param mixed $input Settings input.
	 * @return array<string, string>
	 */
	public function sanitize_settings( $input ) {
		$defaults = $this->get_default_settings();
		$input    = is_array( $input ) ? $input : array();

		return array(
			'widget_category_label' => isset( $input['widget_category_label'] ) && '' !== trim( (string) $input['widget_category_label'] )
				? sanitize_text_field( wp_unslash( (string) $input['widget_category_label'] ) )
				: $defaults['widget_category_label'],
			'widget_category_icon'  => isset( $input['widget_category_icon'] ) && '' !== trim( (string) $input['widget_category_icon'] )
				? preg_replace( '/[^a-z0-9\-\s_]/i', '', (string) wp_unslash( $input['widget_category_icon'] ) )
				: $defaults['widget_category_icon'],
			'youtube_api_key' => isset( $input['youtube_api_key'] )
				? sanitize_text_field( wp_unslash( (string) $input['youtube_api_key'] ) )
				: $defaults['youtube_api_key'],
			'youtube_channel_source_default' => isset( $input['youtube_channel_source_default'] )
				? sanitize_text_field( wp_unslash( (string) $input['youtube_channel_source_default'] ) )
				: $defaults['youtube_channel_source_default'],
			'instagram_access_token' => isset( $input['instagram_access_token'] )
				? sanitize_text_field( wp_unslash( (string) $input['instagram_access_token'] ) )
				: $defaults['instagram_access_token'],
			'instagram_business_account_id' => isset( $input['instagram_business_account_id'] )
				? sanitize_text_field( wp_unslash( (string) $input['instagram_business_account_id'] ) )
				: $defaults['instagram_business_account_id'],
		);
	}

	/**
	 * Render settings section description.
	 */
	public function render_settings_section_intro() {
		echo '<p>' . esc_html__( 'These settings control how Foundation Elementor Plus appears inside the Elementor editor. Widget content remains editable per widget instance in Elementor.', 'foundation-elementor-plus' ) . '</p>';
	}

	/**
	 * Render integrations section description.
	 */
	public function render_integrations_section_intro() {
		echo '<p>' . esc_html__( 'Optional API and account settings used by widgets such as Bounce Rail for YouTube and Instagram feeds. Widget-level values can still override these defaults when needed.', 'foundation-elementor-plus' ) . '</p>';
	}

	/**
	 * Render a text setting field.
	 *
	 * @param array<string, string> $args Field arguments.
	 */
	public function render_text_setting_field( $args ) {
		$settings    = $this->get_settings();
		$key         = $args['key'];
		$value       = isset( $settings[ $key ] ) ? $settings[ $key ] : '';
		$placeholder = isset( $args['placeholder'] ) ? $args['placeholder'] : '';
		$description = isset( $args['description'] ) ? $args['description'] : '';
		?>
		<input
			type="text"
			name="<?php echo esc_attr( self::OPTION_KEY . '[' . $key . ']' ); ?>"
			value="<?php echo esc_attr( $value ); ?>"
			placeholder="<?php echo esc_attr( $placeholder ); ?>"
			class="regular-text"
		/>
		<?php if ( $description ) : ?>
			<p class="description"><?php echo esc_html( $description ); ?></p>
		<?php endif; ?>
		<?php
	}

	/**
	 * Render the plugin admin page.
	 */
	public function render_admin_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', 'foundation-elementor-plus' ) );
		}

		$settings          = $this->get_settings();
		$widget_manifest   = Widget_Registry::get_widget_manifest();
		$elementor_ready   = did_action( 'elementor/loaded' );
		$plugin_file       = plugin_basename( FOUNDATION_ELEMENTOR_PLUS_FILE );
		$widget_count      = count( $widget_manifest );
		$settings_page_url = admin_url( 'admin.php?page=' . self::MENU_SLUG );

		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$is_active = is_plugin_active( $plugin_file );
		?>
		<div class="wrap foundation-elementor-plus-admin">
			<style>
				.foundation-elementor-plus-admin {
					max-width: 1120px;
				}
				.foundation-elementor-plus-admin__hero {
					margin-top: 24px;
					padding: 28px 32px;
					border-radius: 24px;
					background: linear-gradient(135deg, #13141F 0%, #1C1D2D 55%, #0E6055 100%);
					color: #F2F2F2;
					box-shadow: 0 18px 48px rgba(19, 20, 31, 0.22);
				}
				.foundation-elementor-plus-admin__hero h1 {
					margin: 0 0 8px;
					color: #F2F2F2;
					font-size: 28px;
				}
				.foundation-elementor-plus-admin__hero p {
					margin: 0;
					max-width: 720px;
					color: rgba(242, 242, 242, 0.82);
					font-size: 15px;
					line-height: 1.6;
				}
				.foundation-elementor-plus-admin__grid {
					display: grid;
					grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
					gap: 16px;
					margin: 24px 0;
				}
				.foundation-elementor-plus-admin__card,
				.foundation-elementor-plus-admin__panel {
					background: #fff;
					border: 1px solid #e7e9f2;
					border-radius: 20px;
					padding: 22px 24px;
					box-shadow: 0 10px 30px rgba(28, 29, 45, 0.06);
				}
				.foundation-elementor-plus-admin__eyebrow {
					display: inline-block;
					margin-bottom: 10px;
					font-size: 12px;
					font-weight: 700;
					letter-spacing: 0.08em;
					text-transform: uppercase;
					color: #0E6055;
				}
				.foundation-elementor-plus-admin__metric {
					margin: 0;
					font-size: 28px;
					font-weight: 700;
					color: #13141F;
				}
				.foundation-elementor-plus-admin__subtle {
					margin: 8px 0 0;
					color: #5c647a;
				}
				.foundation-elementor-plus-admin__layout {
					display: grid;
					grid-template-columns: minmax(0, 1.6fr) minmax(280px, 1fr);
					gap: 20px;
					align-items: start;
				}
				.foundation-elementor-plus-admin__widget-list {
					margin: 0;
					padding-left: 18px;
				}
				.foundation-elementor-plus-admin__widget-list li + li {
					margin-top: 12px;
				}
				.foundation-elementor-plus-admin__widget-list strong {
					display: block;
					margin-bottom: 4px;
					color: #13141F;
				}
				.foundation-elementor-plus-admin__actions {
					display: flex;
					flex-wrap: wrap;
					gap: 12px;
					margin-top: 18px;
				}
				.foundation-elementor-plus-admin .button.button-primary {
					background: #0E6055;
					border-color: #0E6055;
				}
				@media (max-width: 960px) {
					.foundation-elementor-plus-admin__layout {
						grid-template-columns: 1fr;
					}
				}
			</style>

			<div class="foundation-elementor-plus-admin__hero">
				<span class="foundation-elementor-plus-admin__eyebrow"><?php esc_html_e( 'Foundation Suite', 'foundation-elementor-plus' ); ?></span>
				<h1><?php esc_html_e( 'Foundation Elementor Plus', 'foundation-elementor-plus' ); ?></h1>
				<p><?php esc_html_e( 'A modular widget suite for Inkfire Foundation builds. Add custom Elementor widgets here over time while keeping their markup, styles, scripts, and editor controls safely managed in code.', 'foundation-elementor-plus' ); ?></p>
			</div>

			<div class="foundation-elementor-plus-admin__grid">
				<div class="foundation-elementor-plus-admin__card">
					<span class="foundation-elementor-plus-admin__eyebrow"><?php esc_html_e( 'Plugin Status', 'foundation-elementor-plus' ); ?></span>
					<p class="foundation-elementor-plus-admin__metric"><?php echo esc_html( $is_active ? __( 'Active', 'foundation-elementor-plus' ) : __( 'Inactive', 'foundation-elementor-plus' ) ); ?></p>
					<p class="foundation-elementor-plus-admin__subtle"><?php echo esc_html( 'v' . FOUNDATION_ELEMENTOR_PLUS_VERSION ); ?></p>
				</div>
				<div class="foundation-elementor-plus-admin__card">
					<span class="foundation-elementor-plus-admin__eyebrow"><?php esc_html_e( 'Elementor', 'foundation-elementor-plus' ); ?></span>
					<p class="foundation-elementor-plus-admin__metric"><?php echo esc_html( $elementor_ready ? __( 'Connected', 'foundation-elementor-plus' ) : __( 'Missing', 'foundation-elementor-plus' ) ); ?></p>
					<p class="foundation-elementor-plus-admin__subtle"><?php esc_html_e( 'Widget registration depends on Elementor being active.', 'foundation-elementor-plus' ); ?></p>
				</div>
				<div class="foundation-elementor-plus-admin__card">
					<span class="foundation-elementor-plus-admin__eyebrow"><?php esc_html_e( 'Available Widgets', 'foundation-elementor-plus' ); ?></span>
					<p class="foundation-elementor-plus-admin__metric"><?php echo esc_html( (string) $widget_count ); ?></p>
					<p class="foundation-elementor-plus-admin__subtle"><?php esc_html_e( 'This count grows as we add more custom Foundation layouts.', 'foundation-elementor-plus' ); ?></p>
				</div>
			</div>

			<div class="foundation-elementor-plus-admin__layout">
				<div class="foundation-elementor-plus-admin__panel">
					<h2><?php esc_html_e( 'Widget Suite', 'foundation-elementor-plus' ); ?></h2>
					<p><?php esc_html_e( 'Editors can find these widgets in Elementor under the custom Foundation Plus category.', 'foundation-elementor-plus' ); ?></p>
					<ul class="foundation-elementor-plus-admin__widget-list">
						<?php foreach ( $widget_manifest as $widget_id => $widget ) : ?>
							<li>
								<strong><?php echo esc_html( $widget['title'] ); ?></strong>
								<span><?php echo esc_html( $widget['description'] ); ?></span><br />
								<code><?php echo esc_html( $widget_id ); ?></code>
							</li>
						<?php endforeach; ?>
					</ul>
					<div class="foundation-elementor-plus-admin__actions">
						<a class="button button-primary" href="<?php echo esc_url( admin_url( 'edit.php?post_type=elementor_library' ) ); ?>"><?php esc_html_e( 'Open Elementor Library', 'foundation-elementor-plus' ); ?></a>
						<a class="button" href="<?php echo esc_url( $settings_page_url ); ?>"><?php esc_html_e( 'Refresh Status', 'foundation-elementor-plus' ); ?></a>
					</div>
				</div>

				<div class="foundation-elementor-plus-admin__panel">
					<h2><?php esc_html_e( 'Editor Settings', 'foundation-elementor-plus' ); ?></h2>
					<form action="options.php" method="post">
						<?php
						settings_fields( 'foundation_elementor_plus' );
						do_settings_sections( 'foundation-elementor-plus' );
						submit_button( __( 'Save Settings', 'foundation-elementor-plus' ) );
						?>
					</form>
					<p class="foundation-elementor-plus-admin__subtle">
						<?php
						printf(
							/* translators: 1: widget category label, 2: widget category icon class */
							esc_html__( 'Current editor category: %1$s (%2$s)', 'foundation-elementor-plus' ),
							esc_html( $settings['widget_category_label'] ),
							esc_html( $settings['widget_category_icon'] )
						);
						?>
					</p>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Get plugin default settings.
	 *
	 * @return array<string, string>
	 */
	private function get_default_settings() {
		return array(
			'widget_category_label'          => 'Foundation Plus',
			'widget_category_icon'           => 'fa fa-plug',
			'youtube_api_key'                => '',
			'youtube_channel_source_default' => '',
			'instagram_access_token'         => '',
			'instagram_business_account_id'  => '',
		);
	}

	/**
	 * Get merged plugin settings.
	 *
	 * @return array<string, string>
	 */
	private function get_settings() {
		$defaults = $this->get_default_settings();
		$stored   = get_option( self::OPTION_KEY, array() );

		if ( ! is_array( $stored ) ) {
			return $defaults;
		}

		return wp_parse_args( $stored, $defaults );
	}
}
