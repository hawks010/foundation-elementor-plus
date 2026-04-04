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
	const MINIMUM_ELEMENTOR_VERSION = '3.20.0';
	const MINIMUM_PHP_VERSION = '7.4';
	const VERSION_OPTION = 'foundation_elementor_plus_version';
	const CLEAR_CACHE_ACTION = 'foundation_elementor_plus_clear_cache';

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
	 *
	 * @return void
	 */
	public function bootstrap() {
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_menu', array( $this, 'register_admin_menu' ), 20 );
		add_action( 'admin_post_' . self::CLEAR_CACHE_ACTION, array( $this, 'handle_clear_cache' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );

		if ( ! $this->is_compatible() ) {
			return;
		}

		$this->maybe_run_upgrade();

		add_action( 'elementor/frontend/after_register_styles', array( $this, 'register_style_assets' ) );
		add_action( 'elementor/frontend/after_register_scripts', array( $this, 'register_script_assets' ) );
		add_action( 'elementor/elements/categories_registered', array( $this, 'register_category' ) );
		add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );
		add_filter( 'the_content', array( $this, 'normalize_live_upload_urls' ), 20 );
		add_filter( 'elementor/frontend/the_content', array( $this, 'normalize_live_upload_urls' ), 20 );
	}

	/**
	 * Handle one-time upgrade tasks.
	 *
	 * @return void
	 */
	private function maybe_run_upgrade() {
		$installed_version = get_option( self::VERSION_OPTION, '' );

		if ( FOUNDATION_ELEMENTOR_PLUS_VERSION === $installed_version ) {
			return;
		}

		update_option( self::VERSION_OPTION, FOUNDATION_ELEMENTOR_PLUS_VERSION, false );
		$this->clear_elementor_cache();
	}

	/**
	 * Clear Elementor generated assets/cache when available.
	 *
	 * @return void
	 */
	private function clear_elementor_cache() {
		if ( class_exists( '\\Elementor\\Plugin' ) && isset( \Elementor\Plugin::$instance->files_manager ) && is_object( \Elementor\Plugin::$instance->files_manager ) && method_exists( \Elementor\Plugin::$instance->files_manager, 'clear_cache' ) ) {
			\Elementor\Plugin::$instance->files_manager->clear_cache();
		}
	}

	/**
	 * Check whether the current site environment is compatible with the addon.
	 *
	 * @return bool
	 */
	private function is_compatible() {
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', array( $this, 'render_missing_elementor_notice' ) );
			return false;
		}

		if ( defined( 'ELEMENTOR_VERSION' ) && version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '<' ) ) {
			add_action( 'admin_notices', array( $this, 'render_minimum_elementor_version_notice' ) );
			return false;
		}

		if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
			add_action( 'admin_notices', array( $this, 'render_minimum_php_version_notice' ) );
			return false;
		}

		return true;
	}

	/**
	 * Register widget styles.
	 *
	 * @return void
	 */
	public function register_style_assets() {
		$assets        = Widget_Registry::get_asset_map();
		$asset_handles = Widget_Registry::get_asset_handles_for_widgets( $this->get_enabled_widget_ids() );

		foreach ( $asset_handles['styles'] as $handle ) {
			if ( empty( $assets['styles'][ $handle ] ) ) {
				continue;
			}

			if ( wp_style_is( $handle, 'registered' ) ) {
				continue;
			}

			$relative_path = $assets['styles'][ $handle ];
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
	 *
	 * @return void
	 */
	public function register_script_assets() {
		$assets               = Widget_Registry::get_asset_map();
		$settings             = $this->get_settings();
		$should_defer_scripts = 'yes' === $settings['defer_widget_scripts'];
		$asset_handles        = Widget_Registry::get_asset_handles_for_widgets( $this->get_enabled_widget_ids() );

		foreach ( $asset_handles['scripts'] as $handle ) {
			if ( empty( $assets['scripts'][ $handle ] ) ) {
				continue;
			}

			if ( wp_script_is( $handle, 'registered' ) ) {
				continue;
			}

			$relative_path = $assets['scripts'][ $handle ];
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

			if ( $should_defer_scripts ) {
				wp_script_add_data( $handle, 'strategy', 'defer' );
			}
		}
	}

	/**
	 * Normalize legacy beta-hosted upload URLs without mutating builder data.
	 *
	 * @param string $content Rendered content.
	 * @return string
	 */
	public function normalize_live_upload_urls( $content ) {
		if ( ! is_string( $content ) || '' === $content ) {
			return $content;
		}

		$settings = $this->get_settings();

		if ( 'yes' !== $settings['enable_legacy_upload_normalization'] ) {
			return $content;
		}

		$is_elementor_preview = $this->is_elementor_editor_or_preview();

		if ( is_admin() && ! $is_elementor_preview ) {
			return $content;
		}

		if ( ! $is_elementor_preview && ! is_front_page() && ! is_home() && ! is_page( 30 ) ) {
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
	 * Determine whether Elementor is rendering an editor or preview request.
	 *
	 * @return bool
	 */
	private function is_elementor_editor_or_preview() {
		if ( ! class_exists( '\\Elementor\\Plugin' ) ) {
			return false;
		}

		$plugin = \Elementor\Plugin::$instance;

		if ( isset( $plugin->editor ) && method_exists( $plugin->editor, 'is_edit_mode' ) && $plugin->editor->is_edit_mode() ) {
			return true;
		}

		if ( isset( $plugin->preview ) && method_exists( $plugin->preview, 'is_preview_mode' ) && $plugin->preview->is_preview_mode() ) {
			return true;
		}

		return false;
	}

	/**
	 * Register Inkfire widget category.
	 *
	 * @param Elements_Manager $elements_manager Elementor elements manager.
	 * @return void
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
	 * Register all enabled plugin widgets.
	 *
	 * @param Widgets_Manager $widgets_manager Elementor widgets manager.
	 * @return void
	 */
	public function register_widgets( Widgets_Manager $widgets_manager ) {
		$manifest           = Widget_Registry::get_widget_manifest();
		$enabled_widget_ids = $this->get_enabled_widget_ids();

		if ( empty( $enabled_widget_ids ) ) {
			return;
		}

		Widget_Registry::load_widget_class_files( $enabled_widget_ids );

		foreach ( $enabled_widget_ids as $widget_id ) {
			if ( empty( $manifest[ $widget_id ]['class'] ) ) {
				continue;
			}

			$widget_class = $manifest[ $widget_id ]['class'];

			if ( class_exists( $widget_class ) ) {
				$widgets_manager->register( new $widget_class() );
			}
		}
	}

	/**
	 * Render admin notice when Elementor is unavailable.
	 *
	 * @return void
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
	 * Render admin notice when Elementor is below the supported minimum version.
	 *
	 * @return void
	 */
	public function render_minimum_elementor_version_notice() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		printf(
			'<div class="notice notice-warning"><p>%s</p></div>',
			esc_html(
				sprintf(
					/* translators: %s: minimum Elementor version */
					__( 'Foundation Elementor Plus requires Elementor version %s or greater.', 'foundation-elementor-plus' ),
					self::MINIMUM_ELEMENTOR_VERSION
				)
			)
		);
	}

	/**
	 * Render admin notice when PHP is below the supported minimum version.
	 *
	 * @return void
	 */
	public function render_minimum_php_version_notice() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		printf(
			'<div class="notice notice-warning"><p>%s</p></div>',
			esc_html(
				sprintf(
					/* translators: %s: minimum PHP version */
					__( 'Foundation Elementor Plus requires PHP version %s or greater.', 'foundation-elementor-plus' ),
					self::MINIMUM_PHP_VERSION
				)
			)
		);
	}

	/**
	 * Register plugin settings.
	 *
	 * @return void
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
			'foundation_elementor_plus_performance',
			esc_html__( 'Performance & Loading', 'foundation-elementor-plus' ),
			array( $this, 'render_performance_section_intro' ),
			'foundation-elementor-plus'
		);

		add_settings_field(
			'defer_widget_scripts',
			esc_html__( 'Defer Widget Scripts', 'foundation-elementor-plus' ),
			array( $this, 'render_toggle_setting_field' ),
			'foundation-elementor-plus',
			'foundation_elementor_plus_performance',
			array(
				'key'         => 'defer_widget_scripts',
				'label'       => esc_html__( 'Load frontend widget JavaScript with defer when possible', 'foundation-elementor-plus' ),
				'description' => esc_html__( 'Keeps scripts out of the critical path. Disable only if a specific widget script proves timing-sensitive on your site.', 'foundation-elementor-plus' ),
			)
		);

		add_settings_field(
			'enable_legacy_upload_normalization',
			esc_html__( 'Legacy Upload URL Fix', 'foundation-elementor-plus' ),
			array( $this, 'render_toggle_setting_field' ),
			'foundation-elementor-plus',
			'foundation_elementor_plus_performance',
			array(
				'key'         => 'enable_legacy_upload_normalization',
				'label'       => esc_html__( 'Rewrite old beta upload URLs in Foundation content', 'foundation-elementor-plus' ),
				'description' => esc_html__( 'Useful while older builder data still references beta.inkfire.co.uk uploads. Turn off once all content is clean.', 'foundation-elementor-plus' ),
			)
		);

		add_settings_field(
			'enabled_widgets',
			esc_html__( 'Enabled Widgets', 'foundation-elementor-plus' ),
			array( $this, 'render_widget_toggle_field' ),
			'foundation-elementor-plus',
			'foundation_elementor_plus_performance',
			array(
				'key'         => 'enabled_widgets',
				'description' => esc_html__( 'Disable widgets you are not using so they do not register in Elementor or load their assets on the front end. Existing pages that use a disabled widget will need it re-enabled to render again.', 'foundation-elementor-plus' ),
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
	 *
	 * @return void
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
	 * @return array<string, mixed>
	 */
	public function sanitize_settings( $input ) {
		$defaults       = $this->get_default_settings();
		$input          = is_array( $input ) ? $input : array();
		$manifest            = Widget_Registry::get_widget_manifest();
		$valid_widget_ids    = array_keys( $manifest );
		$widgets_were_posted = isset( $input['enabled_widgets_present'] );
		$enabled_widgets     = isset( $input['enabled_widgets'] ) && is_array( $input['enabled_widgets'] )
			? array_values( array_intersect( $valid_widget_ids, array_map( 'sanitize_key', wp_unslash( $input['enabled_widgets'] ) ) ) )
			: ( $widgets_were_posted ? array() : $defaults['enabled_widgets'] );

		if ( empty( $enabled_widgets ) ) {
			$enabled_widgets = array();
		}

		$this->clear_elementor_cache();

		return array(
			'widget_category_label' => isset( $input['widget_category_label'] ) && '' !== trim( (string) $input['widget_category_label'] )
				? sanitize_text_field( wp_unslash( (string) $input['widget_category_label'] ) )
				: $defaults['widget_category_label'],
			'widget_category_icon'  => isset( $input['widget_category_icon'] ) && '' !== trim( (string) $input['widget_category_icon'] )
				? preg_replace( '/[^a-z0-9\-\s_]/i', '', (string) wp_unslash( $input['widget_category_icon'] ) )
				: $defaults['widget_category_icon'],
			'defer_widget_scripts' => isset( $input['defer_widget_scripts'] ) && 'yes' === wp_unslash( (string) $input['defer_widget_scripts'] )
				? 'yes'
				: 'no',
			'enable_legacy_upload_normalization' => isset( $input['enable_legacy_upload_normalization'] ) && 'yes' === wp_unslash( (string) $input['enable_legacy_upload_normalization'] )
				? 'yes'
				: 'no',
			'enabled_widgets' => $enabled_widgets,
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
	 *
	 * @return void
	 */
	public function render_settings_section_intro() {
		echo '<p>' . esc_html__( 'These settings control how Foundation Elementor Plus appears inside the Elementor editor. Widget content remains editable per widget instance in Elementor.', 'foundation-elementor-plus' ) . '</p>';
	}

	/**
	 * Render performance section description.
	 *
	 * @return void
	 */
	public function render_performance_section_intro() {
		echo '<p>' . esc_html__( 'The plugin already loads widget CSS and JavaScript only when a widget is used. Use the switches below to keep the suite leaner in production and to disable widgets your site never uses.', 'foundation-elementor-plus' ) . '</p>';
	}

	/**
	 * Render integrations section description.
	 *
	 * @return void
	 */
	public function render_integrations_section_intro() {
		echo '<p>' . esc_html__( 'Optional API and account settings used by widgets such as Bounce Rail for YouTube and Instagram feeds. Widget-level values can still override these defaults when needed.', 'foundation-elementor-plus' ) . '</p>';
	}

	/**
	 * Render a text setting field.
	 *
	 * @param array<string, string> $args Field arguments.
	 * @return void
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
			value="<?php echo esc_attr( is_array( $value ) ? '' : (string) $value ); ?>"
			placeholder="<?php echo esc_attr( $placeholder ); ?>"
			class="regular-text"
		/>
		<?php if ( $description ) : ?>
			<p class="description"><?php echo esc_html( $description ); ?></p>
		<?php endif; ?>
		<?php
	}

	/**
	 * Render a yes/no checkbox style field.
	 *
	 * @param array<string, string> $args Field arguments.
	 * @return void
	 */
	public function render_toggle_setting_field( $args ) {
		$settings    = $this->get_settings();
		$key         = $args['key'];
		$label       = isset( $args['label'] ) ? $args['label'] : '';
		$description = isset( $args['description'] ) ? $args['description'] : '';
		$is_checked  = isset( $settings[ $key ] ) && 'yes' === $settings[ $key ];
		?>
		<label>
			<input
				type="checkbox"
				name="<?php echo esc_attr( self::OPTION_KEY . '[' . $key . ']' ); ?>"
				value="yes"
				<?php checked( $is_checked ); ?>
			/>
			<?php echo esc_html( $label ); ?>
		</label>
		<?php if ( $description ) : ?>
			<p class="description"><?php echo esc_html( $description ); ?></p>
		<?php endif; ?>
		<?php
	}

	/**
	 * Render widget enable/disable controls.
	 *
	 * @param array<string, string> $args Field arguments.
	 * @return void
	 */
	public function render_widget_toggle_field( $args ) {
		$settings       = $this->get_settings();
		$enabled        = isset( $settings['enabled_widgets'] ) && is_array( $settings['enabled_widgets'] ) ? $settings['enabled_widgets'] : array();
		$manifest       = Widget_Registry::get_widget_manifest();
		$description    = isset( $args['description'] ) ? $args['description'] : '';
		$enabled_count  = count( $enabled );
		$total_count    = count( $manifest );
		?>
		<div style="display:grid;gap:12px;max-width:720px;">
			<div style="font-weight:600;">
				<?php echo esc_html( sprintf( __( '%1$d of %2$d widgets enabled', 'foundation-elementor-plus' ), $enabled_count, $total_count ) ); ?>
			</div>
			<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:10px;">
				<?php foreach ( $manifest as $widget_id => $widget ) : ?>
					<label style="display:flex;gap:10px;align-items:flex-start;padding:12px 14px;border:1px solid #d9deeb;border-radius:14px;background:#fff;">
						<input
							type="checkbox"
							name="<?php echo esc_attr( self::OPTION_KEY . '[enabled_widgets][]' ); ?>"
							value="<?php echo esc_attr( $widget_id ); ?>"
							<?php checked( in_array( $widget_id, $enabled, true ) ); ?>
						/>
						<span>
							<strong style="display:block;margin-bottom:4px;"><?php echo esc_html( $widget['title'] ); ?></strong>
							<span style="display:block;color:#5c647a;"><?php echo esc_html( $widget['description'] ); ?></span>
							<code style="display:block;margin-top:6px;"><?php echo esc_html( $widget_id ); ?></code>
						</span>
					</label>
				<?php endforeach; ?>
			</div>
		</div>
		<?php if ( $description ) : ?>
			<p class="description"><?php echo esc_html( $description ); ?></p>
		<?php endif; ?>
		<?php
	}

	/**
	 * Handle manual Elementor cache clearing.
	 *
	 * @return void
	 */
	public function handle_clear_cache() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to perform this action.', 'foundation-elementor-plus' ) );
		}

		check_admin_referer( self::CLEAR_CACHE_ACTION );

		$this->clear_elementor_cache();

		wp_safe_redirect(
			add_query_arg(
				array(
					'page'                  => self::MENU_SLUG,
					'foundation_cache_cleared' => '1',
				),
				admin_url( 'admin.php' )
			)
		);
		exit;
	}

	/**
	 * Enqueue admin app assets for the Foundation settings screen.
	 *
	 * @param string $hook_suffix Current admin page hook suffix.
	 * @return void
	 */
	public function enqueue_admin_assets( $hook_suffix ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( self::MENU_SLUG !== $page && false === strpos( (string) $hook_suffix, self::MENU_SLUG ) ) {
			return;
		}

		$css_relative = 'assets/admin/foundation-admin.css';
		$js_relative  = 'assets/admin/foundation-admin-app.js';
		$css_path     = FOUNDATION_ELEMENTOR_PLUS_PATH . $css_relative;
		$js_path      = FOUNDATION_ELEMENTOR_PLUS_PATH . $js_relative;

		wp_enqueue_style(
			'foundation-elementor-plus-admin',
			FOUNDATION_ELEMENTOR_PLUS_URL . $css_relative,
			array(),
			file_exists( $css_path ) ? (string) filemtime( $css_path ) : FOUNDATION_ELEMENTOR_PLUS_VERSION
		);

		wp_enqueue_script(
			'foundation-elementor-plus-admin',
			FOUNDATION_ELEMENTOR_PLUS_URL . $js_relative,
			array( 'wp-element' ),
			file_exists( $js_path ) ? (string) filemtime( $js_path ) : FOUNDATION_ELEMENTOR_PLUS_VERSION,
			true
		);

		wp_localize_script(
			'foundation-elementor-plus-admin',
			'foundationElementorPlusAdmin',
			$this->get_admin_app_data()
		);
	}

	/**
	 * Build admin bootstrapping data for the React settings screen.
	 *
	 * @return array<string, mixed>
	 */
	private function get_admin_app_data() {
		$settings            = $this->get_settings();
		$widget_manifest     = Widget_Registry::get_widget_manifest();
		$enabled_widget_ids  = $this->get_enabled_widget_ids();
		$widget_count        = count( $widget_manifest );
		$enabled_widget_count = count( $enabled_widget_ids );
		$elementor_ready     = did_action( 'elementor/loaded' );
		$plugin_file         = plugin_basename( FOUNDATION_ELEMENTOR_PLUS_FILE );

		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$widgets = array();

		foreach ( $widget_manifest as $widget_id => $widget ) {
			$widgets[] = array(
				'id'          => $widget_id,
				'title'       => $widget['title'],
				'description' => $widget['description'],
				'enabled'     => in_array( $widget_id, $enabled_widget_ids, true ),
			);
		}

		return array(
			'version'          => FOUNDATION_ELEMENTOR_PLUS_VERSION,
			'isActive'         => is_plugin_active( $plugin_file ),
			'elementorReady'   => $elementor_ready,
			'widgetCount'      => $widget_count,
			'enabledCount'     => $enabled_widget_count,
			'clearCacheUrl'    => wp_nonce_url( admin_url( 'admin-post.php?action=' . self::CLEAR_CACHE_ACTION ), self::CLEAR_CACHE_ACTION ),
			'settingsPageUrl'  => admin_url( 'admin.php?page=' . self::MENU_SLUG ),
			'libraryUrl'       => admin_url( 'edit.php?post_type=elementor_library' ),
			'cacheCleared'     => isset( $_GET['foundation_cache_cleared'] ) ? '1' === sanitize_text_field( wp_unslash( $_GET['foundation_cache_cleared'] ) ) : false, // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			'settings'         => $settings,
			'widgets'          => $widgets,
			'copy'             => array(
				'heroTitle'       => __( 'Foundation Elementor Plus', 'foundation-elementor-plus' ),
				'heroBody'        => __( 'SaaS-style control centre for your Inkfire widget suite. Keep only the widgets you use, trim asset loading, and manage integrations without spelunking through plugin files.', 'foundation-elementor-plus' ),
				'saveLabel'       => __( 'Save settings', 'foundation-elementor-plus' ),
				'enableAll'       => __( 'Enable all', 'foundation-elementor-plus' ),
				'disableAll'      => __( 'Disable all', 'foundation-elementor-plus' ),
				'openLibrary'     => __( 'Open Elementor Library', 'foundation-elementor-plus' ),
				'clearCache'      => __( 'Clear Elementor cache', 'foundation-elementor-plus' ),
				'refresh'         => __( 'Refresh', 'foundation-elementor-plus' ),
				'widgetSearch'    => __( 'Search widgets', 'foundation-elementor-plus' ),
				'widgetSearchHint'=> __( 'Filter by widget name or description', 'foundation-elementor-plus' ),
				'enabledLabel'    => __( 'Enabled', 'foundation-elementor-plus' ),
				'disabledLabel'   => __( 'Disabled', 'foundation-elementor-plus' ),
				'changesNotice'   => __( 'Changes save through WordPress options, so you still get the safe, native settings flow under the shiny paint.', 'foundation-elementor-plus' ),
				'cacheClearedNotice' => __( 'Elementor cache cleared.', 'foundation-elementor-plus' ),
			),
		);
	}

	/**
	 * Render the plugin admin page.
	 *
	 * @return void
	 */
	public function render_admin_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', 'foundation-elementor-plus' ) );
		}
		?>
		<div class="wrap">
			<form id="foundation-elementor-plus-admin-form" class="foundation-admin-shell" action="options.php" method="post">
				<?php settings_fields( 'foundation_elementor_plus' ); ?>
				<input type="hidden" name="<?php echo esc_attr( self::OPTION_KEY . '[enabled_widgets_present]' ); ?>" value="1" />
				<div id="foundation-elementor-plus-admin-root"></div>
				<noscript>
					<div class="notice notice-warning inline"><p><?php esc_html_e( 'The new Foundation admin screen needs JavaScript. The fallback settings form is shown below.', 'foundation-elementor-plus' ); ?></p></div>
					<?php do_settings_sections( 'foundation-elementor-plus' ); ?>
					<?php submit_button( __( 'Save Settings', 'foundation-elementor-plus' ) ); ?>
				</noscript>
			</form>
		</div>
		<?php
	}

	/**
	 * Get plugin default settings.
	 *
	 * @return array<string, mixed>
	 */
	private function get_default_settings() {
		return array(
			'widget_category_label'                => 'Foundation Plus',
			'widget_category_icon'                 => 'fa fa-plug',
			'defer_widget_scripts'                 => 'yes',
			'enable_legacy_upload_normalization'   => 'yes',
			'enabled_widgets'                      => array_keys( Widget_Registry::get_widget_manifest() ),
			'youtube_api_key'                      => '',
			'youtube_channel_source_default'       => '',
			'instagram_access_token'               => '',
			'instagram_business_account_id'        => '',
		);
	}

	/**
	 * Get merged plugin settings.
	 *
	 * @return array<string, mixed>
	 */
	private function get_settings() {
		$defaults = $this->get_default_settings();
		$stored   = get_option( self::OPTION_KEY, array() );

		if ( ! is_array( $stored ) ) {
			return $defaults;
		}

		$settings = wp_parse_args( $stored, $defaults );

		if ( empty( $settings['enabled_widgets'] ) || ! is_array( $settings['enabled_widgets'] ) ) {
			$settings['enabled_widgets'] = $defaults['enabled_widgets'];
		}

		return $settings;
	}

	/**
	 * Get the list of enabled widget ids in manifest order.
	 *
	 * @return array<int, string>
	 */
	private function get_enabled_widget_ids() {
		$manifest = Widget_Registry::get_widget_manifest();
		$settings = $this->get_settings();
		$enabled  = isset( $settings['enabled_widgets'] ) && is_array( $settings['enabled_widgets'] ) ? $settings['enabled_widgets'] : array_keys( $manifest );

		return array_values(
			array_filter(
				array_keys( $manifest ),
				static function( $widget_id ) use ( $enabled ) {
					return in_array( $widget_id, $enabled, true );
				}
			)
		);
	}
}
