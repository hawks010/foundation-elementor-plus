<?php

namespace FoundationElementorPlus\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Portfolio_Mosaic_Widget extends Widget_Base {
	public function get_name() {
		return 'foundation-portfolio-mosaic';
	}

	public function get_title() {
		return esc_html__( 'Foundation Dynamic Grid', 'foundation-elementor-plus' );
	}

	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	public function get_categories() {
		return array( \FoundationElementorPlus\Plugin::CATEGORY_SLUG );
	}

	public function get_keywords() {
		return array( 'foundation', 'dynamic', 'grid', 'mosaic', 'portfolio', 'blog', 'archive', 'search', 'posts', 'projects' );
	}

	public function get_style_depends(): array {
		return array( 'foundation-elementor-plus-portfolio-mosaic' );
	}

	public function get_script_depends(): array {
		return array( 'foundation-elementor-plus-portfolio-mosaic' );
	}

	protected function register_controls() {
		$this->register_display_controls();
		$this->register_header_controls();
		$this->register_source_controls();
		$this->register_manual_controls();
		$this->register_query_controls();
		$this->register_embedded_card_controls();
		$this->register_cta_controls();
		$this->register_layout_controls();
		$this->register_heading_style_controls();
		$this->register_card_style_controls();
		$this->register_card_typography_style_controls();
		$this->register_media_style_controls();
	}

	protected function render() {
		$settings  = $this->get_settings_for_display();
		$cards     = $this->get_cards( $settings );
		$cards     = $this->maybe_insert_embedded_shortcode_cards( $cards, $settings );
		$cards     = $this->maybe_insert_cta_card( $cards, $settings );
		$cards     = $this->apply_layout_pattern( $cards, $settings );
		$filters   = $this->get_filter_items( $cards );
			$widget_id = 'foundation-portfolio-mosaic-' . $this->get_id();
			$variant   = $this->normalize_grid_variant( $settings['grid_variant'] ?? 'showcase' );
			$layout_mode = $this->normalize_showcase_layout_mode( $settings['showcase_layout_mode'] ?? 'flexible' );
				$has_cards = ! empty( $cards );
				$initial_visible_cards = ! empty( $settings['initial_visible_cards'] ) ? max( 1, (int) $settings['initial_visible_cards'] ) : 0;
				$load_more_enabled = $initial_visible_cards > 0;
				$load_more_step = ! empty( $settings['load_more_step'] ) ? max( 1, (int) $settings['load_more_step'] ) : 6;

		if ( ! $has_cards && ! $this->should_render_header( $settings ) ) {
			return;
		}

		$section_classes = array(
			'foundation-portfolio-mosaic',
			'foundation-portfolio-mosaic--' . $variant,
		);

		if ( 'showcase' === $variant ) {
			$section_classes[] = 'foundation-portfolio-mosaic--layout-' . $layout_mode;

			if ( 'flexible' === $layout_mode ) {
				$section_classes[] = 'foundation-portfolio-mosaic--showcase-cols-' . $this->normalize_column_mode( $settings['showcase_desktop_columns'] ?? '4' );
				$section_classes[] = 'foundation-portfolio-mosaic--feature-layout-' . $this->normalize_feature_layout( $settings['feature_card_layout'] ?? 'wide' );
			}
		}

		if ( 'compact' === $variant ) {
			$section_classes[] = 'foundation-portfolio-mosaic--compact-cols-' . $this->normalize_column_mode( $settings['compact_desktop_columns'] ?? '3' );
		}

		if ( 'yes' !== ( $settings['enable_card_tilt'] ?? 'yes' ) ) {
			$section_classes[] = 'foundation-portfolio-mosaic--no-tilt';
		}
		?>
		<section
			id="<?php echo esc_attr( $widget_id ); ?>"
				class="<?php echo esc_attr( implode( ' ', $section_classes ) ); ?>"
				data-foundation-portfolio-mosaic
				data-foundation-portfolio-tilt="<?php echo esc_attr( 'yes' === ( $settings['enable_card_tilt'] ?? 'yes' ) ? 'yes' : 'no' ); ?>"
				data-foundation-portfolio-feature-min-columns="<?php echo esc_attr( $this->normalize_feature_span_min_columns( $settings['feature_span_min_columns'] ?? '4' ) ); ?>"
				data-foundation-portfolio-load-more="<?php echo esc_attr( $load_more_enabled ? 'yes' : 'no' ); ?>"
				data-foundation-portfolio-initial-limit="<?php echo esc_attr( $initial_visible_cards ); ?>"
				data-foundation-portfolio-load-more-step="<?php echo esc_attr( $load_more_step ); ?>"
			>
			<div class="foundation-portfolio-mosaic__wrap">
				<?php $this->render_header( $settings ); ?>

				<?php if ( $has_cards ) : ?>
					<?php $this->render_filter_tabs( $settings, $filters, $widget_id ); ?>
						<div class="foundation-portfolio-mosaic__grid" role="list">
							<?php foreach ( $cards as $card ) : ?>
							<?php
							if ( 'cta' === $card['type'] ) {
								$this->render_cta_card( $card, $settings );
								continue;
							}

							if ( 'shortcode' === ( $card['type'] ?? '' ) ) {
								$this->render_shortcode_card( $card, $settings );
								continue;
							}

							$this->render_project_card( $card, $settings );
							?>
							<?php endforeach; ?>
						</div>
						<?php $this->render_load_more_button( $cards, $settings ); ?>
					<?php endif; ?>
				</div>
		</section>
		<?php
	}

	private function register_display_controls() {
		$this->start_controls_section(
			'section_display',
			array(
				'label' => esc_html__( 'Display', 'foundation-elementor-plus' ),
			)
		);

		$this->add_control(
			'grid_variant',
			array(
				'label'   => esc_html__( 'Grid Variant', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'showcase',
				'options' => array(
					'showcase' => esc_html__( 'Showcase Grid', 'foundation-elementor-plus' ),
					'compact'  => esc_html__( 'Compact / Related Grid', 'foundation-elementor-plus' ),
				),
			)
		);

		$this->add_control(
			'display_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'Choose what the card grid should show, then toggle the bits of card content you want visible. Turn the widget header off here if you plan to place your own heading above it.', 'foundation-elementor-plus' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);

		$this->add_control(
			'display_content_heading',
			array(
				'label'     => esc_html__( 'Card Content', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'show_header',
			array(
				'label'        => esc_html__( 'Show Widget Header', 'foundation-elementor-plus' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'foundation-elementor-plus' ),
				'label_off'    => esc_html__( 'No', 'foundation-elementor-plus' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'show_brand_name',
			array(
				'label'        => esc_html__( 'Show Brand Name', 'foundation-elementor-plus' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'foundation-elementor-plus' ),
				'label_off'    => esc_html__( 'No', 'foundation-elementor-plus' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_category_pill',
			array(
				'label'        => esc_html__( 'Show Category Pill', 'foundation-elementor-plus' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'foundation-elementor-plus' ),
				'label_off'    => esc_html__( 'No', 'foundation-elementor-plus' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_badges',
			array(
				'label'        => esc_html__( 'Show Extra Badge', 'foundation-elementor-plus' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'foundation-elementor-plus' ),
				'label_off'    => esc_html__( 'No', 'foundation-elementor-plus' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_kicker',
			array(
				'label'        => esc_html__( 'Show Supporting Label', 'foundation-elementor-plus' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'foundation-elementor-plus' ),
				'label_off'    => esc_html__( 'No', 'foundation-elementor-plus' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'show_media',
			array(
				'label'        => esc_html__( 'Show Image / Video', 'foundation-elementor-plus' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'foundation-elementor-plus' ),
				'label_off'    => esc_html__( 'No', 'foundation-elementor-plus' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

			$this->add_control(
				'show_arrow_icon',
				array(
					'label'        => esc_html__( 'Show Link Icon', 'foundation-elementor-plus' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'foundation-elementor-plus' ),
				'label_off'    => esc_html__( 'No', 'foundation-elementor-plus' ),
				'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'display_paging_heading',
				array(
					'label'     => esc_html__( 'Progressive Reveal', 'foundation-elementor-plus' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$this->add_control(
				'enable_load_more',
				array(
					'label'        => esc_html__( 'Show Load More Button', 'foundation-elementor-plus' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Yes', 'foundation-elementor-plus' ),
					'label_off'    => esc_html__( 'No', 'foundation-elementor-plus' ),
					'return_value' => 'yes',
					'default'      => '',
				)
			);

			$this->add_control(
				'initial_visible_cards',
				array(
					'label'     => esc_html__( 'Cards Shown Initially', 'foundation-elementor-plus' ),
					'type'      => Controls_Manager::NUMBER,
					'default'   => 0,
					'min'       => 1,
					'max'       => 24,
					'condition' => array(
						'enable_load_more' => 'yes',
					),
				)
			);

			$this->add_control(
				'load_more_step',
				array(
					'label'     => esc_html__( 'Cards Added Per Click', 'foundation-elementor-plus' ),
					'type'      => Controls_Manager::NUMBER,
					'default'   => 6,
					'min'       => 1,
					'max'       => 24,
					'condition' => array(
						'enable_load_more' => 'yes',
					),
				)
			);

			$this->add_control(
				'load_more_label',
				array(
					'label'     => esc_html__( 'Load More Label', 'foundation-elementor-plus' ),
					'type'      => Controls_Manager::TEXT,
					'default'   => esc_html__( 'Load More Items', 'foundation-elementor-plus' ),
					'condition' => array(
						'enable_load_more' => 'yes',
					),
				)
			);

			$this->add_control(
				'display_behavior_heading',
			array(
				'label'     => esc_html__( 'Interaction', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'enable_card_tilt',
			array(
				'label'        => esc_html__( 'Enable Hover Tilt', 'foundation-elementor-plus' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'foundation-elementor-plus' ),
				'label_off'    => esc_html__( 'No', 'foundation-elementor-plus' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'card_icon',
			array(
				'label'   => esc_html__( 'Card Link Icon', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-arrow-right',
					'library' => 'fa-solid',
				),
				'condition' => array(
					'show_arrow_icon' => 'yes',
				),
			)
		);

		$this->add_control(
			'cta_icon',
			array(
				'label'   => esc_html__( 'CTA Icon', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-arrow-right',
					'library' => 'fa-solid',
				),
				'condition' => array(
					'show_arrow_icon' => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	private function register_header_controls() {
		$this->start_controls_section(
			'section_header',
			array(
				'label' => esc_html__( 'Header', 'foundation-elementor-plus' ),
			)
		);

		$this->add_control(
			'eyebrow',
			array(
				'label'       => esc_html__( 'Eyebrow', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'New & featured', 'foundation-elementor-plus' ),
				'label_block' => true,
				'dynamic'     => array(
					'active' => true,
				),
				'condition'   => array(
					'show_header' => 'yes',
				),
			)
		);

		$this->add_control(
			'title',
			array(
				'label'       => esc_html__( 'Title', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'Web design work that feels alive on screen', 'foundation-elementor-plus' ),
				'rows'        => 3,
				'label_block' => true,
				'dynamic'     => array(
					'active' => true,
				),
				'condition'   => array(
					'show_header' => 'yes',
				),
			)
		);

		$this->add_control(
			'highlight_text',
			array(
				'label'       => esc_html__( 'Highlight Text', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'design work', 'foundation-elementor-plus' ),
				'label_block' => true,
				'dynamic'     => array(
					'active' => true,
				),
				'condition'   => array(
					'show_header' => 'yes',
				),
			)
		);

		$this->add_control(
			'intro',
			array(
				'label'       => esc_html__( 'Intro Copy', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 3,
				'default'     => '',
				'label_block' => true,
				'dynamic'     => array(
					'active' => true,
				),
				'condition'   => array(
					'show_header' => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	private function register_source_controls() {
		$this->start_controls_section(
			'section_source',
			array(
				'label' => esc_html__( 'Data Source', 'foundation-elementor-plus' ),
			)
		);

		$this->add_control(
			'source_mode',
			array(
				'label'   => esc_html__( 'Source', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'query',
				'options' => array(
					'query'  => esc_html__( 'Dynamic Query', 'foundation-elementor-plus' ),
					'manual' => esc_html__( 'Manual Cards', 'foundation-elementor-plus' ),
				),
			)
		);

		$this->add_control(
			'manual_mode_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'Manual mode supports Elementor dynamic tags on text, media, and links. Dynamic query mode can pull portfolio items, blog posts, or the current archive/search context.', 'foundation-elementor-plus' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);

		$this->end_controls_section();
	}

	private function register_manual_controls() {
		$this->start_controls_section(
			'section_manual_cards',
			array(
				'label'     => esc_html__( 'Manual Cards', 'foundation-elementor-plus' ),
				'condition' => array(
					'source_mode' => 'manual',
				),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'project_name',
			array(
				'label'       => esc_html__( 'Brand Name', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Project Name', 'foundation-elementor-plus' ),
				'label_block' => true,
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$repeater->add_control(
			'category_pill',
			array(
				'label'       => esc_html__( 'Category Pill', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Web Design', 'foundation-elementor-plus' ),
				'label_block' => true,
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$repeater->add_control(
			'badge_text',
			array(
				'label'       => esc_html__( 'Secondary Badge', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'label_block' => true,
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$repeater->add_control(
			'kicker',
			array(
				'label'       => esc_html__( 'Supporting Label', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'label_block' => true,
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$repeater->add_control(
			'headline',
			array(
				'label'       => esc_html__( 'Headline', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 4,
				'default'     => esc_html__( 'A sharp, confident card headline for this project.', 'foundation-elementor-plus' ),
				'label_block' => true,
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$repeater->add_control(
			'description',
			array(
				'label'       => esc_html__( 'Feature Description', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 4,
				'default'     => '',
				'label_block' => true,
				'dynamic'     => array(
					'active' => true,
				),
				'description' => esc_html__( 'Shown beneath the headline on larger featured cards.', 'foundation-elementor-plus' ),
			)
		);

		$repeater->add_control(
			'media_type',
			array(
				'label'   => esc_html__( 'Media Type', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'image',
				'options' => array(
					'image' => esc_html__( 'Image', 'foundation-elementor-plus' ),
					'video' => esc_html__( 'Video', 'foundation-elementor-plus' ),
				),
			)
		);

		$repeater->add_control(
			'image',
			array(
				'label'   => esc_html__( 'Image', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'dynamic' => array(
					'active' => true,
				),
				'condition' => array(
					'media_type' => 'image',
				),
			)
		);

		$repeater->add_control(
			'image_alt',
			array(
				'label'       => esc_html__( 'Image Alt Text Override', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'label_block' => true,
				'dynamic'     => array(
					'active' => true,
				),
				'condition'   => array(
					'media_type' => 'image',
				),
			)
		);

		$repeater->add_control(
			'video_url',
			array(
				'label'       => esc_html__( 'Video URL', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://example.com/video.mp4',
				'dynamic'     => array(
					'active' => true,
				),
				'condition'   => array(
					'media_type' => 'video',
				),
			)
		);

		$repeater->add_control(
			'video_poster',
			array(
				'label'   => esc_html__( 'Video Poster', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::MEDIA,
				'dynamic' => array(
					'active' => true,
				),
				'condition' => array(
					'media_type' => 'video',
				),
			)
		);

		$repeater->add_control(
			'link',
			array(
				'label'       => esc_html__( 'Card Link', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://example.com',
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$repeater->add_control(
			'card_size',
			array(
				'label'   => esc_html__( 'Card Size', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'standard',
				'options' => array(
					'standard' => esc_html__( 'Standard', 'foundation-elementor-plus' ),
					'feature'  => esc_html__( 'Feature', 'foundation-elementor-plus' ),
				),
			)
		);

		$repeater->add_control(
			'card_theme',
			array(
				'label'   => esc_html__( 'Card Theme', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'dark',
				'options' => array(
					'dark'   => esc_html__( 'Dark', 'foundation-elementor-plus' ),
					'light'  => esc_html__( 'Light', 'foundation-elementor-plus' ),
					'accent' => esc_html__( 'Accent', 'foundation-elementor-plus' ),
				),
			)
		);

		$repeater->add_control(
			'hover_color',
			array(
				'label'   => esc_html__( 'Hover Accent', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::COLOR,
				'default' => '#62d0ff',
			)
		);

		$this->add_control(
			'cards',
			array(
				'label'       => esc_html__( 'Cards', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ project_name }}}',
				'default'     => $this->get_default_manual_cards(),
			)
		);

		$this->end_controls_section();
	}

	private function register_query_controls() {
		$this->start_controls_section(
			'section_query',
			array(
				'label'     => esc_html__( 'Dynamic Query', 'foundation-elementor-plus' ),
				'condition' => array(
					'source_mode' => 'query',
				),
			)
		);

		$this->add_control(
			'query_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'Use Related for single portfolio templates, or switch to Current Archive / Search to mirror the page context the widget sits in. Portfolio and blog sources can also be combined for a mixed editorial grid.', 'foundation-elementor-plus' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);

		$this->add_control(
			'query_scope',
			array(
				'label'   => esc_html__( 'Query Mode', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'all',
				'options' => array(
					'all'           => esc_html__( 'Latest / Ordered Items', 'foundation-elementor-plus' ),
					'related'       => esc_html__( 'Related To Current Portfolio', 'foundation-elementor-plus' ),
					'current_query' => esc_html__( 'Current Archive / Search Context', 'foundation-elementor-plus' ),
				),
			)
		);

		$this->add_control(
			'query_content_source',
			array(
				'label'   => esc_html__( 'Content Source', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'portfolio',
				'options' => array(
					'portfolio'            => esc_html__( 'Portfolio Items', 'foundation-elementor-plus' ),
					'portfolio_case_study' => esc_html__( 'Portfolio + Blog Posts', 'foundation-elementor-plus' ),
					'case_study'           => esc_html__( 'Blog Posts / Articles', 'foundation-elementor-plus' ),
				),
				'condition' => array(
					'query_scope!' => 'current_query',
				),
			)
		);

		$this->add_control(
			'query_result_heading',
			array(
				'label'     => esc_html__( 'Result Set', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'posts_per_page',
			array(
				'label'   => esc_html__( 'Posts Per Page', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 12,
				'min'     => 1,
				'max'     => 24,
			)
		);

		$this->add_control(
			'offset',
			array(
				'label'   => esc_html__( 'Offset', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 0,
				'min'     => 0,
				'condition' => array(
					'query_scope!' => 'current_query',
				),
			)
		);

		$this->add_control(
			'orderby',
			array(
				'label'   => esc_html__( 'Order By', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'menu_order',
				'options' => array(
					'menu_order' => esc_html__( 'Menu Order', 'foundation-elementor-plus' ),
					'date'       => esc_html__( 'Date', 'foundation-elementor-plus' ),
					'title'      => esc_html__( 'Title', 'foundation-elementor-plus' ),
					'rand'       => esc_html__( 'Random', 'foundation-elementor-plus' ),
				),
				'condition' => array(
					'query_scope!' => 'current_query',
				),
			)
		);

		$this->add_control(
			'order',
			array(
				'label'   => esc_html__( 'Order', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'ASC',
				'options' => array(
					'ASC'  => esc_html__( 'Ascending', 'foundation-elementor-plus' ),
					'DESC' => esc_html__( 'Descending', 'foundation-elementor-plus' ),
				),
				'condition' => array(
					'query_scope!' => 'current_query',
				),
			)
		);

		$this->add_control(
			'query_matching_heading',
			array(
				'label'     => esc_html__( 'Matching Rules', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'type_filter',
			array(
				'label'       => esc_html__( 'Portfolio Type Slugs', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => 'marketing,web-branding',
				'description' => esc_html__( 'Optional comma-separated type slugs to filter portfolio queries. This is ignored for blog-only and current archive/search modes.', 'foundation-elementor-plus' ),
				'condition'   => array(
					'query_scope!' => 'current_query',
				),
			)
		);

		$this->add_control(
			'exclude_current_post',
			array(
				'label'        => esc_html__( 'Exclude Current Portfolio', 'foundation-elementor-plus' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'foundation-elementor-plus' ),
				'label_off'    => esc_html__( 'No', 'foundation-elementor-plus' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array(
					'query_scope!' => 'current_query',
				),
			)
		);

		$this->add_control(
			'show_term_kicker',
			array(
				'label'        => esc_html__( 'Use First Term As Supporting Label Fallback', 'foundation-elementor-plus' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'foundation-elementor-plus' ),
				'label_off'    => esc_html__( 'No', 'foundation-elementor-plus' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array(
					'query_scope!' => 'current_query',
				),
			)
		);

		$this->add_control(
			'case_study_category_slugs',
			array(
				'label'       => esc_html__( 'Blog Category Slugs', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'case-studies',
				'placeholder' => 'case-studies',
				'description' => esc_html__( 'Comma-separated blog category slugs used when blog posts are included.', 'foundation-elementor-plus' ),
				'condition'   => array(
					'query_scope!' => 'current_query',
					'query_content_source!' => 'portfolio',
				),
			)
		);

		$this->add_control(
			'case_study_excluded_category_slugs',
			array(
				'label'       => esc_html__( 'Ignored Blog Categories', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'blog,featured,case-studies,news',
				'placeholder' => 'blog,featured,case-studies,news',
				'description' => esc_html__( 'These categories are skipped when choosing the blog pill/tab label.', 'foundation-elementor-plus' ),
				'condition'   => array(
					'query_scope!' => 'current_query',
					'query_content_source!' => 'portfolio',
				),
			)
		);

		$this->add_control(
			'filter_tabs_heading',
			array(
				'label'     => esc_html__( 'Filter Tabs', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'show_filter_tabs',
			array(
				'label'        => esc_html__( 'Show Filter Tabs', 'foundation-elementor-plus' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'foundation-elementor-plus' ),
				'label_off'    => esc_html__( 'No', 'foundation-elementor-plus' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'filter_all_label',
			array(
				'label'     => esc_html__( 'All Tab Label', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'All Items', 'foundation-elementor-plus' ),
				'condition' => array(
					'show_filter_tabs' => 'yes',
				),
			)
		);


		$this->end_controls_section();
	}

	private function register_embedded_card_controls() {
		$this->start_controls_section(
			'section_embedded_cards',
			array(
				'label' => esc_html__( 'Embedded Cards', 'foundation-elementor-plus' ),
			)
		);

		$this->add_control(
			'embedded_cards_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'Drop helper shortcodes like the blog footer card or accessibility tools card into any grid layout. Use position mode to inject them before a specific item, or send them to the end of the grid.', 'foundation-elementor-plus' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);

		$this->add_control(
			'post_footer_heading',
			array(
				'label'     => esc_html__( 'Embedded Widget Card', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'show_post_footer_card',
			array(
				'label'        => esc_html__( 'Include [ink_post_footer] Card', 'foundation-elementor-plus' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'foundation-elementor-plus' ),
				'label_off'    => esc_html__( 'No', 'foundation-elementor-plus' ),
				'return_value' => 'yes',
				'default'      => '',
				'description'  => esc_html__( 'Adds the blog footer widget into the grid for article, archive, and search-based layouts.', 'foundation-elementor-plus' ),
			)
		);

		$this->add_control(
			'post_footer_card_insert_mode',
			array(
				'label'     => esc_html__( 'Footer Card Placement', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'end',
				'options'   => array(
					'end'      => esc_html__( 'At End Of Grid', 'foundation-elementor-plus' ),
					'position' => esc_html__( 'Insert At Position', 'foundation-elementor-plus' ),
				),
				'condition' => array(
					'show_post_footer_card' => 'yes',
				),
			)
		);

		$this->add_control(
			'post_footer_card_position',
			array(
				'label'       => esc_html__( 'Footer Card Position', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 3,
				'min'         => 0,
				'description' => esc_html__( 'Zero inserts before the first dynamic item.', 'foundation-elementor-plus' ),
				'condition'   => array(
					'show_post_footer_card'      => 'yes',
					'post_footer_card_insert_mode' => 'position',
				),
			)
		);

		$this->add_control(
			'post_footer_card_size',
			array(
				'label'     => esc_html__( 'Footer Card Size', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'feature',
				'options'   => array(
					'standard' => esc_html__( 'Standard', 'foundation-elementor-plus' ),
					'feature'  => esc_html__( 'Feature', 'foundation-elementor-plus' ),
				),
				'condition' => array(
					'show_post_footer_card' => 'yes',
				),
			)
		);

		$this->add_control(
			'show_a11y_card',
			array(
				'label'        => esc_html__( 'Include [ink_access_meta] Card', 'foundation-elementor-plus' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'foundation-elementor-plus' ),
				'label_off'    => esc_html__( 'No', 'foundation-elementor-plus' ),
				'return_value' => 'yes',
				'default'      => '',
				'description'  => esc_html__( 'Adds the accessibility tools shortcode into the grid with the same placement options as the footer widget card.', 'foundation-elementor-plus' ),
			)
		);

		$this->add_control(
			'a11y_card_insert_mode',
			array(
				'label'     => esc_html__( 'A11y Card Placement', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'end',
				'options'   => array(
					'end'      => esc_html__( 'At End Of Grid', 'foundation-elementor-plus' ),
					'position' => esc_html__( 'Insert At Position', 'foundation-elementor-plus' ),
				),
				'condition' => array(
					'show_a11y_card' => 'yes',
				),
			)
		);

		$this->add_control(
			'a11y_card_position',
			array(
				'label'       => esc_html__( 'A11y Card Position', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 4,
				'min'         => 0,
				'description' => esc_html__( 'Zero inserts before the first dynamic item.', 'foundation-elementor-plus' ),
				'condition'   => array(
					'show_a11y_card'        => 'yes',
					'a11y_card_insert_mode' => 'position',
				),
			)
		);

		$this->add_control(
			'a11y_card_size',
			array(
				'label'     => esc_html__( 'A11y Card Size', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'feature',
				'options'   => array(
					'standard' => esc_html__( 'Standard', 'foundation-elementor-plus' ),
					'feature'  => esc_html__( 'Feature', 'foundation-elementor-plus' ),
				),
				'condition' => array(
					'show_a11y_card' => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	private function register_cta_controls() {
		$this->start_controls_section(
			'section_cta',
			array(
				'label' => esc_html__( 'Promo / Last Card', 'foundation-elementor-plus' ),
			)
		);

		$this->add_control(
			'cta_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'Use this as a service advert, agency promo, or final call-to-action card. It can sit at the end of the grid or be inserted after a specific card.', 'foundation-elementor-plus' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);

		$this->add_control(
			'show_cta_card',
			array(
				'label'        => esc_html__( 'Show CTA Card', 'foundation-elementor-plus' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'foundation-elementor-plus' ),
				'label_off'    => esc_html__( 'No', 'foundation-elementor-plus' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'cta_insert_mode',
			array(
				'label'     => esc_html__( 'Promo Card Placement', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'position',
				'options'   => array(
					'position' => esc_html__( 'After Specific Card', 'foundation-elementor-plus' ),
					'end'      => esc_html__( 'At End Of Grid', 'foundation-elementor-plus' ),
				),
				'condition' => array(
					'show_cta_card' => 'yes',
				),
			)
		);

		$this->add_control(
			'cta_position',
			array(
				'label'       => esc_html__( 'Insert After Card Number', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 0,
				'min'         => 0,
				'description' => esc_html__( 'Use 0 to place the promo card before the first portfolio card.', 'foundation-elementor-plus' ),
				'condition'   => array(
					'show_cta_card'  => 'yes',
					'cta_insert_mode' => 'position',
				),
			)
		);

		$this->add_control(
			'cta_size',
			array(
				'label'     => esc_html__( 'CTA Card Size', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'standard',
				'options'   => array(
					'standard' => esc_html__( 'Standard', 'foundation-elementor-plus' ),
					'feature'  => esc_html__( 'Feature', 'foundation-elementor-plus' ),
				),
				'condition' => array(
					'show_cta_card' => 'yes',
				),
			)
		);

		$this->add_control(
			'cta_theme',
			array(
				'label'     => esc_html__( 'CTA Theme', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'accent',
				'options'   => array(
					'light'  => esc_html__( 'Light', 'foundation-elementor-plus' ),
					'accent' => esc_html__( 'Accent', 'foundation-elementor-plus' ),
					'dark'   => esc_html__( 'Dark', 'foundation-elementor-plus' ),
				),
				'description' => esc_html__( 'Controls the CTA card text tone and the default background family when the CTA background is set to follow theme.', 'foundation-elementor-plus' ),
				'condition' => array(
					'show_cta_card' => 'yes',
				),
			)
		);

		$this->add_control(
			'cta_background_style',
			array(
				'label'     => esc_html__( 'CTA Background', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'use_style',
				'options'   => array(
					'use_style'    => esc_html__( 'Use Style Tab Setting', 'foundation-elementor-plus' ),
					'theme'        => esc_html__( 'Follow CTA Theme', 'foundation-elementor-plus' ),
					'cta_gradient' => esc_html__( 'Ink CTA Gradient', 'foundation-elementor-plus' ),
					'dark'         => esc_html__( 'Dark Glass', 'foundation-elementor-plus' ),
					'navy'         => esc_html__( 'Navy Glass', 'foundation-elementor-plus' ),
					'green'        => esc_html__( 'Green Glass', 'foundation-elementor-plus' ),
					'white'        => esc_html__( 'White Glass', 'foundation-elementor-plus' ),
					'orange'       => esc_html__( 'Orange Glass', 'foundation-elementor-plus' ),
				),
				'description' => esc_html__( 'Pick a direct background here when you want the promo card to ignore the style-tab override.', 'foundation-elementor-plus' ),
				'condition' => array(
					'show_cta_card' => 'yes',
				),
			)
		);

		$this->add_control(
			'cta_kicker',
			array(
				'label'       => esc_html__( 'CTA Kicker', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Start something bold', 'foundation-elementor-plus' ),
				'label_block' => true,
				'dynamic'     => array(
					'active' => true,
				),
				'condition'   => array(
					'show_cta_card' => 'yes',
				),
			)
		);

		$this->add_control(
			'cta_title',
			array(
				'label'       => esc_html__( 'CTA Title', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'Ready to start your next web design project?', 'foundation-elementor-plus' ),
				'rows'        => 3,
				'label_block' => true,
				'dynamic'     => array(
					'active' => true,
				),
				'condition'   => array(
					'show_cta_card' => 'yes',
				),
			)
		);

		$this->add_control(
			'cta_button_text',
			array(
				'label'       => esc_html__( 'CTA Button Text', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Get a quote', 'foundation-elementor-plus' ),
				'label_block' => true,
				'dynamic'     => array(
					'active' => true,
				),
				'condition'   => array(
					'show_cta_card' => 'yes',
				),
			)
		);

		$this->add_control(
			'cta_url',
			array(
				'label'       => esc_html__( 'CTA Link', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://example.com',
				'dynamic'     => array(
					'active' => true,
				),
				'condition'   => array(
					'show_cta_card' => 'yes',
				),
			)
		);

		$this->add_control(
			'cta_hover_color',
			array(
				'label'     => esc_html__( 'CTA Hover Accent', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#9af0ff',
				'condition' => array(
					'show_cta_card' => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	private function register_layout_controls() {
		$this->start_controls_section(
			'section_layout',
			array(
				'label' => esc_html__( 'Layout', 'foundation-elementor-plus' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'layout_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'Start with content width, grid columns, and the featured card layout. Then tune heights only if the cards need more breathing room.', 'foundation-elementor-plus' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);

		$this->add_control(
			'showcase_layout_mode',
			array(
				'label'   => esc_html__( 'Showcase Layout Mode', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'flexible',
				'options' => array(
					'flexible'  => esc_html__( 'Flexible Masonry', 'foundation-elementor-plus' ),
					'editorial' => esc_html__( 'Alternating Editorial Pattern', 'foundation-elementor-plus' ),
				),
				'condition' => array(
					'grid_variant' => 'showcase',
				),
			)
		);

		$this->add_control(
			'editorial_pattern_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'Editorial Pattern locks showcase mode into a 3-column rhythm: one 2x2 hero card, two stacked side cards, then a row of three standard cards, alternating left and right as it repeats. Card order decides which project lands in the larger hero slot.', 'foundation-elementor-plus' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition'       => array(
					'grid_variant'          => 'showcase',
					'showcase_layout_mode' => 'editorial',
				),
			)
		);

		$this->add_responsive_control(
			'section_padding',
			array(
				'label'      => esc_html__( 'Section Padding', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em', 'rem' ),
				'default'    => array(
					'top'      => 0,
					'right'    => 0,
					'bottom'   => 0,
					'left'     => 0,
					'unit'     => 'px',
					'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-portfolio-mosaic' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'content_width',
			array(
				'label'      => esc_html__( 'Content Width', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'default'    => array(
					'unit' => '%',
					'size' => 95,
				),
				'range'      => array(
					'px' => array(
						'min' => 800,
						'max' => 2200,
					),
					'%' => array(
						'min' => 60,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-portfolio-mosaic__wrap' => 'width: {{SIZE}}{{UNIT}}; max-width: none;',
				),
			)
		);

		$this->add_control(
			'layout_grid_heading',
			array(
				'label'     => esc_html__( 'Grid Structure', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'heading_max_width',
			array(
				'label'      => esc_html__( 'Header Max Width', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 760,
				),
				'range'      => array(
					'px' => array(
						'min' => 320,
						'max' => 1400,
					),
					'%' => array(
						'min' => 40,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-portfolio-mosaic__header' => 'max-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'grid_gap',
			array(
				'label'      => esc_html__( 'Grid Gap', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 24,
				),
				'range'      => array(
					'px' => array(
						'min' => 8,
						'max' => 60,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-portfolio-mosaic' => '--foundation-portfolio-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'showcase_desktop_columns',
			array(
				'label'   => esc_html__( 'Showcase Desktop Columns', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '4',
				'options' => array(
					'auto' => esc_html__( 'Auto Fit', 'foundation-elementor-plus' ),
					'3'    => esc_html__( '3 Columns', 'foundation-elementor-plus' ),
					'4'    => esc_html__( '4 Columns', 'foundation-elementor-plus' ),
					'5'    => esc_html__( '5 Columns', 'foundation-elementor-plus' ),
				),
				'condition' => array(
					'grid_variant'          => 'showcase',
					'showcase_layout_mode!' => 'editorial',
				),
			)
		);

		$this->add_control(
			'compact_desktop_columns',
			array(
				'label'   => esc_html__( 'Compact Desktop Columns', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '3',
				'options' => array(
					'auto' => esc_html__( 'Auto Fit', 'foundation-elementor-plus' ),
					'2'    => esc_html__( '2 Columns', 'foundation-elementor-plus' ),
					'3'    => esc_html__( '3 Columns', 'foundation-elementor-plus' ),
					'4'    => esc_html__( '4 Columns', 'foundation-elementor-plus' ),
				),
				'condition' => array(
					'grid_variant' => 'compact',
				),
			)
		);

		$this->add_responsive_control(
			'showcase_column_min',
			array(
				'label'      => esc_html__( 'Showcase Minimum Column Width', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 280,
				),
				'range'      => array(
					'px' => array(
						'min' => 180,
						'max' => 420,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-portfolio-mosaic' => '--foundation-portfolio-column-min: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'grid_variant'          => 'showcase',
					'showcase_layout_mode!' => 'editorial',
				),
			)
		);

		$this->add_responsive_control(
			'compact_column_min',
			array(
				'label'      => esc_html__( 'Compact Minimum Column Width', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 220,
				),
				'range'      => array(
					'px' => array(
						'min' => 160,
						'max' => 360,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-portfolio-mosaic' => '--foundation-portfolio-compact-column-min: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'grid_variant' => 'compact',
				),
			)
		);

		$this->add_responsive_control(
			'card_radius',
			array(
				'label'      => esc_html__( 'Card Radius', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 30,
				),
				'range'      => array(
					'px' => array(
						'min' => 8,
						'max' => 80,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-portfolio-mosaic' => '--foundation-portfolio-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'layout_card_sizing_heading',
			array(
				'label'     => esc_html__( 'Card Sizing', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'media_radius',
			array(
				'label'      => esc_html__( 'Media Radius', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 22,
				),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 64,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-portfolio-mosaic' => '--foundation-portfolio-media-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'glass_blur',
			array(
				'label'      => esc_html__( 'Glass Blur', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 18,
				),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 40,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-portfolio-mosaic' => '--foundation-portfolio-blur: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'standard_min_height',
			array(
				'label'      => esc_html__( 'Standard Card Min Height', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 390,
				),
				'range'      => array(
					'px' => array(
						'min' => 220,
						'max' => 760,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-portfolio-mosaic' => '--foundation-portfolio-card-min-height: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'grid_variant' => 'showcase',
				),
			)
		);

		$this->add_responsive_control(
			'feature_min_height',
			array(
				'label'      => esc_html__( 'Feature Card Min Height', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 560,
				),
				'range'      => array(
					'px' => array(
						'min' => 300,
						'max' => 1000,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-portfolio-mosaic' => '--foundation-portfolio-feature-min-height: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'grid_variant'          => 'showcase',
					'showcase_layout_mode!' => 'editorial',
				),
			)
		);

		$this->add_responsive_control(
			'compact_min_height',
			array(
				'label'      => esc_html__( 'Compact Card Min Height', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 300,
				),
				'range'      => array(
					'px' => array(
						'min' => 180,
						'max' => 560,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-portfolio-mosaic' => '--foundation-portfolio-compact-min-height: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'grid_variant' => 'compact',
				),
			)
		);

		$this->add_responsive_control(
			'media_min_height',
			array(
				'label'      => esc_html__( 'Media Minimum Height', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 210,
				),
				'range'      => array(
					'px' => array(
						'min' => 120,
						'max' => 520,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-portfolio-mosaic' => '--foundation-portfolio-media-min-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'card_padding',
			array(
				'label'      => esc_html__( 'Card Padding', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'rem', '%' ),
				'default'    => array(
					'top'      => 28,
					'right'    => 28,
					'bottom'   => 28,
					'left'     => 28,
					'unit'     => 'px',
					'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-portfolio-mosaic__card, {{WRAPPER}} .foundation-portfolio-mosaic__cta-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'layout_fit_heading',
			array(
				'label'     => esc_html__( 'Fit & Wrapping', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'feature_card_layout',
			array(
				'label'   => esc_html__( 'Featured Card Layout', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'wide',
				'options' => array(
					'wide'  => esc_html__( 'Wide 2x1', 'foundation-elementor-plus' ),
					'large' => esc_html__( 'Large 2x2', 'foundation-elementor-plus' ),
				),
				'condition' => array(
					'grid_variant'          => 'showcase',
					'showcase_layout_mode!' => 'editorial',
				),
			)
		);

		$this->add_control(
			'feature_span_min_columns',
			array(
				'label'   => esc_html__( 'Feature Cards Span When Grid Has', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '4',
				'options' => array(
					'3' => esc_html__( '3+ Columns', 'foundation-elementor-plus' ),
					'4' => esc_html__( '4+ Columns', 'foundation-elementor-plus' ),
					'5' => esc_html__( '5+ Columns', 'foundation-elementor-plus' ),
				),
				'condition' => array(
					'grid_variant'          => 'showcase',
					'showcase_layout_mode!' => 'editorial',
				),
			)
		);

		$this->add_control(
			'meta_stack_threshold',
			array(
				'label'      => esc_html__( 'Stack Pills Below Card Width', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 360,
				),
				'range'      => array(
					'px' => array(
						'min' => 280,
						'max' => 480,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-portfolio-mosaic' => '--foundation-portfolio-meta-stack-threshold: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function register_heading_style_controls() {
		$this->start_controls_section(
			'section_heading_style',
			array(
				'label' => esc_html__( 'Header Style', 'foundation-elementor-plus' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'show_header' => 'yes',
				),
			)
		);

		$this->add_control(
			'eyebrow_color',
			array(
				'label'     => esc_html__( 'Eyebrow Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#9fe7ff',
				'selectors' => array(
					'{{WRAPPER}} .foundation-portfolio-mosaic__eyebrow' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'label'    => esc_html__( 'Eyebrow Typography', 'foundation-elementor-plus' ),
				'name'     => 'eyebrow_typography',
				'selector' => '{{WRAPPER}} .foundation-portfolio-mosaic__eyebrow',
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => esc_html__( 'Title Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .foundation-portfolio-mosaic__title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'highlight_color',
			array(
				'label'     => esc_html__( 'Highlight Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#7de7ff',
				'selectors' => array(
					'{{WRAPPER}} .foundation-portfolio-mosaic__highlight::after' => 'background: linear-gradient(90deg, {{VALUE}} 0%, rgba(255,255,255,0.92) 100%);',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'label'    => esc_html__( 'Header Title Typography', 'foundation-elementor-plus' ),
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .foundation-portfolio-mosaic__title',
			)
		);

		$this->add_control(
			'intro_color',
			array(
				'label'     => esc_html__( 'Intro Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(235, 244, 255, 0.8)',
				'selectors' => array(
					'{{WRAPPER}} .foundation-portfolio-mosaic__intro' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'label'    => esc_html__( 'Intro Copy Typography', 'foundation-elementor-plus' ),
				'name'     => 'intro_typography',
				'selector' => '{{WRAPPER}} .foundation-portfolio-mosaic__intro',
			)
		);

		$this->end_controls_section();
	}

	private function register_card_style_controls() {
		$this->start_controls_section(
			'section_card_style',
			array(
				'label' => esc_html__( 'Card Surfaces', 'foundation-elementor-plus' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'card_style_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'Use glass presets for the quickest Inkfire look. The color controls below are for fine tuning once you have the right preset mix.', 'foundation-elementor-plus' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);

		$this->add_control(
			'standard_glass_preset',
			array(
				'label'   => esc_html__( 'Standard Card Glass Preset', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'inherit',
				'options' => $this->get_glass_preset_options(),
			)
		);

		$this->add_control(
			'feature_glass_preset',
			array(
				'label'   => esc_html__( 'Feature Card Glass Preset', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'navy',
				'options' => $this->get_glass_preset_options(),
			)
		);

		$this->add_control(
			'cta_glass_preset',
			array(
				'label'       => esc_html__( 'CTA Background Override', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'inherit',
				'options' => $this->get_glass_preset_options( true ),
				'description' => esc_html__( 'Used when CTA Background is left on "Use Style Tab Setting". Set this to Theme Default if you want CTA Theme to drive the background.', 'foundation-elementor-plus' ),
			)
		);

		$this->add_control(
			'card_style_surface_heading',
			array(
				'label'     => esc_html__( 'Theme Surfaces', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'dark_card_bg',
			array(
				'label'     => esc_html__( 'Dark Card Background', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#101827',
				'selectors' => array(
					'{{WRAPPER}} .foundation-portfolio-mosaic' => '--foundation-portfolio-dark-bg: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'dark_card_text',
			array(
				'label'     => esc_html__( 'Dark Card Text', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#f7fbff',
				'selectors' => array(
					'{{WRAPPER}} .foundation-portfolio-mosaic' => '--foundation-portfolio-dark-text: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'dark_card_subtle',
			array(
				'label'     => esc_html__( 'Dark Card Meta Text', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(223, 234, 255, 0.82)',
				'selectors' => array(
					'{{WRAPPER}} .foundation-portfolio-mosaic' => '--foundation-portfolio-dark-subtle: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'light_card_bg',
			array(
				'label'     => esc_html__( 'Light Card Background', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#f7faff',
				'selectors' => array(
					'{{WRAPPER}} .foundation-portfolio-mosaic' => '--foundation-portfolio-light-bg: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'light_card_text',
			array(
				'label'     => esc_html__( 'Light Card Text', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#102033',
				'selectors' => array(
					'{{WRAPPER}} .foundation-portfolio-mosaic' => '--foundation-portfolio-light-text: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'light_card_subtle',
			array(
				'label'     => esc_html__( 'Light Card Meta Text', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(16, 32, 51, 0.78)',
				'selectors' => array(
					'{{WRAPPER}} .foundation-portfolio-mosaic' => '--foundation-portfolio-light-subtle: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'accent_card_bg',
			array(
				'label'     => esc_html__( 'Accent Card Background', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e5fff4',
				'selectors' => array(
					'{{WRAPPER}} .foundation-portfolio-mosaic' => '--foundation-portfolio-accent-bg: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'accent_card_text',
			array(
				'label'     => esc_html__( 'Accent Card Text', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#102033',
				'selectors' => array(
					'{{WRAPPER}} .foundation-portfolio-mosaic' => '--foundation-portfolio-accent-text: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'accent_card_subtle',
			array(
				'label'     => esc_html__( 'Accent Card Meta Text', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(16, 32, 51, 0.78)',
				'selectors' => array(
					'{{WRAPPER}} .foundation-portfolio-mosaic' => '--foundation-portfolio-accent-subtle: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'border_color',
			array(
				'label'     => esc_html__( 'Card Border Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255,255,255,0.14)',
				'selectors' => array(
					'{{WRAPPER}} .foundation-portfolio-mosaic' => '--foundation-portfolio-border: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'focus_ring_color',
			array(
				'label'     => esc_html__( 'Focus Ring Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#8be9ff',
				'selectors' => array(
					'{{WRAPPER}} .foundation-portfolio-mosaic' => '--foundation-portfolio-focus-ring: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'card_style_chips_heading',
			array(
				'label'     => esc_html__( 'Pills, Badges & Icons', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'pill_bg',
			array(
				'label'     => esc_html__( 'Category Pill Background', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255,255,255,0.12)',
				'selectors' => array(
					'{{WRAPPER}} .foundation-portfolio-mosaic' => '--foundation-portfolio-pill-bg: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pill_text_color',
			array(
				'label'     => esc_html__( 'Category Pill Text', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .foundation-portfolio-mosaic' => '--foundation-portfolio-pill-text: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pill_border_color',
			array(
				'label'     => esc_html__( 'Category Pill Border', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255,255,255,0.18)',
				'selectors' => array(
					'{{WRAPPER}} .foundation-portfolio-mosaic' => '--foundation-portfolio-pill-border: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'badge_bg',
			array(
				'label'     => esc_html__( 'Badge Background', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(131, 229, 255, 0.16)',
				'selectors' => array(
					'{{WRAPPER}} .foundation-portfolio-mosaic' => '--foundation-portfolio-badge-bg: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'badge_text_color',
			array(
				'label'     => esc_html__( 'Badge Text', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .foundation-portfolio-mosaic' => '--foundation-portfolio-badge-text: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'badge_border_color',
			array(
				'label'     => esc_html__( 'Badge Border', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255,255,255,0.14)',
				'selectors' => array(
					'{{WRAPPER}} .foundation-portfolio-mosaic' => '--foundation-portfolio-badge-border: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'label'     => esc_html__( 'Icon Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .foundation-portfolio-mosaic' => '--foundation-portfolio-icon-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_bg_color',
			array(
				'label'     => esc_html__( 'Icon Background', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255,255,255,0.07)',
				'selectors' => array(
					'{{WRAPPER}} .foundation-portfolio-mosaic' => '--foundation-portfolio-icon-bg: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_border_color',
			array(
				'label'     => esc_html__( 'Icon Border', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255,255,255,0.14)',
				'selectors' => array(
					'{{WRAPPER}} .foundation-portfolio-mosaic' => '--foundation-portfolio-icon-border: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_hover_color',
			array(
				'label'     => esc_html__( 'Icon Hover Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .foundation-portfolio-mosaic' => '--foundation-portfolio-icon-hover-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_hover_bg_color',
			array(
				'label'     => esc_html__( 'Icon Hover Background', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255,255,255,0.14)',
				'selectors' => array(
					'{{WRAPPER}} .foundation-portfolio-mosaic' => '--foundation-portfolio-icon-hover-bg: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_hover_border_color',
			array(
				'label'     => esc_html__( 'Icon Hover Border', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255,255,255,0.24)',
				'selectors' => array(
					'{{WRAPPER}} .foundation-portfolio-mosaic' => '--foundation-portfolio-icon-hover-border: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'card_border',
				'selector' => '{{WRAPPER}} .foundation-portfolio-mosaic__card, {{WRAPPER}} .foundation-portfolio-mosaic__cta-card',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'card_shadow',
				'selector' => '{{WRAPPER}} .foundation-portfolio-mosaic__card, {{WRAPPER}} .foundation-portfolio-mosaic__cta-card',
				'fields_options' => array(
					'box_shadow' => array(
						'default' => array(
							'horizontal' => 0,
							'vertical'   => 26,
							'blur'       => 70,
							'spread'     => 0,
							'color'      => 'rgba(3, 8, 18, 0.32)',
						),
					),
				),
			)
		);

		$this->end_controls_section();
	}

	private function register_card_typography_style_controls() {
		$this->start_controls_section(
			'section_card_typography_style',
			array(
				'label' => esc_html__( 'Card Typography', 'foundation-elementor-plus' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'card_typography_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'Typography here controls the project name, card heading, pills, supporting label, and CTA label independently.', 'foundation-elementor-plus' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'label'    => esc_html__( 'Brand Name Typography', 'foundation-elementor-plus' ),
				'name'     => 'brand_typography',
				'selector' => '{{WRAPPER}} .foundation-portfolio-mosaic__project-name',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'label'    => esc_html__( 'Headline Typography', 'foundation-elementor-plus' ),
				'name'     => 'headline_typography',
				'selector' => '{{WRAPPER}} .foundation-portfolio-mosaic__headline',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'label'    => esc_html__( 'Feature Description Typography', 'foundation-elementor-plus' ),
				'name'     => 'description_typography',
				'selector' => '{{WRAPPER}} .foundation-portfolio-mosaic__description',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'label'    => esc_html__( 'Category Pill & Badge Typography', 'foundation-elementor-plus' ),
				'name'     => 'pill_typography',
				'selector' => '{{WRAPPER}} .foundation-portfolio-mosaic__pill, {{WRAPPER}} .foundation-portfolio-mosaic__badge',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'label'    => esc_html__( 'Supporting Label Typography', 'foundation-elementor-plus' ),
				'name'     => 'kicker_typography',
				'selector' => '{{WRAPPER}} .foundation-portfolio-mosaic__kicker',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'label'    => esc_html__( 'CTA Button Typography', 'foundation-elementor-plus' ),
				'name'     => 'cta_button_typography',
				'selector' => '{{WRAPPER}} .foundation-portfolio-mosaic__cta-button',
			)
		);

		$this->end_controls_section();
	}

	private function register_media_style_controls() {
		$this->start_controls_section(
			'section_media_style',
			array(
				'label' => esc_html__( 'Media Style', 'foundation-elementor-plus' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'media_bg',
			array(
				'label'     => esc_html__( 'Media Background', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(11, 19, 34, 0.55)',
				'selectors' => array(
					'{{WRAPPER}} .foundation-portfolio-mosaic' => '--foundation-portfolio-media-bg: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'media_border',
			array(
				'label'     => esc_html__( 'Media Border', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255,255,255,0.12)',
				'selectors' => array(
					'{{WRAPPER}} .foundation-portfolio-mosaic' => '--foundation-portfolio-media-border: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'media_shadow',
				'selector' => '{{WRAPPER}} .foundation-portfolio-mosaic__media',
				'fields_options' => array(
					'box_shadow' => array(
						'default' => array(
							'horizontal' => 0,
							'vertical'   => 18,
							'blur'       => 42,
							'spread'     => 0,
							'color'      => 'rgba(2, 7, 18, 0.28)',
						),
					),
				),
			)
		);

		$this->end_controls_section();
	}

	private function render_header( array $settings ) {
		if ( ! $this->should_render_header( $settings ) ) {
			return;
		}
		?>
		<header class="foundation-portfolio-mosaic__header">
			<?php if ( ! empty( $settings['eyebrow'] ) ) : ?>
				<p class="foundation-portfolio-mosaic__eyebrow"><?php echo wp_kses( $settings['eyebrow'], $this->get_inline_text_allowed_html() ); ?></p>
			<?php endif; ?>

			<?php if ( ! empty( $settings['title'] ) ) : ?>
				<h2 class="foundation-portfolio-mosaic__title"><?php echo wp_kses_post( $this->get_title_markup( $settings['title'], $settings['highlight_text'] ?? '' ) ); ?></h2>
			<?php endif; ?>

			<?php if ( ! empty( $settings['intro'] ) ) : ?>
				<div class="foundation-portfolio-mosaic__intro"><?php echo wp_kses_post( wpautop( $settings['intro'] ) ); ?></div>
			<?php endif; ?>
		</header>
		<?php
	}

		private function render_filter_tabs( array $settings, array $filters, $widget_id ) {
			if ( 'yes' !== ( $settings['show_filter_tabs'] ?? '' ) || count( $filters ) < 2 ) {
				return;
		}

		$all_label = ! empty( $settings['filter_all_label'] ) ? $settings['filter_all_label'] : esc_html__( 'All Items', 'foundation-elementor-plus' );
		?>
		<div class="foundation-portfolio-mosaic__filters" data-foundation-portfolio-filters>
			<div class="foundation-portfolio-mosaic__filters-scroll" role="tablist" aria-label="<?php esc_attr_e( 'Filter items', 'foundation-elementor-plus' ); ?>">
				<button
					class="foundation-portfolio-mosaic__filter is-active"
					type="button"
					role="tab"
					aria-selected="true"
					data-foundation-portfolio-filter="all"
					data-foundation-portfolio-filter-target="<?php echo esc_attr( $widget_id ); ?>"
				>
					<?php echo esc_html( $all_label ); ?>
				</button>
				<?php foreach ( $filters as $filter ) : ?>
					<button
						class="foundation-portfolio-mosaic__filter"
						type="button"
						role="tab"
						aria-selected="false"
						data-foundation-portfolio-filter="<?php echo esc_attr( $filter['slug'] ); ?>"
						data-foundation-portfolio-filter-target="<?php echo esc_attr( $widget_id ); ?>"
					>
						<?php echo esc_html( $filter['label'] ); ?>
					</button>
				<?php endforeach; ?>
			</div>
			</div>
			<?php
		}

		private function render_load_more_button( array $cards, array $settings ) {
			$initial_visible_cards = ! empty( $settings['initial_visible_cards'] ) ? max( 1, (int) $settings['initial_visible_cards'] ) : 0;

			if ( $initial_visible_cards < 1 ) {
				return;
			}

			$label = ! empty( $settings['load_more_label'] ) ? $settings['load_more_label'] : esc_html__( 'Load More Items', 'foundation-elementor-plus' );
			?>
			<div class="foundation-portfolio-mosaic__load-more-wrap" data-foundation-portfolio-load-more-wrap hidden>
				<button
					class="foundation-portfolio-mosaic__load-more"
					type="button"
					data-foundation-portfolio-load-more-button
					hidden
				>
					<?php echo esc_html( $label ); ?>
				</button>
			</div>
			<?php
		}

	private function render_project_card( array $card, array $settings ) {
		$glass_preset = $this->resolve_card_glass_preset( $card, $settings, 'portfolio' );
		$category_label = isset( $card['category_pill'] ) ? trim( (string) $card['category_pill'] ) : '';
		$badge_label    = isset( $card['badge_text'] ) ? trim( (string) $card['badge_text'] ) : '';
		$classes = array(
			'foundation-portfolio-mosaic__card-shell',
			'foundation-portfolio-mosaic__theme-' . $card['card_theme'],
			'foundation-portfolio-mosaic__glass-' . $glass_preset,
		);

		if ( ! empty( $card['layout_classes'] ) && is_array( $card['layout_classes'] ) ) {
			$classes = array_merge( $classes, $card['layout_classes'] );
		}

		if ( $this->is_featured_card( $card ) ) {
			$classes[] = 'is-featured';
		}

		$style             = '--foundation-portfolio-hover:' . esc_attr( $card['hover_color'] ) . ';';
		$tag               = ! empty( $card['url']['url'] ) ? 'a' : 'div';
		$link_attributes   = $this->get_link_attribute_string( $card['url'] ?? array() );
		$card_icon_markup  = 'yes' === ( $settings['show_arrow_icon'] ?? 'yes' ) ? $this->get_icon_markup( $settings['card_icon'] ?? array(), 'foundation-portfolio-mosaic__icon-svg' ) : '';
		$show_brand_name   = 'yes' === ( $settings['show_brand_name'] ?? 'yes' );
		$show_category     = 'yes' === ( $settings['show_category_pill'] ?? 'yes' ) && '' !== $category_label;
		$show_badge        = 'yes' === ( $settings['show_badges'] ?? 'yes' ) && '' !== $badge_label && 0 !== strcasecmp( $badge_label, $category_label );
		$show_kicker       = 'yes' === ( $settings['show_kicker'] ?? '' ) && ! empty( $card['kicker'] );
		$card_label        = $this->get_card_link_label( $card );
		$show_description  = $this->is_featured_card( $card ) && ! empty( $card['description'] );
		$filter_attribute  = $this->get_filter_data_attribute( $card );
		?>
		<article class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" style="<?php echo esc_attr( $style ); ?>" data-foundation-portfolio-mosaic-card <?php echo $filter_attribute; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> role="listitem">
			<<?php echo esc_html( $tag ); ?>
				class="foundation-portfolio-mosaic__card"
				<?php if ( 'a' === $tag ) : ?>
					href="<?php echo esc_url( $card['url']['url'] ); ?>"
					aria-label="<?php echo esc_attr( $card_label ); ?>"
					<?php echo $link_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php endif; ?>
			>
				<div class="foundation-portfolio-mosaic__content">
					<div class="foundation-portfolio-mosaic__meta">
						<div class="foundation-portfolio-mosaic__brand-stack">
							<?php if ( $show_brand_name ) : ?>
								<p class="foundation-portfolio-mosaic__project-name"><?php echo esc_html( $card['project_name'] ); ?></p>
							<?php endif; ?>

							<?php if ( $show_kicker ) : ?>
								<p class="foundation-portfolio-mosaic__kicker"><?php echo esc_html( $card['kicker'] ); ?></p>
							<?php endif; ?>
						</div>

							<div class="foundation-portfolio-mosaic__meta-side">
								<?php if ( $show_category ) : ?>
									<span class="foundation-portfolio-mosaic__pill"><?php echo esc_html( $category_label ); ?></span>
								<?php endif; ?>

								<?php if ( $show_badge ) : ?>
									<span class="foundation-portfolio-mosaic__badge"><?php echo esc_html( $badge_label ); ?></span>
								<?php endif; ?>

							<?php if ( $card_icon_markup ) : ?>
								<span class="foundation-portfolio-mosaic__icon" aria-hidden="true"><?php echo $card_icon_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
							<?php endif; ?>
						</div>
					</div>

					<?php if ( ! empty( $card['headline'] ) ) : ?>
						<h3 class="foundation-portfolio-mosaic__headline"><?php echo esc_html( $card['headline'] ); ?></h3>
					<?php endif; ?>

					<?php if ( $show_description ) : ?>
						<p class="foundation-portfolio-mosaic__description"><?php echo esc_html( $card['description'] ); ?></p>
					<?php endif; ?>
				</div>

				<?php if ( 'yes' === ( $settings['show_media'] ?? 'yes' ) ) : ?>
					<div class="foundation-portfolio-mosaic__media-wrap">
						<?php $this->render_media( $card ); ?>
					</div>
				<?php endif; ?>
			</<?php echo esc_html( $tag ); ?>>
		</article>
		<?php
	}

	private function render_cta_card( array $card, array $settings ) {
		$glass_preset = $this->resolve_card_glass_preset( $card, $settings, 'cta' );
		$classes = array(
			'foundation-portfolio-mosaic__card-shell',
			'foundation-portfolio-mosaic__theme-' . $card['card_theme'],
			'foundation-portfolio-mosaic__glass-' . $glass_preset,
			'is-cta',
		);

		if ( ! empty( $card['layout_classes'] ) && is_array( $card['layout_classes'] ) ) {
			$classes = array_merge( $classes, $card['layout_classes'] );
		}

		if ( $this->is_featured_card( $card ) ) {
			$classes[] = 'is-featured';
		}

		$style            = '--foundation-portfolio-hover:' . esc_attr( $card['hover_color'] ) . ';';
		$tag              = ! empty( $card['url']['url'] ) ? 'a' : 'div';
		$link_attributes  = $this->get_link_attribute_string( $card['url'] ?? array() );
		$cta_icon_markup  = 'yes' === ( $settings['show_arrow_icon'] ?? 'yes' ) ? $this->get_icon_markup( $settings['cta_icon'] ?? array(), 'foundation-portfolio-mosaic__icon-svg' ) : '';
		?>
		<article class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" style="<?php echo esc_attr( $style ); ?>" data-foundation-portfolio-mosaic-card data-foundation-portfolio-static-card="yes" role="listitem">
			<<?php echo esc_html( $tag ); ?>
				class="foundation-portfolio-mosaic__cta-card"
				<?php if ( 'a' === $tag ) : ?>
					href="<?php echo esc_url( $card['url']['url'] ); ?>"
					aria-label="<?php echo esc_attr( $this->get_cta_link_label( $card ) ); ?>"
					<?php echo $link_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php endif; ?>
			>
				<div class="foundation-portfolio-mosaic__cta-copy">
					<?php if ( ! empty( $card['kicker'] ) ) : ?>
						<p class="foundation-portfolio-mosaic__kicker"><?php echo esc_html( $card['kicker'] ); ?></p>
					<?php endif; ?>

					<?php if ( ! empty( $card['headline'] ) ) : ?>
						<h3 class="foundation-portfolio-mosaic__headline"><?php echo esc_html( $card['headline'] ); ?></h3>
					<?php endif; ?>
				</div>

				<?php if ( ! empty( $card['button_text'] ) ) : ?>
					<span class="foundation-portfolio-mosaic__cta-button">
						<span class="foundation-portfolio-mosaic__cta-label"><?php echo esc_html( $card['button_text'] ); ?></span>
						<?php if ( $cta_icon_markup ) : ?>
							<span class="foundation-portfolio-mosaic__icon" aria-hidden="true"><?php echo $cta_icon_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						<?php endif; ?>
					</span>
				<?php endif; ?>
			</<?php echo esc_html( $tag ); ?>>
		</article>
		<?php
	}

	private function render_shortcode_card( array $card, array $settings ) {
		$classes = array(
			'foundation-portfolio-mosaic__card-shell',
			'foundation-portfolio-mosaic__card-shell--shortcode',
		);

		if ( ! empty( $card['layout_classes'] ) && is_array( $card['layout_classes'] ) ) {
			$classes = array_merge( $classes, $card['layout_classes'] );
		}

		if ( $this->is_post_footer_card( $card ) ) {
			$classes[] = 'foundation-portfolio-mosaic__card-shell--post-footer';
		}

		if ( $this->is_a11y_card( $card ) ) {
			$classes[] = 'foundation-portfolio-mosaic__card-shell--a11y';
		}

		if ( $this->is_support_shortcode_card( $card ) ) {
			$classes[] = 'foundation-portfolio-mosaic__card-shell--support-shortcode';
		}

		if ( $this->is_featured_card( $card ) ) {
			$classes[] = 'is-featured';
		}

		$style = '--foundation-portfolio-hover:' . esc_attr( $card['hover_color'] ?? '#f6b59f' ) . ';';
		?>
		<article class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" style="<?php echo esc_attr( $style ); ?>" data-foundation-portfolio-mosaic-card data-foundation-portfolio-static-card="yes" role="listitem">
			<div class="foundation-portfolio-mosaic__shortcode-card">
				<div class="foundation-portfolio-mosaic__shortcode-content">
					<?php echo do_shortcode( wp_kses_post( $card['shortcode'] ?? '' ) ); ?>
				</div>
			</div>
		</article>
		<?php
	}

	private function render_media( array $card ) {
		if ( 'video' === $card['media_type'] && ! empty( $card['video_url'] ) ) {
			?>
			<div class="foundation-portfolio-mosaic__media foundation-portfolio-mosaic__media--video">
				<video
					src="<?php echo esc_url( $card['video_url'] ); ?>"
					<?php echo ! empty( $card['poster_url'] ) ? 'poster="' . esc_url( $card['poster_url'] ) . '"' : ''; ?>
					autoplay
					muted
					loop
					playsinline
					preload="metadata"
					aria-hidden="true"
				></video>
			</div>
			<?php
			return;
		}

		$image_url       = ! empty( $card['image_url'] ) ? $card['image_url'] : '';
		$image_id        = ! empty( $card['image_id'] ) ? (int) $card['image_id'] : 0;
		$image_alt       = ! empty( $card['image_alt'] ) ? $card['image_alt'] : $card['project_name'];
		$is_featured     = $this->is_featured_card( $card );
		$attachment_size = $is_featured ? 'full' : 'large';
		$image_sizes     = $is_featured
			? '(min-width: 1500px) 42vw, (min-width: 1100px) 50vw, (min-width: 768px) 50vw, 100vw'
			: '(min-width: 1500px) 22vw, (min-width: 1100px) 26vw, (min-width: 768px) 50vw, 100vw';

		if ( '' === $image_url ) {
			?>
			<div class="foundation-portfolio-mosaic__media foundation-portfolio-mosaic__media--placeholder" aria-hidden="true">
				<span class="foundation-portfolio-mosaic__placeholder-kicker"><?php esc_html_e( 'Inkfire', 'foundation-elementor-plus' ); ?></span>
				<span class="foundation-portfolio-mosaic__placeholder-label"><?php esc_html_e( 'Media coming soon', 'foundation-elementor-plus' ); ?></span>
			</div>
			<?php
			return;
		}

		?>
		<div class="foundation-portfolio-mosaic__media foundation-portfolio-mosaic__media--image">
			<?php
			if ( $image_id > 0 ) {
				echo wp_get_attachment_image(
					$image_id,
					$attachment_size,
					false,
					array(
						'alt'      => $image_alt,
						'loading'  => 'lazy',
						'decoding' => 'async',
						'sizes'    => $image_sizes,
					)
				); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			} else {
				?>
				<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>" loading="lazy" decoding="async" sizes="<?php echo esc_attr( $image_sizes ); ?>">
				<?php
			}
			?>
		</div>
		<?php
	}

	private function get_cards( array $settings ) {
		if ( 'manual' === ( $settings['source_mode'] ?? 'query' ) ) {
			return $this->get_manual_cards( $settings );
		}

		return $this->get_query_cards( $settings );
	}

	private function normalize_query_scope( $value ) {
		return in_array( (string) $value, array( 'all', 'related', 'current_query' ), true ) ? (string) $value : 'all';
	}

	private function get_manual_cards( array $settings ) {
		$cards = array();
		$items = ! empty( $settings['cards'] ) && is_array( $settings['cards'] ) ? $settings['cards'] : array();

		foreach ( $items as $item ) {
			$image_alt = ! empty( $item['image_alt'] ) ? $item['image_alt'] : $this->get_media_alt( $item['image'] ?? array(), $item['project_name'] ?? '' );
			$category  = ! empty( $item['category_pill'] ) ? $item['category_pill'] : '';

			$cards[] = array(
				'type'          => 'portfolio',
				'project_name'  => ! empty( $item['project_name'] ) ? $item['project_name'] : esc_html__( 'Project Name', 'foundation-elementor-plus' ),
				'category_pill' => $category,
				'badge_text'    => ! empty( $item['badge_text'] ) ? $item['badge_text'] : '',
				'kicker'        => ! empty( $item['kicker'] ) ? $item['kicker'] : '',
				'headline'      => ! empty( $item['headline'] ) ? $item['headline'] : '',
				'description'   => ! empty( $item['description'] ) ? $item['description'] : '',
				'media_type'    => 'video' === ( $item['media_type'] ?? 'image' ) ? 'video' : 'image',
				'video_url'     => ! empty( $item['video_url']['url'] ) ? $item['video_url']['url'] : '',
				'poster_url'    => $this->get_media_url( $item['video_poster'] ?? array() ),
				'image_url'     => $this->get_media_url( $item['image'] ?? array() ),
				'image_id'      => ! empty( $item['image']['id'] ) ? (int) $item['image']['id'] : 0,
				'image_alt'     => $image_alt,
				'card_size'     => $this->normalize_card_size( $item['card_size'] ?? 'standard' ),
				'card_theme'    => $this->normalize_card_theme( $item['card_theme'] ?? 'dark' ),
				'hover_color'   => ! empty( $item['hover_color'] ) ? $item['hover_color'] : '#62d0ff',
				'is_featured'   => false,
				'layout_classes'=> array(),
				'url'           => $this->normalize_url_setting( $item['link'] ?? array() ),
				'filter_terms'  => $category ? array(
					array(
						'slug'  => sanitize_title( $category ),
						'label' => $category,
					),
				) : array(),
			);
		}

		return array_values( array_filter( $cards ) );
	}

	private function get_query_cards( array $settings ) {
		$query_scope    = $this->normalize_query_scope( $settings['query_scope'] ?? 'all' );
		$content_source = $settings['query_content_source'] ?? 'portfolio';
		$posts_per_page = ! empty( $settings['posts_per_page'] ) ? max( 1, (int) $settings['posts_per_page'] ) : 12;

		if ( 'current_query' === $query_scope ) {
			return $this->get_current_query_cards( $settings, $posts_per_page );
		}

		$current_post_id = $this->get_current_portfolio_id();
		$args            = array(
			'post_type'           => 'ink_portfolio',
			'post_status'         => 'publish',
			'posts_per_page'      => $posts_per_page,
			'offset'              => ! empty( $settings['offset'] ) ? max( 0, (int) $settings['offset'] ) : 0,
			'orderby'             => ! empty( $settings['orderby'] ) ? $settings['orderby'] : 'menu_order',
			'order'               => ! empty( $settings['order'] ) ? $settings['order'] : 'ASC',
			'ignore_sticky_posts' => true,
		);

		$tax_query = array();
		$type_slugs = array_values(
			array_filter(
				array_map(
					'sanitize_title',
					array_map( 'trim', explode( ',', (string) ( $settings['type_filter'] ?? '' ) ) )
				)
			)
		);

		if ( ! empty( $type_slugs ) ) {
			$tax_query[] = array(
				'taxonomy' => 'ink_portfolio_type',
				'field'    => 'slug',
				'terms'    => $type_slugs,
			);
		}

		if ( 'related' === $query_scope && $current_post_id ) {
			$related_terms = wp_get_post_terms( $current_post_id, 'ink_portfolio_type', array( 'fields' => 'ids' ) );

			if ( ! is_wp_error( $related_terms ) && ! empty( $related_terms ) ) {
				$tax_query[] = array(
					'taxonomy' => 'ink_portfolio_type',
					'field'    => 'term_id',
					'terms'    => array_map( 'intval', $related_terms ),
				);
			}
		}

		if ( count( $tax_query ) > 1 ) {
			$args['tax_query'] = array_merge( array( 'relation' => 'AND' ), $tax_query );
		} elseif ( 1 === count( $tax_query ) ) {
			$args['tax_query'] = $tax_query;
		}

		if ( $current_post_id && 'yes' === ( $settings['exclude_current_post'] ?? 'yes' ) ) {
			$args['post__not_in'] = array( $current_post_id );
		}

		$portfolio_cards = array();
		$case_study_cards = array();

		if ( 'case_study' !== $content_source ) {
			$query = new \WP_Query( $args );

			if ( $query->have_posts() ) {
				while ( $query->have_posts() ) {
					$query->the_post();
					$portfolio_cards[] = $this->build_portfolio_query_card( get_the_ID(), $settings );
				}

				wp_reset_postdata();
			}
		}

		if ( 'portfolio' !== $content_source ) {
			$case_study_cards = $this->get_case_study_cards( $settings, $posts_per_page );
		}

		return $this->merge_query_cards( $portfolio_cards, $case_study_cards, $settings, $posts_per_page );
	}

	private function get_current_query_cards( array $settings, $limit ) {
		$query_vars = array();

		if ( isset( $GLOBALS['wp_query'] ) && $GLOBALS['wp_query'] instanceof \WP_Query && ! empty( $GLOBALS['wp_query']->query_vars ) ) {
			$query_vars = $GLOBALS['wp_query']->query_vars;
		}

		if ( empty( $query_vars ) ) {
			$query_vars = array(
				'post_type'           => 'post',
				'post_status'         => 'publish',
				'orderby'             => ! empty( $settings['orderby'] ) ? $settings['orderby'] : 'date',
				'order'               => ! empty( $settings['order'] ) ? $settings['order'] : 'DESC',
				'ignore_sticky_posts' => true,
			);
		}

		$query_vars['posts_per_page'] = max( 1, (int) $limit );
		$query_vars['post_status']    = 'publish';
		$query_vars['no_found_rows']  = true;
		$query_vars['ignore_sticky_posts'] = true;

		$query = new \WP_Query( $query_vars );
		$cards  = array();

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$cards[] = $this->build_dynamic_query_card( get_the_ID(), $settings );
			}

			wp_reset_postdata();
		}

		return $cards;
	}

	private function build_dynamic_query_card( $post_id, array $settings ) {
		$post_type = get_post_type( $post_id );

		if ( 'ink_portfolio' === $post_type ) {
			return $this->build_portfolio_query_card( $post_id, $settings );
		}

		return $this->build_generic_query_card( $post_id, $settings );
	}

	private function build_generic_query_card( $post_id, array $settings, array $ignored_slugs = array() ) {
		$post_type   = get_post_type( $post_id ) ?: 'post';
		$post_title  = get_the_title( $post_id );
		$terms       = $this->get_dynamic_filter_terms( $post_id, $post_type, $ignored_slugs );
		$term_label  = ! empty( $terms ) ? $terms[0]['label'] : $this->get_dynamic_item_label( $post_type );
		$thumb_id    = get_post_thumbnail_id( $post_id );
		$image_url   = get_the_post_thumbnail_url( $post_id, 'large' );
		$excerpt     = trim( (string) get_the_excerpt( $post_id ) );
		$content     = get_post_field( 'post_content', $post_id );
		$headline    = $excerpt ? $excerpt : wp_trim_words( wp_strip_all_tags( strip_shortcodes( $content ) ), 16 );
		$description = wp_trim_words( wp_strip_all_tags( strip_shortcodes( $content ) ), 24 );

		return array(
			'type'          => 'dynamic',
			'post_type'     => $post_type,
			'source_kind'   => $post_type,
			'post_id'       => $post_id,
			'project_name'  => $post_title ? $post_title : $this->get_dynamic_item_label( $post_type ),
			'category_pill' => $term_label,
			'badge_text'    => $this->get_dynamic_item_label( $post_type ),
			'kicker'        => $term_label,
			'headline'      => $this->limit_card_text( $headline, 118 ),
			'description'   => $this->limit_card_text( $description, 170 ),
			'media_type'    => 'image',
			'video_url'     => '',
			'poster_url'    => '',
			'image_url'     => $image_url,
			'image_id'      => $thumb_id,
			'image_alt'     => $this->get_attachment_alt( $thumb_id, $post_title ),
			'card_size'     => 'standard',
			'card_theme'    => 'dark',
			'hover_color'   => '#62d0ff',
			'is_featured'   => false,
			'layout_classes'=> array(),
			'filter_terms'  => $terms,
			'url'           => array(
				'url'         => get_permalink( $post_id ),
				'is_external' => false,
				'nofollow'    => false,
			),
			'sort_date'     => (string) get_post_field( 'post_date', $post_id ),
			'sort_title'    => $post_title,
			'sort_order'    => 9999,
		);
	}

	private function build_portfolio_query_card( $post_id, array $settings ) {
		$project_name = get_the_title( $post_id );
		$type_terms   = $this->get_post_filter_terms( $post_id, 'ink_portfolio_type' );
		$type_label   = ! empty( $type_terms ) ? $type_terms[0]['label'] : '';
		$kicker       = (string) get_post_meta( $post_id, '_ink_portfolio_grid_kicker', true );
		$link_url     = (string) get_post_meta( $post_id, '_ink_portfolio_card_link_url', true );
		$thumb_id     = get_post_thumbnail_id( $post_id );
		$image_url    = get_the_post_thumbnail_url( $post_id, 'large' );

		return array(
			'type'          => 'portfolio',
			'post_type'     => 'ink_portfolio',
			'source_kind'   => 'portfolio',
			'post_id'       => $post_id,
			'project_name'  => $project_name,
			'category_pill' => $type_label,
			'badge_text'    => (string) get_post_meta( $post_id, '_ink_portfolio_grid_badge', true ),
			'kicker'        => $kicker ? $kicker : ( 'yes' === ( $settings['show_term_kicker'] ?? 'yes' ) ? $type_label : '' ),
			'headline'      => $this->get_portfolio_headline( $post_id ),
			'description'   => $this->get_portfolio_description( $post_id ),
			'media_type'    => 'video' === get_post_meta( $post_id, '_ink_portfolio_media_type', true ) ? 'video' : 'image',
			'video_url'     => (string) get_post_meta( $post_id, '_ink_portfolio_video_url', true ),
			'poster_url'    => (string) get_post_meta( $post_id, '_ink_portfolio_video_poster', true ),
			'image_url'     => $image_url ? $image_url : (string) get_post_meta( $post_id, '_ink_portfolio_video_poster', true ),
			'image_id'      => $thumb_id,
			'image_alt'     => $this->get_attachment_alt( $thumb_id, $project_name ),
			'card_size'     => $this->normalize_card_size( (string) get_post_meta( $post_id, '_ink_portfolio_card_size', true ) ),
			'card_theme'    => $this->normalize_card_theme( (string) get_post_meta( $post_id, '_ink_portfolio_card_theme', true ) ),
			'hover_color'   => (string) get_post_meta( $post_id, '_ink_portfolio_hover_color', true ) ?: '#62d0ff',
			'is_featured'   => false,
			'layout_classes'=> array(),
			'filter_terms'  => $type_terms,
			'url'           => array(
				'url'         => $link_url ? $link_url : get_permalink( $post_id ),
				'is_external' => '1' === (string) get_post_meta( $post_id, '_ink_portfolio_card_link_new_tab', true ),
				'nofollow'    => false,
			),
			'sort_date'     => (string) get_post_field( 'post_date', $post_id ),
			'sort_title'    => $project_name,
			'sort_order'    => (int) get_post_field( 'menu_order', $post_id ),
		);
	}

	private function get_case_study_cards( array $settings, $limit ) {
		$category_slugs = $this->parse_slug_list( $settings['case_study_category_slugs'] ?? 'case-studies' );
		$ignore_slugs   = $this->parse_slug_list( $settings['case_study_excluded_category_slugs'] ?? 'blog,featured,case-studies,news' );
		$args           = array(
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'posts_per_page'      => max( 1, $limit ),
			'ignore_sticky_posts' => true,
			'orderby'             => 'date',
			'order'               => 'DESC',
		);

		if ( ! empty( $category_slugs ) ) {
			$args['category_name'] = implode( ',', $category_slugs );
		}

		$query = new \WP_Query( $args );
		$cards = array();

			if ( $query->have_posts() ) {
				while ( $query->have_posts() ) {
					$query->the_post();
					$cards[] = $this->build_generic_query_card( get_the_ID(), $settings, $ignore_slugs );
				}

			wp_reset_postdata();
		}

		return $cards;
	}

	private function merge_query_cards( array $portfolio_cards, array $case_study_cards, array $settings, $limit ) {
		$content_source = $settings['query_content_source'] ?? 'portfolio';
		$cards          = array();

		if ( 'case_study' === $content_source ) {
			$cards = $case_study_cards;
		} elseif ( 'portfolio_case_study' === $content_source ) {
			$cards = array_merge( $portfolio_cards, $case_study_cards );
		} else {
			$cards = $portfolio_cards;
		}

		$orderby = ! empty( $settings['orderby'] ) ? $settings['orderby'] : 'menu_order';
		$order   = ! empty( $settings['order'] ) ? strtoupper( $settings['order'] ) : 'ASC';

		if ( 'portfolio_case_study' === $content_source ) {
			if ( 'date' === $orderby ) {
				usort(
					$cards,
					static function( $left, $right ) use ( $order ) {
						$result = strcmp( (string) $left['sort_date'], (string) $right['sort_date'] );
						return 'DESC' === $order ? -$result : $result;
					}
				);
			} elseif ( 'title' === $orderby ) {
				usort(
					$cards,
					static function( $left, $right ) use ( $order ) {
						$result = strcasecmp( (string) $left['sort_title'], (string) $right['sort_title'] );
						return 'DESC' === $order ? -$result : $result;
					}
				);
			} elseif ( 'rand' === $orderby ) {
				shuffle( $cards );
			} else {
				usort(
					$cards,
					static function( $left, $right ) {
						if ( $left['sort_order'] === $right['sort_order'] ) {
							return strcmp( (string) $left['sort_title'], (string) $right['sort_title'] );
						}

						return (int) $left['sort_order'] <=> (int) $right['sort_order'];
					}
				);
			}
		}

		return array_slice( $cards, 0, max( 1, (int) $limit ) );
	}

	private function get_dynamic_filter_terms( $post_id, $post_type, array $ignored_slugs = array() ) {
		if ( 'ink_portfolio' === $post_type ) {
			return $this->get_post_filter_terms( $post_id, 'ink_portfolio_type' );
		}

		if ( 'post' === $post_type ) {
			return $this->get_post_category_filter_terms( $post_id, $ignored_slugs );
		}

		$taxonomies = get_object_taxonomies( $post_type, 'objects' );
		$filters    = array();

		if ( empty( $taxonomies ) || ! is_array( $taxonomies ) ) {
			return $filters;
		}

		foreach ( $taxonomies as $taxonomy ) {
			if ( ! $taxonomy instanceof \WP_Taxonomy || empty( $taxonomy->name ) || 'post_format' === $taxonomy->name ) {
				continue;
			}

			$terms = get_the_terms( $post_id, $taxonomy->name );

			if ( is_wp_error( $terms ) || empty( $terms ) ) {
				continue;
			}

			foreach ( $terms as $term ) {
				if ( ! $term instanceof \WP_Term || in_array( $term->slug, $ignored_slugs, true ) ) {
					continue;
				}

				$filters[] = array(
					'slug'  => $term->slug,
					'label' => html_entity_decode( wp_strip_all_tags( $term->name ), ENT_QUOTES, get_bloginfo( 'charset' ) ),
				);
			}

			if ( ! empty( $filters ) ) {
				break;
			}
		}

		return $filters;
	}

	private function get_dynamic_item_label( $post_type ) {
		switch ( $post_type ) {
			case 'ink_portfolio':
				return esc_html__( 'Portfolio Project', 'foundation-elementor-plus' );
			case 'post':
				return esc_html__( 'Article', 'foundation-elementor-plus' );
			case 'page':
				return esc_html__( 'Page', 'foundation-elementor-plus' );
			default:
				$post_type_object = get_post_type_object( $post_type );

				if ( $post_type_object instanceof \WP_Post_Type && ! empty( $post_type_object->labels->singular_name ) ) {
					return html_entity_decode( wp_strip_all_tags( $post_type_object->labels->singular_name ), ENT_QUOTES, get_bloginfo( 'charset' ) );
				}

				return esc_html__( 'Item', 'foundation-elementor-plus' );
		}
	}

	private function maybe_insert_cta_card( array $cards, array $settings ) {
			if ( 'yes' !== ( $settings['show_cta_card'] ?? '' ) ) {
				return $cards;
		}

		$cta_card = array(
			'type'             => 'cta',
			'kicker'           => ! empty( $settings['cta_kicker'] ) ? $settings['cta_kicker'] : '',
			'headline'         => ! empty( $settings['cta_title'] ) ? $settings['cta_title'] : esc_html__( 'Ready to start your next project?', 'foundation-elementor-plus' ),
			'button_text'      => ! empty( $settings['cta_button_text'] ) ? $settings['cta_button_text'] : esc_html__( 'Get in touch', 'foundation-elementor-plus' ),
			'card_size'        => $this->normalize_card_size( $settings['cta_size'] ?? 'standard' ),
			'card_theme'       => $this->normalize_card_theme( $settings['cta_theme'] ?? 'accent' ),
			'background_style' => $this->normalize_cta_background_style( $settings['cta_background_style'] ?? 'use_style' ),
			'hover_color'      => ! empty( $settings['cta_hover_color'] ) ? $settings['cta_hover_color'] : '#9af0ff',
			'is_featured' => false,
			'layout_classes' => array(),
			'url'         => $this->normalize_url_setting( $settings['cta_url'] ?? array() ),
		);

		if ( 'end' === ( $settings['cta_insert_mode'] ?? 'position' ) ) {
			$cards[] = $cta_card;
			return $cards;
		}

		$position = isset( $settings['cta_position'] ) ? max( 0, (int) $settings['cta_position'] ) : 0;
		$position = min( $position, count( $cards ) );

		array_splice( $cards, $position, 0, array( $cta_card ) );

			return $cards;
		}

	private function maybe_insert_embedded_shortcode_cards( array $cards, array $settings ) {
		$insertions = array();

		if ( 'yes' === ( $settings['show_post_footer_card'] ?? '' ) && $this->supports_post_footer_card( $cards, $settings ) ) {
			$insertions[] = array(
				'insert_mode' => ( 'position' === ( $settings['post_footer_card_insert_mode'] ?? 'end' ) ) ? 'position' : 'end',
				'position'    => isset( $settings['post_footer_card_position'] ) ? max( 0, (int) $settings['post_footer_card_position'] ) : count( $cards ),
				'card'        => array(
					'type'           => 'shortcode',
					'shortcode'      => '[ink_post_footer]',
					'card_size'      => $this->normalize_card_size( $settings['post_footer_card_size'] ?? 'feature' ),
					'hover_color'    => '#f6b59f',
					'is_featured'    => false,
					'layout_classes' => array(),
					'is_post_footer' => true,
					'filter_terms'   => array(),
				),
			);
		}

		if ( 'yes' === ( $settings['show_a11y_card'] ?? '' ) ) {
			$insertions[] = array(
				'insert_mode' => ( 'position' === ( $settings['a11y_card_insert_mode'] ?? 'end' ) ) ? 'position' : 'end',
				'position'    => isset( $settings['a11y_card_position'] ) ? max( 0, (int) $settings['a11y_card_position'] ) : count( $cards ),
				'card'        => array(
					'type'           => 'shortcode',
					'shortcode'      => '[ink_access_meta]',
					'card_size'      => $this->normalize_card_size( $settings['a11y_card_size'] ?? 'feature' ),
					'hover_color'    => '#8ce7d2',
					'is_featured'    => false,
					'layout_classes' => array(),
					'is_a11y_card'   => true,
					'filter_terms'   => array(),
				),
			);
		}

		if ( empty( $insertions ) ) {
			return $cards;
		}

		$positioned = array_values(
			array_filter(
				$insertions,
				static function( $insertion ) {
					return 'position' === ( $insertion['insert_mode'] ?? 'end' );
				}
			)
		);
		$ending = array_values(
			array_filter(
				$insertions,
				static function( $insertion ) {
					return 'position' !== ( $insertion['insert_mode'] ?? 'end' );
				}
			)
		);

		usort(
			$positioned,
			static function( $left, $right ) {
				return (int) ( $left['position'] ?? 0 ) <=> (int) ( $right['position'] ?? 0 );
			}
		);

		$result = $cards;
		$offset = 0;

		foreach ( $positioned as $insertion ) {
			$position = min( max( 0, (int) ( $insertion['position'] ?? 0 ) ), count( $cards ) );
			array_splice( $result, $position + $offset, 0, array( $insertion['card'] ) );
			$offset++;
		}

		foreach ( $ending as $insertion ) {
			$result[] = $insertion['card'];
		}

		return $result;
	}

	private function supports_post_footer_card( array $cards, array $settings ) {
		unset( $settings );

		foreach ( $cards as $card ) {
			if ( 'post' === ( $card['post_type'] ?? '' ) ) {
				return true;
			}
		}

		if ( is_search() || is_home() || is_singular( 'post' ) || is_category() || is_tag() || is_author() || is_date() ) {
			return true;
		}

		$queried_object = get_queried_object();

		if ( $queried_object instanceof \WP_Post_Type && 'post' === $queried_object->name ) {
			return true;
		}

		return false;
	}

	private function should_render_header( array $settings ) {
			if ( 'yes' !== ( $settings['show_header'] ?? '' ) ) {
			return false;
		}

		return ! empty( $settings['eyebrow'] ) || ! empty( $settings['title'] ) || ! empty( $settings['intro'] );
	}

	private function get_portfolio_headline( $post_id ) {
		$excerpt = trim( (string) get_the_excerpt( $post_id ) );

		if ( $excerpt ) {
			return $this->limit_card_text( $excerpt, 118 );
		}

		$post = get_post( $post_id );

		if ( ! $post instanceof \WP_Post ) {
			return '';
		}

		return $this->limit_card_text( wp_trim_words( wp_strip_all_tags( strip_shortcodes( $post->post_content ) ), 16 ), 118 );
	}

	private function get_portfolio_description( $post_id ) {
		$description = trim( (string) get_post_meta( $post_id, '_ink_portfolio_grid_description', true ) );

		if ( $description ) {
			return $this->limit_card_text( $description, 170 );
		}

		$post = get_post( $post_id );

		if ( ! $post instanceof \WP_Post ) {
			return '';
		}

		return $this->limit_card_text( wp_trim_words( wp_strip_all_tags( strip_shortcodes( $post->post_content ) ), 24 ), 170 );
	}

	private function limit_card_text( $text, $max_chars ) {
		$plain_text = html_entity_decode( wp_strip_all_tags( (string) $text ), ENT_QUOTES, get_bloginfo( 'charset' ) );
		$plain_text = trim( preg_replace( '/\s+/', ' ', $plain_text ) );

		if ( '' === $plain_text || mb_strlen( $plain_text ) <= $max_chars ) {
			return $plain_text;
		}

		return rtrim( wp_html_excerpt( $plain_text, $max_chars, '&hellip;' ) );
	}

	private function get_first_term_name( $post_id, $taxonomy ) {
		$terms = get_the_terms( $post_id, $taxonomy );

		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			return '';
		}

		$first = reset( $terms );

		return $first instanceof \WP_Term ? $first->name : '';
	}

	private function get_post_filter_terms( $post_id, $taxonomy ) {
		$terms = get_the_terms( $post_id, $taxonomy );

		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			return array();
		}

		$filters = array();

		foreach ( $terms as $term ) {
			if ( ! $term instanceof \WP_Term ) {
				continue;
			}

			$filters[] = array(
				'slug'  => $term->slug,
				'label' => $term->name,
			);
		}

		return $filters;
	}

	private function get_post_category_filter_terms( $post_id, array $ignored_slugs = array() ) {
		$terms = get_the_terms( $post_id, 'category' );

		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			return array();
		}

		$filters = array();

		foreach ( $terms as $term ) {
			if ( ! $term instanceof \WP_Term || in_array( $term->slug, $ignored_slugs, true ) ) {
				continue;
			}

			$filters[] = array(
				'slug'  => $term->slug,
				'label' => html_entity_decode( wp_strip_all_tags( $term->name ), ENT_QUOTES, get_bloginfo( 'charset' ) ),
			);
		}

		if ( empty( $filters ) ) {
			$filters[] = array(
				'slug'  => 'case-study',
				'label' => esc_html__( 'Article', 'foundation-elementor-plus' ),
			);
		}

		return $filters;
	}

	private function parse_slug_list( $value ) {
		return array_values(
			array_filter(
				array_map(
					'sanitize_title',
					array_map( 'trim', explode( ',', (string) $value ) )
				)
			)
		);
	}

	private function get_filter_items( array $cards ) {
		$filters = array();

		foreach ( $cards as $card ) {
			if ( empty( $card['filter_terms'] ) || ! is_array( $card['filter_terms'] ) ) {
				continue;
			}

			foreach ( $card['filter_terms'] as $term ) {
				if ( empty( $term['slug'] ) || empty( $term['label'] ) || isset( $filters[ $term['slug'] ] ) ) {
					continue;
				}

				$filters[ $term['slug'] ] = array(
					'slug'  => $term['slug'],
					'label' => $term['label'],
				);
			}
		}

		return array_values( $filters );
	}

	private function get_filter_data_attribute( array $card ) {
		if ( empty( $card['filter_terms'] ) || ! is_array( $card['filter_terms'] ) ) {
			return '';
		}

		$slugs = array_values(
			array_filter(
				array_map(
					static function( $term ) {
						return ! empty( $term['slug'] ) ? sanitize_title( $term['slug'] ) : '';
					},
					$card['filter_terms']
				)
			)
		);

		if ( empty( $slugs ) ) {
			return '';
		}

		return 'data-foundation-portfolio-filters="' . esc_attr( implode( ' ', array_unique( $slugs ) ) ) . '"';
	}

	private function get_current_portfolio_id() {
		$candidates = array(
			get_queried_object_id(),
			get_the_ID(),
		);

		if ( isset( $_GET['preview_id'] ) ) {
			$candidates[] = absint( wp_unslash( $_GET['preview_id'] ) );
		}

		foreach ( array_filter( array_map( 'absint', $candidates ) ) as $candidate ) {
			if ( 'ink_portfolio' === get_post_type( $candidate ) ) {
				return $candidate;
			}
		}

		return 0;
	}

	private function get_attachment_alt( $attachment_id, $fallback ) {
		if ( ! $attachment_id ) {
			return $fallback;
		}

		$alt = trim( (string) get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) );

		return $alt ? $alt : $fallback;
	}

	private function get_media_url( array $media_control ) {
		return ! empty( $media_control['url'] ) ? $media_control['url'] : '';
	}

	private function get_media_alt( array $media_control, $fallback ) {
		if ( ! empty( $media_control['id'] ) ) {
			$alt = trim( (string) get_post_meta( (int) $media_control['id'], '_wp_attachment_image_alt', true ) );

			if ( $alt ) {
				return $alt;
			}
		}

		return $fallback;
	}

	private function get_glass_preset_options( $include_cta_gradient = false ) {
		$options = array(
			'inherit' => esc_html__( 'Theme Default', 'foundation-elementor-plus' ),
			'dark'    => esc_html__( 'Dark Glass', 'foundation-elementor-plus' ),
			'navy'    => esc_html__( 'Navy Glass', 'foundation-elementor-plus' ),
			'green'   => esc_html__( 'Green Glass', 'foundation-elementor-plus' ),
			'white'   => esc_html__( 'White Glass', 'foundation-elementor-plus' ),
			'orange'  => esc_html__( 'Orange Glass', 'foundation-elementor-plus' ),
		);

		if ( $include_cta_gradient ) {
			$options['cta_gradient'] = esc_html__( 'Ink CTA Gradient', 'foundation-elementor-plus' );
		}

		return $options;
	}

	private function resolve_card_glass_preset( array $card, array $settings, $card_type ) {
		if ( 'cta' === $card_type ) {
			$background_style = $this->normalize_cta_background_style( $card['background_style'] ?? 'use_style' );

			if ( 'theme' === $background_style ) {
				return $this->map_card_theme_to_glass_preset( $card['card_theme'] ?? 'dark' );
			}

			if ( 'use_style' !== $background_style ) {
				return $this->normalize_glass_preset( $background_style );
			}

			$preset = $this->normalize_glass_preset( $settings['cta_glass_preset'] ?? 'inherit' );

			if ( 'inherit' !== $preset ) {
				return $preset;
			}

			return $this->map_card_theme_to_glass_preset( $card['card_theme'] ?? 'dark' );
		}

		$preset = $this->is_featured_card( $card )
			? ( $settings['feature_glass_preset'] ?? 'navy' )
			: ( $settings['standard_glass_preset'] ?? 'inherit' );

		$preset = $this->normalize_glass_preset( $preset );

		if ( 'inherit' !== $preset ) {
			return $preset;
		}

		return $this->map_card_theme_to_glass_preset( $card['card_theme'] ?? 'dark' );
	}

	private function normalize_grid_variant( $value ) {
		return 'compact' === $value ? 'compact' : 'showcase';
	}

	private function normalize_showcase_layout_mode( $value ) {
		return 'editorial' === $value ? 'editorial' : 'flexible';
	}

	private function normalize_column_mode( $value ) {
		return in_array( (string) $value, array( 'auto', '2', '3', '4', '5' ), true ) ? (string) $value : '4';
	}

	private function normalize_feature_span_min_columns( $value ) {
		return in_array( (string) $value, array( '3', '4', '5' ), true ) ? (string) $value : '4';
	}

	private function normalize_feature_layout( $value ) {
		return in_array( (string) $value, array( 'wide', 'large' ), true ) ? (string) $value : 'wide';
	}

	private function normalize_glass_preset( $value ) {
		return in_array( (string) $value, array( 'inherit', 'dark', 'navy', 'green', 'white', 'orange', 'cta_gradient' ), true ) ? (string) $value : 'inherit';
	}

	private function normalize_cta_background_style( $value ) {
		return in_array( (string) $value, array( 'use_style', 'theme', 'dark', 'navy', 'green', 'white', 'orange', 'cta_gradient' ), true ) ? (string) $value : 'use_style';
	}

	private function map_card_theme_to_glass_preset( $theme ) {
		switch ( $theme ) {
			case 'light':
				return 'white';
			case 'accent':
				return 'green';
			case 'dark':
			default:
				return 'dark';
		}
	}

	private function normalize_card_size( $value ) {
		return 'feature' === $value ? 'feature' : 'standard';
	}

	private function is_featured_card( array $card ) {
		if ( array_key_exists( 'is_featured', $card ) ) {
			return ! empty( $card['is_featured'] );
		}

		return 'feature' === ( $card['card_size'] ?? 'standard' );
	}

	private function is_post_footer_card( array $card ) {
		return 'shortcode' === ( $card['type'] ?? '' ) && ! empty( $card['is_post_footer'] );
	}

	private function is_a11y_card( array $card ) {
		return 'shortcode' === ( $card['type'] ?? '' ) && ! empty( $card['is_a11y_card'] );
	}

	private function is_support_shortcode_card( array $card ) {
		return 'shortcode' === ( $card['type'] ?? '' ) && ( $this->is_post_footer_card( $card ) || $this->is_a11y_card( $card ) );
	}

	private function apply_layout_pattern( array $cards, array $settings ) {
		if ( 'showcase' !== $this->normalize_grid_variant( $settings['grid_variant'] ?? 'showcase' ) ) {
			return $cards;
		}

		if ( 'editorial' !== $this->normalize_showcase_layout_mode( $settings['showcase_layout_mode'] ?? 'flexible' ) ) {
			return $cards;
		}

		foreach ( $cards as $index => $card ) {
			$block_index = (int) floor( $index / 6 );
			$slot_index  = $index % 6;
			$is_mirrored = 1 === ( $block_index % 2 );
			$slot_class  = '';
			$is_featured = false;
			$is_support_shortcode = $this->is_support_shortcode_card( $card );

			if ( $is_mirrored ) {
				switch ( $slot_index ) {
					case 0:
						$slot_class = $is_support_shortcode ? 'is-pattern-support-feature-left' : 'is-pattern-stack-top-left';
						break;
					case 1:
						$slot_class = 'is-pattern-stack-bottom-left';
						break;
					case 2:
						$slot_class = 'is-pattern-feature-right';
						$is_featured = true;
						break;
					default:
						$slot_class = 'is-pattern-row';
						break;
				}
			} else {
				switch ( $slot_index ) {
					case 0:
						$slot_class = 'is-pattern-feature-left';
						$is_featured = true;
						break;
					case 1:
						$slot_class = $is_support_shortcode ? 'is-pattern-support-feature-right' : 'is-pattern-stack-top-right';
						break;
					case 2:
						$slot_class = 'is-pattern-row';
						break;
					default:
						$slot_class = 'is-pattern-row';
						break;
				}
			}

			$cards[ $index ]['layout_classes'] = array_values(
				array_filter(
					array_merge(
						(array) ( $card['layout_classes'] ?? array() ),
						array( $slot_class )
					)
				)
			);
			$cards[ $index ]['is_featured']   = $is_featured || ( 'cta' === ( $card['type'] ?? 'portfolio' ) && 'feature' === ( $card['card_size'] ?? 'standard' ) );
		}

		return $cards;
	}

	private function normalize_card_theme( $value ) {
		if ( in_array( $value, array( 'light', 'accent', 'dark' ), true ) ) {
			return $value;
		}

		return 'dark';
	}

	private function normalize_url_setting( array $url_setting ) {
		return array(
			'url'         => ! empty( $url_setting['url'] ) ? $url_setting['url'] : '',
			'is_external' => ! empty( $url_setting['is_external'] ),
			'nofollow'    => ! empty( $url_setting['nofollow'] ),
		);
	}

	private function get_link_attribute_string( array $url ) {
		$attributes = array();
		$rel        = array();

		if ( ! empty( $url['is_external'] ) ) {
			$attributes[] = 'target="_blank"';
			$rel[]        = 'noopener';
		}

		if ( ! empty( $url['nofollow'] ) ) {
			$rel[] = 'nofollow';
		}

		if ( ! empty( $rel ) ) {
			$attributes[] = 'rel="' . esc_attr( implode( ' ', array_unique( $rel ) ) ) . '"';
		}

		return $attributes ? ' ' . implode( ' ', $attributes ) : '';
	}

	private function get_title_markup( $title, $highlight_text ) {
		$title = (string) $title;

		// If the editor is intentionally using inline markup (for example a Font Awesome icon),
		// preserve a safe subset instead of forcing it to plain text.
		if ( false !== strpos( $title, '<' ) ) {
			return nl2br( wp_kses( $title, $this->get_inline_text_allowed_html() ) );
		}

		$title = esc_html( $title );

		if ( ! $highlight_text ) {
			return nl2br( $title );
		}

		$highlight_text = esc_html( $highlight_text );
		$replacement    = '<span class="foundation-portfolio-mosaic__highlight">' . $highlight_text . '</span>';
		$pattern        = '/' . preg_quote( $highlight_text, '/' ) . '/';

		return nl2br( preg_replace( $pattern, $replacement, $title, 1 ) ?: $title );
	}

	private function get_inline_text_allowed_html() {
		return array(
			'br'     => array(),
			'em'     => array(),
			'strong' => array(),
			'span'   => array(
				'class'       => true,
				'aria-hidden' => true,
			),
			'i'      => array(
				'class'       => true,
				'aria-hidden' => true,
				'title'       => true,
			),
		);
	}

	private function get_card_link_label( array $card ) {
		$post_type = ! empty( $card['post_type'] ) ? (string) $card['post_type'] : '';

		switch ( $post_type ) {
			case 'ink_portfolio':
				$default_label = esc_html__( 'View portfolio project', 'foundation-elementor-plus' );
				break;
			case 'post':
				$default_label = esc_html__( 'Read article', 'foundation-elementor-plus' );
				break;
			case 'page':
				$default_label = esc_html__( 'View page', 'foundation-elementor-plus' );
				break;
			default:
				$dynamic_label = $this->get_dynamic_item_label( $post_type );
				$default_label = sprintf(
					/* translators: %s is a generic item label. */
					esc_html__( 'View %s', 'foundation-elementor-plus' ),
					$dynamic_label
				);
				break;
		}

		if ( empty( $card['project_name'] ) ) {
			return $default_label;
		}

		return sprintf(
			/* translators: %s is the item name. */
			esc_html__( '%1$s: %2$s', 'foundation-elementor-plus' ),
			$default_label,
			$card['project_name']
		);
	}

	private function get_cta_link_label( array $card ) {
		if ( empty( $card['headline'] ) ) {
			return esc_html__( 'Open call to action', 'foundation-elementor-plus' );
		}

		return sprintf(
			/* translators: %s is the CTA title. */
			esc_html__( 'Open call to action: %s', 'foundation-elementor-plus' ),
			wp_strip_all_tags( $card['headline'] )
		);
	}

	private function get_icon_markup( $icon, $class_name ) {
		if ( empty( $icon['value'] ) ) {
			return '';
		}

		ob_start();
		Icons_Manager::render_icon(
			$icon,
			array(
				'aria-hidden' => 'true',
				'class'       => $class_name,
			)
		);

		return (string) ob_get_clean();
	}

	private function get_default_manual_cards() {
		return array(
			array(
				'project_name'  => 'Yaspa',
				'category_pill' => 'Web Design',
				'headline'      => 'Bold web design for a fast-scaling global payments fintech',
				'card_size'     => 'feature',
				'card_theme'    => 'dark',
				'hover_color'   => '#7359ff',
			),
			array(
				'project_name'  => 'Penny Black Media',
				'category_pill' => 'Branding',
				'headline'      => 'A cinematic new website for a world-class film catalogue',
				'card_size'     => 'standard',
				'card_theme'    => 'dark',
				'hover_color'   => '#d11d66',
			),
			array(
				'project_name'  => 'Finest Properties',
				'category_pill' => 'Marketing',
				'badge_text'    => 'Lite',
				'headline'      => 'Luxury property marketing with sophistication',
				'card_size'     => 'standard',
				'card_theme'    => 'light',
				'hover_color'   => '#604331',
			),
		);
	}
}
