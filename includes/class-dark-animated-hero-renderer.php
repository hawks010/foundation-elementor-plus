<?php

namespace FoundationElementorPlus;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Dark_Animated_Hero_Renderer {
	const OPTION_KEY = 'foundation_dark_animated_hero_settings';
	private static $autoplay_toggle_rendered = false;

	private static function get_palette_presets() {
		return array(
			'inkfire_gradient' => '#08352F,#0E6055,#138170,#07A079,#32B190,#CE3D27,#D85814,#E27200,#EC853D,#F18E5C',
			'inkfire_green'    => '#08352F,#0E6055,#138170,#07A079,#32B190',
			'inkfire_orange'   => '#CE3D27,#D85814,#E27200,#EC853D,#F18E5C',
		);
	}

	public static function get_palette_preset_options() {
		return array(
			''                 => 'Use preset',
			'inkfire_gradient' => 'Inkfire Gradient',
			'inkfire_green'    => 'Inkfire Green',
			'inkfire_orange'   => 'Inkfire Orange',
		);
	}

	private static function resolve_palette_string( $palette = '', $palette_key = '', $preset_palette_key = 'inkfire_gradient' ) {
		if ( '' !== trim( (string) $palette ) ) {
			return (string) $palette;
		}

		$palettes = self::get_palette_presets();
		$key      = '' !== trim( (string) $palette_key ) ? (string) $palette_key : (string) $preset_palette_key;

		if ( isset( $palettes[ $key ] ) ) {
			return $palettes[ $key ];
		}

		return $palettes['inkfire_gradient'];
	}

	public static function get_default_settings() {
		return array(
			'palette'        => self::get_palette_presets()['inkfire_gradient'],
			'team_images'    => implode(
				"\n",
				array(
					self::build_upload_url( '/2025/11/IMG_0054.jpeg' ),
					self::build_upload_url( '/2025/11/IMG_0053.jpeg' ),
					self::build_upload_url( '/2025/11/IMG_0049.png' ),
					self::build_upload_url( '/2025/11/IMG_0050.png' ),
				)
			),
			'team_link_text' => 'Meet the team',
			'team_link_url'  => '/about-us/meet-the-team/',
			'trust_label'    => 'Trusted Inkfire partners',
			'trust_logos'    => implode(
				"\n",
				array(
					self::build_upload_url( '/2026/03/logo-03.png' ),
					self::build_upload_url( '/2026/03/logo-04.png' ),
					self::build_upload_url( '/2026/03/logo-01.png' ),
					self::build_upload_url( '/2026/03/logo-02.png' ),
				)
			),
			'presets'        => array(
				'home'               => array(
					'label'            => 'Home',
					'palette_key'      => 'inkfire_gradient',
					'size'             => 'full',
					'align'            => 'center',
					'content_vertical_align' => 'center',
					'min_height'       => '100svh',
					'eyebrow'          => '🏆 Multi-award winning. Disabled-led team.',
					'title'            => '<span class="foundation-inkfire-brand"><img src="' . self::build_upload_url( '/2025/11/IMG_1089.png' ) . '" alt="Inkfire logo" style="height:1.5em; width:auto; display:inline-block; vertical-align:middle; margin-right:-4px; transform:translateY(-3px);"><span class="foundation-inkfire-brand-name">Inkfire</span></span> Building <br><span class="foundation-inkfire-gradient-wrap"><span class="foundation-inkfire-gradient-text">inclusive systems</span></span> that just work.',
					'subhead'          => 'We design, build and co-create inclusive digital systems, and support them long-term, when you need things done properly, at scale. Trusted by charities, public sector teams and growing organisations across the UK.',
					'primary_text'     => 'View our portfolio',
					'primary_url'      => '/portfolio/',
					'secondary_text'   => '',
					'secondary_url'    => '',
					'features'         => '',
					'feature_position' => 'after_subhead',
					'show_team'        => 1,
					'show_trust'       => 1,
				),
				'portfolio_archive'  => array(
					'label'            => 'Portfolio Archive',
					'palette_key'      => 'inkfire_gradient',
					'size'             => 'compact',
					'align'            => 'left',
					'content_vertical_align' => 'center',
					'min_height'       => '72svh',
					'eyebrow'          => '<span class="foundation-inkfire-pill foundation-inkfire-pill--crumb">{current_breadcrumb}</span><span class="foundation-inkfire-pill foundation-inkfire-pill--brand"><img src="' . self::build_upload_url( '/2025/11/IMG_1089.png' ) . '" alt="Inkfire logo"><span>Award winning</span></span><span class="foundation-inkfire-pill foundation-inkfire-pill--rating" aria-label="5 star rated"><span class="foundation-inkfire-rating-number">5</span><span class="foundation-inkfire-stars" aria-hidden="true">★</span><span class="foundation-inkfire-rating-label">rated</span></span>',
					'kicker'           => '{current_title_plain}',
					'title'            => 'Seen something you like? Partner with our disabled-led team ready to support your next project.',
					'subhead'          => 'Whether you need help getting started, a bit of support applying for Access To Work, or just a friendly chat, we’d love to hear from you.',
					'primary_text'     => 'Start a project',
					'primary_url'      => '/contact-us/',
					'secondary_text'   => 'Our services',
					'secondary_url'    => '/services/',
					'features'         => "Accessible by design\nBuilt for performance\nMade to scale",
					'feature_position' => 'after_subhead',
					'show_team'        => 0,
					'show_trust'       => 0,
				),
				'portfolio_post'     => array(
					'label'            => 'Portfolio Post',
					'palette_key'      => 'inkfire_gradient',
					'size'             => 'compact',
					'align'            => 'left',
					'content_vertical_align' => 'center',
					'min_height'       => '76svh',
					'eyebrow'          => '<span class="foundation-inkfire-pill foundation-inkfire-pill--crumb">{current_breadcrumb}</span><span class="foundation-inkfire-pill foundation-inkfire-pill--brand"><img src="' . self::build_upload_url( '/2025/11/IMG_1089.png' ) . '" alt="Inkfire logo"><span>Award winning</span></span><span class="foundation-inkfire-pill foundation-inkfire-pill--rating" aria-label="5 star rated"><span class="foundation-inkfire-rating-number">5</span><span class="foundation-inkfire-stars" aria-hidden="true">★</span><span class="foundation-inkfire-rating-label">rated</span></span>',
					'kicker'           => '{current_title_plain}',
					'title'            => '{current_title}',
					'subhead'          => '{current_excerpt}',
					'primary_text'     => 'Start a project',
					'primary_url'      => '/contact-us/',
					'secondary_text'   => 'View all work',
					'secondary_url'    => '/portfolio/',
					'features'         => '',
					'feature_position' => 'hidden',
					'show_team'        => 0,
					'show_trust'       => 0,
				),
				'seo'                => array(
					'label'            => 'SEO Service',
					'palette_key'      => 'inkfire_gradient',
					'size'             => 'compact',
					'align'            => 'left',
					'content_vertical_align' => 'center',
					'min_height'       => '72svh',
					'eyebrow'          => 'SEO services',
					'title'            => 'SEO systems that compound over time',
					'subhead'          => 'Technical SEO, content strategy, and accessible search experiences designed to bring the right people to you and keep momentum building.',
					'primary_text'     => 'Talk to us',
					'primary_url'      => '/contact-us/',
					'secondary_text'   => 'Creative marketing',
					'secondary_url'    => '/services/creative-marketing/',
					'features'         => "Technical SEO audits\nContent strategy\nInclusive search journeys",
					'feature_position' => 'after_subhead',
					'show_team'        => 0,
					'show_trust'       => 0,
				),
				'it_support'         => array(
					'label'            => 'IT Support',
					'palette_key'      => 'inkfire_gradient',
					'size'             => 'compact',
					'align'            => 'left',
					'content_vertical_align' => 'center',
					'min_height'       => '72svh',
					'eyebrow'          => 'IT support',
					'title'            => 'Reliable tech support without the usual friction',
					'subhead'          => 'Managed IT, Microsoft 365, cybersecurity, and day-to-day support for organisations that need things handled properly.',
					'primary_text'     => 'Speak to our team',
					'primary_url'      => '/contact-us/',
					'secondary_text'   => 'Tech and support',
					'secondary_url'    => '/services/tech-support/',
					'features'         => "Managed support\nMicrosoft 365\nSecurity and device care",
					'feature_position' => 'after_subhead',
					'show_team'        => 0,
					'show_trust'       => 0,
				),
				'web_development'    => array(
					'label'            => 'Web Development',
					'palette_key'      => 'inkfire_gradient',
					'size'             => 'compact',
					'align'            => 'left',
					'content_vertical_align' => 'center',
					'min_height'       => '72svh',
					'eyebrow'          => 'Web development',
					'title'            => 'Websites that are inclusive, fast, and built to grow',
					'subhead'          => 'Custom web design and development for ambitious organisations that need performance, accessibility, and a strong technical foundation.',
					'primary_text'     => 'Plan your website',
					'primary_url'      => '/contact-us/',
					'secondary_text'   => 'Web development',
					'secondary_url'    => '/services/web-accessibility/web-development/',
					'features'         => "Custom builds\nAccessibility first\nLong-term support",
					'feature_position' => 'after_subhead',
					'show_team'        => 0,
					'show_trust'       => 0,
				),
				'branding_marketing' => array(
					'label'            => 'Branding / Marketing',
					'palette_key'      => 'inkfire_gradient',
					'size'             => 'compact',
					'align'            => 'left',
					'content_vertical_align' => 'center',
					'min_height'       => '72svh',
					'eyebrow'          => 'Branding and marketing',
					'title'            => 'Brands and campaigns people remember for the right reasons',
					'subhead'          => 'Strategy, storytelling, and inclusive creative marketing that helps people find you, trust you, and choose you.',
					'primary_text'     => 'Start a campaign',
					'primary_url'      => '/contact-us/',
					'secondary_text'   => 'Creative marketing',
					'secondary_url'    => '/services/creative-marketing/',
					'features'         => "Brand strategy\nContent and campaigns\nAccessible messaging",
					'feature_position' => 'after_subhead',
					'show_team'        => 0,
					'show_trust'       => 0,
				),
				'other_service'      => array(
					'label'            => 'Other Service',
					'palette_key'      => 'inkfire_gradient',
					'size'             => 'compact',
					'align'            => 'left',
					'content_vertical_align' => 'center',
					'min_height'       => '72svh',
					'eyebrow'          => 'Specialist support',
					'title'            => 'Complex projects, shaped around what you actually need',
					'subhead'          => 'For work that spans strategy, delivery, accessibility, and support, we shape a service around the outcome you need.',
					'primary_text'     => 'Tell us what you need',
					'primary_url'      => '/contact-us/',
					'secondary_text'   => 'Explore services',
					'secondary_url'    => '/services/',
					'features'         => "Accessibility consultancy\nBespoke builds\nFlexible support models",
					'feature_position' => 'after_subhead',
					'show_team'        => 0,
					'show_trust'       => 0,
				),
			),
		);
	}

	public static function get_settings() {
		$defaults = self::get_default_settings();
		$stored   = get_option( self::OPTION_KEY, array() );
		$stored   = is_array( $stored ) ? $stored : array();
		$settings = wp_parse_args( $stored, $defaults );

		$settings['presets'] = isset( $stored['presets'] ) && is_array( $stored['presets'] ) ? $stored['presets'] : array();

		foreach ( $defaults['presets'] as $preset_key => $preset_defaults ) {
			$settings['presets'][ $preset_key ] = wp_parse_args(
				isset( $settings['presets'][ $preset_key ] ) && is_array( $settings['presets'][ $preset_key ] ) ? $settings['presets'][ $preset_key ] : array(),
				$preset_defaults
			);
			$settings['presets'][ $preset_key ]['min_height'] = self::normalize_viewport_unit( $settings['presets'][ $preset_key ]['min_height'] );
		}

		return $settings;
	}

	public static function get_preset_options() {
		$options  = array();
		$settings = self::get_settings();

		foreach ( $settings['presets'] as $preset_key => $preset ) {
			$options[ $preset_key ] = isset( $preset['label'] ) ? $preset['label'] : $preset_key;
		}

		return $options;
	}

	public static function enqueue_assets( $palette = '', $needs_icon_fonts = false ) {
		wp_enqueue_style( 'foundation-elementor-plus-core' );
		wp_enqueue_style( 'foundation-elementor-plus-dark-animated-hero' );
		wp_enqueue_script( 'foundation-elementor-plus-fluid-core' );
		wp_enqueue_script( 'foundation-elementor-plus-dark-animated-hero' );

		if ( $needs_icon_fonts ) {
			self::enqueue_icon_fonts();
		}

		$config = array(
			'palette'             => self::get_palette_values( $palette ),
			'ditheringTextureUrl' => FOUNDATION_ELEMENTOR_PLUS_URL . 'assets/vendor/LDR_LLL1_0.png',
		);

		wp_add_inline_script(
			'foundation-elementor-plus-dark-animated-hero',
			'window.FOUNDATION_INKFIRE_SPLASH_CONFIG = ' . wp_json_encode( $config ) . ';',
			'before'
		);
	}

	public static function render( $atts = array() ) {
		$settings       = self::get_settings();
		$preset_key     = isset( $atts['preset'] ) ? sanitize_key( (string) $atts['preset'] ) : 'home';
		$default_preset = self::get_preset_config( $preset_key );

		$defaults = array(
			'preset'           => 'home',
			'palette_key'      => isset( $default_preset['palette_key'] ) ? $default_preset['palette_key'] : 'inkfire_green',
			'palette'          => '',
			'size'             => $default_preset['size'],
			'align'            => $default_preset['align'],
			'content_vertical_align' => isset( $default_preset['content_vertical_align'] ) ? $default_preset['content_vertical_align'] : 'center',
			'min_height'       => $default_preset['min_height'],
			'height_strategy'  => 'preset',
			'eyebrow'          => $default_preset['eyebrow'],
			'eyebrow_style'    => 'green_glass',
			'kicker_html'      => isset( $default_preset['kicker'] ) ? $default_preset['kicker'] : '',
			'h1_html'          => $default_preset['title'],
			'subhead'          => $default_preset['subhead'],
			'btn_text'         => $default_preset['primary_text'],
			'btn_url'          => $default_preset['primary_url'],
			'show_primary_button' => '',
			'secondary_text'   => $default_preset['secondary_text'],
			'secondary_url'    => $default_preset['secondary_url'],
			'show_secondary_button' => '',
			'trust_html'       => '',
			'trust_label'      => '',
			'trust_logos'      => array(),
			'features'         => $default_preset['features'],
			'feature_position' => $default_preset['feature_position'],
			'show_team'        => (string) $default_preset['show_team'],
			'show_trust'       => (string) $default_preset['show_trust'],
			'class'            => '',
		);

		$a = wp_parse_args( $atts, $defaults );

		$preset_key       = sanitize_key( (string) $a['preset'] );
		$context_post_id  = self::get_render_context_post_id( $preset_key );
		$token_context    = array( 'post_id' => $context_post_id );
		$preset_slug      = str_replace( '_', '-', $preset_key );
		$size             = in_array( (string) $a['size'], array( 'full', 'compact' ), true ) ? (string) $a['size'] : $default_preset['size'];
		$align            = in_array( (string) $a['align'], array( 'center', 'left' ), true ) ? (string) $a['align'] : $default_preset['align'];
		$content_vertical_align = in_array( (string) $a['content_vertical_align'], array( 'flex-start', 'center', 'flex-end' ), true ) ? (string) $a['content_vertical_align'] : ( isset( $default_preset['content_vertical_align'] ) ? (string) $default_preset['content_vertical_align'] : 'center' );
		$height_strategy  = in_array( (string) $a['height_strategy'], array( 'preset', 'viewport', 'viewport_offset', 'content' ), true ) ? (string) $a['height_strategy'] : 'preset';
		$a['min_height']  = self::normalize_viewport_unit( $a['min_height'] );
		$headline_html    = self::normalize_icon_markup( self::replace_tokens( $a['h1_html'], $token_context ) );
		$kicker_html      = self::normalize_icon_markup( self::replace_tokens( $a['kicker_html'], $token_context ) );
		$subhead_html     = self::normalize_icon_markup( self::replace_tokens( $a['subhead'], $token_context ) );
		$eyebrow_html     = self::normalize_icon_markup( self::replace_tokens( $a['eyebrow'], $token_context ) );
		$eyebrow_style    = in_array( (string) $a['eyebrow_style'], array( 'green_glass', 'orange_glass', 'white_glass' ), true ) ? (string) $a['eyebrow_style'] : 'green_glass';
		$feature_items    = self::parse_features( self::replace_tokens( $a['features'], $token_context ) );
		$feature_position = in_array( (string) $a['feature_position'], array( 'after_title', 'after_subhead', 'before_buttons', 'hidden' ), true ) ? (string) $a['feature_position'] : $default_preset['feature_position'];
		$show_team        = in_array( (string) $a['show_team'], array( '1', 'yes', 'true' ), true ) || true === $a['show_team'];
		$show_trust       = in_array( (string) $a['show_trust'], array( '1', 'yes', 'true' ), true ) || true === $a['show_trust'];
		$show_primary_button = '0' !== (string) $a['show_primary_button'] && '' !== trim( (string) $a['btn_text'] );
		$show_secondary_button = '0' !== (string) $a['show_secondary_button'] && '' !== trim( (string) $a['secondary_text'] );
		$preset_palette   = isset( $default_preset['palette_key'] ) ? (string) $default_preset['palette_key'] : 'inkfire_green';
		$palette_key      = isset( $a['palette_key'] ) ? sanitize_key( (string) $a['palette_key'] ) : '';
		$resolved_palette = self::resolve_palette_string( $a['palette'], $palette_key, $preset_palette );
		$project_team_markup = self::render_project_team_hero(
			$preset_key,
			array(
				'visibility' => isset( $a['project_team_visibility'] ) ? (string) $a['project_team_visibility'] : '',
				'department' => isset( $a['project_team_department'] ) ? (string) $a['project_team_department'] : '',
			)
		);

		$is_editor_preview = self::is_elementor_editor_preview();

		$class_names = array(
			'foundation-inkfire-splash',
			'foundation-inkfire-splash--' . $size,
			'foundation-inkfire-splash--height-' . sanitize_html_class( str_replace( '_', '-', $height_strategy ) ),
			'foundation-inkfire-splash--align-' . $align,
			'foundation-inkfire-splash--preset-' . sanitize_html_class( $preset_slug ),
			'foundation-inkfire-splash--features-' . sanitize_html_class( $feature_position ),
			'foundation-inkfire-splash--palette-' . sanitize_html_class( str_replace( '_', '-', '' !== $palette_key ? $palette_key : $preset_palette ) ),
		);

		if ( $is_editor_preview ) {
			$class_names[] = 'foundation-inkfire-splash--editor-preview';
		}

		if ( $show_team ) {
			$class_names[] = 'foundation-inkfire-splash--has-team';
		}

		if ( $show_trust ) {
			$class_names[] = 'foundation-inkfire-splash--has-trust';
		}

		if ( ! empty( $feature_items ) && 'hidden' !== $feature_position ) {
			$class_names[] = 'foundation-inkfire-splash--has-features';
		}

		if ( '' !== trim( (string) $a['class'] ) ) {
			$class_names[] = sanitize_html_class( (string) $a['class'] );
		}

		$section_id    = 'foundation-inkfire-hero-' . wp_unique_id();
		$vertical_align_slug = str_replace( '-', '_', $content_vertical_align );
		$hero_classes  = 'foundation-inkfire-hero foundation-inkfire-hero--' . $align . ' foundation-inkfire-hero--' . $size . ' foundation-inkfire-hero--v-' . sanitize_html_class( $vertical_align_slug );
		$palette_attr  = implode( ',', self::get_palette_values( $resolved_palette ) );
		$inline_styles = '--foundation-inkfire-section-min-height:' . esc_attr( (string) $a['min_height'] ) . ';';

		$has_eyebrow      = '' !== trim( wp_strip_all_tags( $eyebrow_html ) );
		$has_kicker       = '' !== trim( wp_strip_all_tags( $kicker_html ) );
		$needs_icon_fonts = self::markup_uses_fontawesome( $eyebrow_html . ' ' . $kicker_html . ' ' . $headline_html . ' ' . $subhead_html );
		self::enqueue_assets( $resolved_palette, $needs_icon_fonts );

		ob_start();
		?>
		<section
			id="<?php echo esc_attr( $section_id ); ?>"
			class="<?php echo esc_attr( trim( implode( ' ', array_filter( $class_names ) ) ) ); ?>"
			data-foundation-inkfire-splash
			data-palette="<?php echo esc_attr( $palette_attr ); ?>"
			data-palette-key="<?php echo esc_attr( '' !== $palette_key ? $palette_key : $preset_palette ); ?>"
			role="banner"
			aria-labelledby="<?php echo esc_attr( $section_id . '-heading' ); ?>"
			aria-describedby="<?php echo esc_attr( $section_id . '-subhead' ); ?>"
			style="<?php echo esc_attr( $inline_styles ); ?>"
		>
			<canvas class="foundation-inkfire-splash__canvas" aria-hidden="true" role="presentation"></canvas>
			<div class="foundation-inkfire-splash__mask" aria-hidden="true"></div>

			<div class="<?php echo esc_attr( $hero_classes ); ?>">
				<div class="foundation-inkfire-hero__inner">
					<?php if ( $has_eyebrow || $has_kicker ) : ?>
						<div class="foundation-inkfire-meta-row">
							<?php if ( $has_kicker ) : ?>
								<p class="foundation-inkfire-kicker"><?php echo wp_kses_post( $kicker_html ); ?></p>
							<?php endif; ?>

							<?php if ( $has_eyebrow ) : ?>
								<p class="foundation-inkfire-eyebrow foundation-inkfire-eyebrow--<?php echo esc_attr( sanitize_html_class( str_replace( '_', '-', $eyebrow_style ) ) ); ?>"><?php echo wp_kses_post( $eyebrow_html ); ?></p>
							<?php endif; ?>
						</div>
					<?php endif; ?>

					<h1 id="<?php echo esc_attr( $section_id . '-heading' ); ?>" class="foundation-inkfire-headline">
						<?php echo wp_kses_post( $headline_html ); ?>
					</h1>

					<?php if ( 'after_title' === $feature_position ) : ?>
						<?php echo self::render_feature_list( $feature_items, $feature_position ); ?>
					<?php endif; ?>

					<?php if ( '' !== trim( wp_strip_all_tags( $subhead_html ) ) ) : ?>
						<p id="<?php echo esc_attr( $section_id . '-subhead' ); ?>" class="foundation-inkfire-subhead">
							<?php echo wp_kses_post( $subhead_html ); ?>
						</p>
					<?php endif; ?>

					<?php if ( 'after_subhead' === $feature_position ) : ?>
						<?php echo self::render_feature_list( $feature_items, $feature_position ); ?>
					<?php endif; ?>

					<?php if ( 'before_buttons' === $feature_position ) : ?>
						<?php echo self::render_feature_list( $feature_items, $feature_position ); ?>
					<?php endif; ?>

					<div class="foundation-inkfire-action-row">
						<div class="foundation-inkfire-button-row">
							<?php if ( $show_primary_button ) : ?>
								<a href="<?php echo esc_url( (string) $a['btn_url'] ); ?>" class="foundation-inkfire-main-btn">
									<span><?php echo esc_html( (string) $a['btn_text'] ); ?></span>
									<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
										<path d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25"></path>
									</svg>
								</a>
							<?php endif; ?>

							<?php if ( $show_secondary_button ) : ?>
								<a href="<?php echo esc_url( (string) $a['secondary_url'] ); ?>" class="foundation-inkfire-secondary-btn">
									<span><?php echo esc_html( (string) $a['secondary_text'] ); ?></span>
									<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
										<path d="M5 12h14M12 5l7 7-7 7"></path>
									</svg>
								</a>
							<?php endif; ?>
						</div>

						<?php if ( $show_team ) : ?>
							<?php echo self::render_team_hero(); ?>
						<?php endif; ?>

						<?php if ( '' !== $project_team_markup ) : ?>
							<?php echo wp_kses_post( $project_team_markup ); ?>
						<?php endif; ?>
					</div>

					<?php if ( $show_trust ) : ?>
						<?php echo wp_kses_post( self::render_trust_bar( $a['trust_html'], $a['trust_label'], $a['trust_logos'] ) ); ?>
					<?php endif; ?>
				</div>
			</div>

			<?php echo self::render_autoplay_toggle(); ?>
		</section>
		<?php

		return ob_get_clean();
	}

	private static function render_autoplay_toggle() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return '';
		}

		if ( self::$autoplay_toggle_rendered ) {
			return '';
		}

		self::$autoplay_toggle_rendered = true;

		ob_start();
		?>
		<button
			id="foundation-inkfire-autoplay-toggle"
			class="foundation-inkfire-autoplay-toggle is-on"
			type="button"
			data-foundation-inkfire-autoplay-toggle
			aria-pressed="true"
			aria-label="<?php echo esc_attr__( 'Toggle hero animation autoplay mode', 'foundation-elementor-plus' ); ?>"
		>
			<span class="foundation-inkfire-autoplay-toggle__dot" aria-hidden="true"></span>
			<span class="foundation-inkfire-autoplay-toggle__prefix"><?php echo esc_html__( 'Animation', 'foundation-elementor-plus' ); ?></span>
			<span class="foundation-inkfire-autoplay-toggle__label" data-foundation-inkfire-toggle-label><?php echo esc_html__( 'AUTO', 'foundation-elementor-plus' ); ?></span>
		</button>
		<?php

		return trim( ob_get_clean() );
	}

	private static function get_preset_config( $preset_key ) {
		$settings = self::get_settings();

		if ( empty( $preset_key ) || ! isset( $settings['presets'][ $preset_key ] ) ) {
			return $settings['presets']['home'];
		}

		$preset = $settings['presets'][ $preset_key ];

		$context_post_id = self::get_render_context_post_id( $preset_key );

		if ( 'portfolio_post' === $preset_key && $context_post_id && 'ink_portfolio' === get_post_type( $context_post_id ) ) {
			$preset['kicker']           = '{current_breadcrumb_links}';
			$preset['eyebrow']          = '{current_portfolio_meta_pills}';
			$preset['title']            = '{current_title}';
			$preset['subhead']          = '{current_excerpt}';
			$preset['features']         = '';
			$preset['feature_position'] = 'hidden';
		}

		$preset['min_height'] = self::normalize_viewport_unit( $preset['min_height'] );

		return $preset;
	}

	private static function normalize_viewport_unit( $value ) {
		$value = trim( (string) $value );

		if ( '' === $value ) {
			return $value;
		}

		return preg_replace( '/(?<![a-z])vh\b/i', 'svh', $value );
	}

	private static function get_palette_values( $palette = '' ) {
		$palette = '' !== trim( (string) $palette )
			? (string) $palette
			: self::get_settings()['palette'];

		return array_values(
			array_filter(
				array_map(
					'trim',
					explode( ',', $palette )
				)
			)
		);
	}

	private static function get_lines( $value ) {
		$lines = preg_split( '/\r\n|\r|\n/', (string) $value );

		return array_values(
			array_filter(
				array_map(
					static function ( $line ) {
						return trim( (string) $line );
					},
					$lines
				)
			)
		);
	}

	private static function replace_tokens( $value, array $context = array() ) {
		$current_title      = '';
		$current_title_plain = '';
		$current_excerpt    = '';
		$current_breadcrumb = '';
		$current_breadcrumb_links = '';
		$current_category   = '';
		$current_date       = '';
		$current_meta_pills = '';
		$context_post_id    = isset( $context['post_id'] ) ? (int) $context['post_id'] : 0;

		if ( $context_post_id > 0 ) {
			$current_title       = get_the_title( $context_post_id );
			$current_title_plain = $current_title;
			$current_excerpt     = self::get_current_context_excerpt( $context_post_id );
			$current_breadcrumb  = self::get_current_breadcrumb_label( $context_post_id );
			$current_breadcrumb_links = self::get_current_breadcrumb_markup( $context_post_id );
			$current_category    = self::get_current_primary_term_label( $context_post_id );
			$current_date        = get_the_date( 'j M Y', $context_post_id );
			$current_meta_pills  = self::get_current_portfolio_meta_pills( $context_post_id );
		} elseif ( is_archive() || is_post_type_archive() ) {
			$current_title = wp_strip_all_tags( get_the_archive_title() );
			if ( is_post_type_archive() ) {
				$current_title_plain = wp_strip_all_tags( post_type_archive_title( '', false ) );
			} elseif ( is_tax() || is_category() || is_tag() ) {
				$current_title_plain = single_term_title( '', false );
			}
		} elseif ( is_singular() ) {
			$post_id            = get_the_ID();
			$current_title      = get_the_title();
			$current_title_plain = $current_title;
			$current_excerpt    = self::get_current_context_excerpt( $post_id );
			$current_breadcrumb = self::get_current_breadcrumb_label( $post_id );
			$current_breadcrumb_links = self::get_current_breadcrumb_markup( $post_id );
			$current_category   = self::get_current_primary_term_label( $post_id );
			$current_date       = $post_id ? get_the_date( 'j M Y', $post_id ) : '';
			$current_meta_pills = self::get_current_portfolio_meta_pills( $post_id );
		} elseif ( is_home() || is_front_page() ) {
			$current_title = get_bloginfo( 'name' );
			$current_title_plain = $current_title;
		} else {
			$current_title = wp_get_document_title();
			$current_title_plain = $current_title;
		}

		if ( '' === $current_title_plain ) {
			$current_title_plain = preg_replace( '/^[^:]+:\s*/', '', $current_title );
		}

		if ( '' === $current_excerpt ) {
			$current_excerpt = 'Selected project work from Inkfire, designed to perform, scale, and stand out for the right reasons.';
		}

		$replaced = strtr(
			(string) $value,
			array(
				'{current_title}'   => $current_title,
				'{current_title_plain}' => $current_title_plain,
				'{current_excerpt}' => $current_excerpt,
				'{current_breadcrumb}' => $current_breadcrumb,
				'{current_breadcrumb_links}' => $current_breadcrumb_links,
				'{current_category}' => $current_category,
				'{current_date}'    => $current_date,
				'{current_portfolio_meta_pills}' => $current_meta_pills,
				'{service_name}'    => $current_title,
				'{site_name}'       => get_bloginfo( 'name' ),
				'{year}'            => gmdate( 'Y' ),
			)
		);

		$replaced = preg_replace(
			'/<span class="foundation-inkfire-pill foundation-inkfire-pill--crumb">\s*<\/span>/',
			'',
			$replaced
		);

		return is_string( $replaced ) ? $replaced : (string) $value;
	}

	private static function get_current_breadcrumb_label( $post_id = 0 ) {
		$post_id = $post_id ? (int) $post_id : (int) get_the_ID();

		if ( ! $post_id ) {
			return '';
		}

		$post_type = get_post_type( $post_id );

		if ( ! $post_type ) {
			return '';
		}

		$post_type_object = get_post_type_object( $post_type );
		$archive_label    = ( $post_type_object && ! empty( $post_type_object->labels->name ) ) ? $post_type_object->labels->name : ucfirst( $post_type );
		$archive_label    = wp_strip_all_tags( (string) $archive_label );

		$term = self::get_current_primary_term( $post_id );
		if ( $term && ! empty( $term->name ) ) {
			return trim( $archive_label . ' / ' . wp_strip_all_tags( (string) $term->name ) );
		}

		return $archive_label;
	}

	private static function get_current_breadcrumb_markup( $post_id = 0 ) {
		$post_id = $post_id ? (int) $post_id : (int) get_the_ID();

		if ( ! $post_id ) {
			return '';
		}

		$post_type = get_post_type( $post_id );
		if ( ! $post_type ) {
			return '';
		}

		$post_type_object = get_post_type_object( $post_type );
		$archive_label    = ( $post_type_object && ! empty( $post_type_object->labels->name ) ) ? $post_type_object->labels->name : ucfirst( $post_type );
		$archive_label    = wp_strip_all_tags( (string) $archive_label );
		$archive_url      = get_post_type_archive_link( $post_type );

		if ( ! $archive_url && 'ink_portfolio' === $post_type ) {
			$archive_url = home_url( '/portfolio/' );
		}

		$term             = self::get_current_primary_term( $post_id );
		$parts            = array();

		if ( $archive_url ) {
			$parts[] = sprintf(
				'<a class="foundation-inkfire-breadcrumb-link foundation-inkfire-breadcrumb-link--archive" href="%s">%s</a>',
				esc_url( $archive_url ),
				esc_html( $archive_label )
			);
		} else {
			$parts[] = sprintf(
				'<span class="foundation-inkfire-breadcrumb-link foundation-inkfire-breadcrumb-link--archive">%s</span>',
				esc_html( $archive_label )
			);
		}

		if ( $term && ! empty( $term->name ) ) {
			$term_link = get_term_link( $term );

			$parts[] = '<span class="foundation-inkfire-breadcrumb-separator" aria-hidden="true">/</span>';

			if ( ! is_wp_error( $term_link ) ) {
				$parts[] = sprintf(
					'<a class="foundation-inkfire-breadcrumb-link foundation-inkfire-breadcrumb-link--term" href="%s">%s</a>',
					esc_url( $term_link ),
					esc_html( $term->name )
				);
			} else {
				$parts[] = sprintf(
					'<span class="foundation-inkfire-breadcrumb-link foundation-inkfire-breadcrumb-link--term">%s</span>',
					esc_html( $term->name )
				);
			}
		}

		return implode( '', $parts );
	}

	private static function get_current_context_excerpt( $post_id = 0 ) {
		$post_id = $post_id ? (int) $post_id : (int) get_the_ID();

		if ( ! $post_id ) {
			return '';
		}

		if ( has_excerpt( $post_id ) ) {
			return get_the_excerpt( $post_id );
		}

		$content = get_post_field( 'post_content', $post_id );

		if ( '' !== trim( (string) $content ) ) {
			return wp_trim_words( wp_strip_all_tags( (string) $content ), 28 );
		}

		return '';
	}

	private static function get_current_primary_term_label( $post_id = 0 ) {
		$term = self::get_current_primary_term( $post_id );

		if ( $term && ! empty( $term->name ) ) {
			return wp_strip_all_tags( (string) $term->name );
		}

		return '';
	}

	private static function get_current_primary_term( $post_id = 0 ) {
		$post_id = $post_id ? (int) $post_id : (int) get_the_ID();

		if ( ! $post_id ) {
			return null;
		}

		$post_type = get_post_type( $post_id );
		if ( ! $post_type ) {
			return null;
		}

		$taxonomies = get_object_taxonomies( $post_type, 'names' );
		$preferred  = 'ink_portfolio' === $post_type
			? array( 'ink_portfolio_type', 'portfolio_category', 'category', 'portfolio_tag', 'post_tag' )
			: array( 'portfolio_category', 'category', 'portfolio_tag', 'post_tag' );
		$ordered    = array_values( array_unique( array_merge( $preferred, $taxonomies ) ) );

		foreach ( $ordered as $taxonomy ) {
			if ( ! in_array( $taxonomy, $taxonomies, true ) ) {
				continue;
			}

			$terms = get_the_terms( $post_id, $taxonomy );
			if ( empty( $terms ) || is_wp_error( $terms ) ) {
				continue;
			}

			$term = reset( $terms );
			if ( $term && ! empty( $term->name ) ) {
				return $term;
			}
		}

		return null;
	}

	private static function get_current_portfolio_meta_pills( $post_id = 0 ) {
		$post_id = $post_id ? (int) $post_id : (int) get_the_ID();

		if ( ! $post_id || 'ink_portfolio' !== get_post_type( $post_id ) ) {
			return '';
		}

		$pills    = array();
		$category = self::get_current_primary_term_label( $post_id );
		$date     = get_the_date( 'j M Y', $post_id );

		if ( '' !== trim( $category ) ) {
			$pills[] = sprintf(
				'<span class="foundation-inkfire-pill foundation-inkfire-pill--crumb">%s</span>',
				esc_html( $category )
			);
		}

		if ( '' !== trim( (string) $date ) ) {
			$pills[] = sprintf(
				'<span class="foundation-inkfire-pill foundation-inkfire-pill--rating">%s</span>',
				esc_html( (string) $date )
			);
		}

		return implode( '', $pills );
	}

	private static function normalize_icon_markup( $html ) {
		return strtr(
			(string) $html,
			array(
				'class="fa-solid '   => 'class="fa-solid fas ',
				"class='fa-solid "   => "class='fa-solid fas ",
				'class="fa-regular ' => 'class="fa-regular far ',
				"class='fa-regular " => "class='fa-regular far ",
				'class="fa-brands '  => 'class="fa-brands fab ',
				"class='fa-brands "  => "class='fa-brands fab ",
			)
		);
	}

	private static function markup_uses_fontawesome( $html ) {
		return is_string( $html ) && ( false !== strpos( $html, 'fa-' ) || false !== strpos( $html, 'fas ' ) || false !== strpos( $html, 'far ' ) || false !== strpos( $html, 'fab ' ) );
	}

	private static function build_upload_url( $relative_path ) {
		return home_url( '/wp-content/uploads' . $relative_path );
	}

	private static function render_team_hero() {
		$settings      = self::get_settings();
		$team_images   = self::get_lines( $settings['team_images'] );
		$team_link_url = ! empty( $settings['team_link_url'] ) ? $settings['team_link_url'] : '/about-us/meet-the-team/';

		if ( empty( $team_images ) ) {
			return '';
		}

		$output  = '<div class="foundation-inkfire-avatar-group" role="img" aria-label="Inkfire team: disabled-led creative and tech experts">';
		$output .= '<div class="foundation-inkfire-avatars" aria-hidden="true">';

		foreach ( $team_images as $index => $url ) {
			$margin  = 0 === $index ? '0' : '-15px';
			$output .= '<img class="foundation-inkfire-avatar" src="' . esc_url( $url ) . '" alt="" aria-hidden="true" loading="lazy" style="margin-left:' . esc_attr( $margin ) . ';position:relative;z-index:' . ( 10 - (int) $index ) . ';">';
		}

		$output .= '</div>';
		$output .= '<a href="' . esc_url( $team_link_url ) . '" class="foundation-inkfire-meet-team-link" aria-label="Meet the Inkfire team">';
		$output .= '<span class="foundation-inkfire-meet-text">' . esc_html( $settings['team_link_text'] ) . '</span>';
		$output .= '<span class="foundation-inkfire-meet-arrow"><svg viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg></span>';
		$output .= '</a>';
		$output .= '</div>';

		return $output;
	}

	private static function render_project_team_hero( $preset_key, array $overrides = array() ) {
		$context_post_id      = self::get_render_context_post_id( $preset_key );
		$is_portfolio_context = $context_post_id && 'ink_portfolio' === get_post_type( $context_post_id );
		$is_editor_preview    = self::is_elementor_editor_preview();

		if ( 'portfolio_post' !== $preset_key || ( ! $is_portfolio_context && ! $is_editor_preview ) ) {
			return '';
		}

		$post_id = $context_post_id ? $context_post_id : (int) get_the_ID();
		$team_enabled = (string) get_post_meta( $post_id, '_ink_portfolio_hero_team_enabled', true );
		$visibility   = isset( $overrides['visibility'] ) ? trim( (string) $overrides['visibility'] ) : '';

		if ( ! $is_portfolio_context && ! $is_editor_preview ) {
			return '';
		}

		if ( 'hide' === $visibility || '0' === $visibility ) {
			return '';
		}

		if ( $is_portfolio_context && ( '' === $visibility || 'post' === $visibility ) ) {
			if ( '0' === $team_enabled ) {
				return '';
			}
		}

		$departments = class_exists( __NAMESPACE__ . '\\Team_Inline_Images' )
			? Team_Inline_Images::get_department_options()
			: array(
				'all'        => __( 'All Team', 'foundation-elementor-plus' ),
				'management' => __( 'Management', 'foundation-elementor-plus' ),
				'it'         => __( 'IT', 'foundation-elementor-plus' ),
				'web'        => __( 'Web', 'foundation-elementor-plus' ),
				'marketing'  => __( 'Marketing', 'foundation-elementor-plus' ),
				'branding'   => __( 'Branding', 'foundation-elementor-plus' ),
			);
		$department_setting = isset( $overrides['department'] ) ? trim( (string) $overrides['department'] ) : '';
		$department         = 'all';

		if ( $is_portfolio_context ) {
			$department = ( '' === $department_setting || 'post' === $department_setting )
				? sanitize_key( (string) get_post_meta( $post_id, '_ink_portfolio_hero_team_department', true ) )
				: sanitize_key( $department_setting );
		} elseif ( $is_editor_preview ) {
			$department = ( '' === $department_setting || 'post' === $department_setting )
				? 'all'
				: sanitize_key( $department_setting );
		}

		if ( ! isset( $departments[ $department ] ) ) {
			$department = 'all';
		}

		if ( ! shortcode_exists( 'ink_team' ) ) {
			return '';
		}

		$settings       = self::get_settings();
		$team_link_url  = ! empty( $settings['team_link_url'] ) ? $settings['team_link_url'] : '/about-us/meet-the-team/';
		$avatars_markup = trim( do_shortcode( '[ink_team department="' . $department . '" class="foundation-team-inline--hero"]' ) );

		if ( '' === $avatars_markup ) {
			return '';
		}

		$output  = '<div class="foundation-inkfire-avatar-group foundation-inkfire-avatar-group--project">';
		$output .= '<span class="foundation-inkfire-meet-text">' . esc_html__( 'Worked on this', 'foundation-elementor-plus' ) . '</span>';
		$output .= '<a href="' . esc_url( $team_link_url ) . '" class="foundation-inkfire-team-strip-link" aria-label="' . esc_attr__( 'Meet the team', 'foundation-elementor-plus' ) . '">';
		$output .= $avatars_markup;
		$output .= '</a>';
		$output .= '</div>';

		return $output;
	}

	private static function is_elementor_editor_preview() {
		if ( ! class_exists( '\Elementor\Plugin' ) ) {
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

	private static function get_render_context_post_id( $preset_key = '' ) {
		if ( is_singular() ) {
			return (int) get_the_ID();
		}

		if ( 'portfolio_post' !== $preset_key || ! self::is_elementor_editor_preview() ) {
			return 0;
		}

		$preview_post_id = self::get_elementor_preview_post_id( 'ink_portfolio' );

		if ( $preview_post_id > 0 ) {
			return $preview_post_id;
		}

		return self::get_fallback_preview_post_id( 'ink_portfolio' );
	}

	private static function get_elementor_preview_post_id( $required_post_type = '' ) {
		if ( ! self::is_elementor_editor_preview() || ! class_exists( '\Elementor\Plugin' ) ) {
			return 0;
		}

		$plugin = \Elementor\Plugin::$instance;

		if ( ! isset( $plugin->documents ) || ! method_exists( $plugin->documents, 'get_current' ) ) {
			return 0;
		}

		$document = $plugin->documents->get_current();

		if ( ! $document || ! method_exists( $document, 'get_settings' ) ) {
			return 0;
		}

		$preview_id   = (int) $document->get_settings( 'preview_id' );
		$preview_type = (string) $document->get_settings( 'preview_type' );

		if ( $preview_id <= 0 ) {
			return 0;
		}

		if ( '' !== $required_post_type ) {
			if ( 'single/' . $required_post_type === $preview_type ) {
				return $preview_id;
			}

			if ( $required_post_type !== get_post_type( $preview_id ) ) {
				return 0;
			}
		}

		return $preview_id;
	}

	private static function get_fallback_preview_post_id( $post_type ) {
		$posts = get_posts(
			array(
				'post_type'      => $post_type,
				'post_status'    => array( 'publish', 'draft', 'private' ),
				'posts_per_page' => 1,
				'orderby'        => 'date',
				'order'          => 'DESC',
				'fields'         => 'ids',
			)
		);

		if ( empty( $posts ) ) {
			return 0;
		}

		return (int) $posts[0];
	}

	private static function normalize_logo_urls( $logos ) {
		if ( is_array( $logos ) ) {
			return array_values(
				array_filter(
					array_map(
						static function ( $logo ) {
							if ( is_array( $logo ) && ! empty( $logo['url'] ) ) {
								return trim( (string) $logo['url'] );
							}

							return trim( (string) $logo );
						},
						$logos
					)
				)
			);
		}

		return self::get_lines( $logos );
	}

	private static function render_trust_bar( $trust_html = '', $trust_label = '', $trust_logos = array() ) {
		$settings = self::get_settings();

		if ( '' !== trim( (string) $trust_html ) ) {
			return $trust_html;
		}

		$logos = self::normalize_logo_urls( $trust_logos );
		if ( empty( $logos ) ) {
			$logos = self::get_lines( $settings['trust_logos'] );
		}
		if ( empty( $logos ) ) {
			return '';
		}

		$label   = '' !== trim( (string) $trust_label ) ? (string) $trust_label : (string) $settings['trust_label'];
		$output  = '<div class="foundation-inkfire-trust-bar" aria-label="' . esc_attr( $label ) . '">';
		$output .= '<span class="foundation-inkfire-trust-label">' . esc_html( $label ) . '</span>';
		$output .= '<ul class="foundation-inkfire-logos" style="list-style:none; margin:0; padding:0;">';

		foreach ( $logos as $logo_url ) {
			$output .= '<li class="foundation-inkfire-logo"><img src="' . esc_url( $logo_url ) . '" alt="" loading="lazy" decoding="async"></li>';
		}

		$output .= '</ul></div>';

		return $output;
	}

	private static function parse_features( $features ) {
		$raw_items = preg_split( '/\r\n|\r|\n|\|/', (string) $features );
		$items     = array();

		foreach ( $raw_items as $item ) {
			$item = trim( (string) $item );
			if ( '' === $item ) {
				continue;
			}

			$items[] = $item;
		}

		return $items;
	}

	private static function render_feature_list( $feature_items, $position ) {
		if ( empty( $feature_items ) || 'hidden' === $position ) {
			return '';
		}

		ob_start();
		?>
		<ul class="foundation-inkfire-feature-list foundation-inkfire-feature-list--<?php echo esc_attr( $position ); ?>" aria-label="<?php esc_attr_e( 'Key service highlights', 'foundation-elementor-plus' ); ?>">
			<?php foreach ( $feature_items as $feature_item ) : ?>
				<li class="foundation-inkfire-feature-item"><?php echo esc_html( $feature_item ); ?></li>
			<?php endforeach; ?>
		</ul>
		<?php
		return ob_get_clean();
	}

	private static function enqueue_icon_fonts() {
		$elementor_icon_handles = array(
			'elementor-icons',
			'elementor-icons-fa-solid',
			'elementor-icons-fa-regular',
			'elementor-icons-fa-brands',
			'elementor-icons-shared-0',
			'elementor-icons-shared-1',
			'elementor-icons-shared-2',
			'elementor-icons-shared-3',
		);

		$enqueued = false;

		foreach ( $elementor_icon_handles as $handle ) {
			if ( wp_style_is( $handle, 'registered' ) ) {
				wp_enqueue_style( $handle );
				$enqueued = true;
			}
		}

		unset( $enqueued );
	}
}
