<?php

namespace FoundationElementorPlus\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Utils;
use FoundationElementorPlus\Widgets\Base_Widget;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Selector_Stack_Widget extends Base_Widget {
	public function get_name() {
		return 'foundation-selector-stack';
	}

	public function get_title() {
		return esc_html__( 'Selector Stack', 'foundation-elementor-plus' );
	}

	public function get_icon() {
		return 'eicon-nested-carousel';
	}

	public function get_categories() {
		return array( \FoundationElementorPlus\Plugin::CATEGORY_SLUG );
	}

	public function get_keywords() {
		return array( 'foundation', 'selector', 'stack', 'cards', 'services' );
	}

	public function get_style_depends(): array {
		return $this->get_foundation_style_depends( array( 'foundation-elementor-plus-selector-stack' ) );
	}

	public function get_script_depends(): array {
		return array( 'foundation-elementor-plus-selector-stack' );
	}

	protected function register_controls() {
		$this->register_content_controls();
		$this->register_layout_controls();
		$this->register_nav_style_controls();
		$this->register_card_style_controls();
		$this->register_content_style_controls();
		$this->register_image_style_controls();
		$this->register_accessibility_controls();
	}

	protected function render() {
		$settings         = $this->get_settings_for_display();
		$cards            = ! empty( $settings['cards'] ) && is_array( $settings['cards'] ) ? array_values( $settings['cards'] ) : array();
		$widget_id        = 'foundation-selector-' . $this->get_id();
		$layer_step_value = isset( $settings['layer_step']['size'] ) ? (float) $settings['layer_step']['size'] : 18;
		$layer_step_unit  = isset( $settings['layer_step']['unit'] ) && '' !== (string) $settings['layer_step']['unit'] ? (string) $settings['layer_step']['unit'] : 'px';
		$total            = count( $cards );
		$root_style       = sprintf(
			'--inkfire-selector-layer-step:%1$s%2$s;--inkfire-selector-card-count:%3$d;',
			rtrim( rtrim( sprintf( '%.4F', $layer_step_value ), '0' ), '.' ),
			esc_attr( $layer_step_unit ),
			$total
		);

		if ( empty( $cards ) ) {
			return;
		}
		?>
		<section <?php echo $this->get_widget_root_attributes( $settings, array( 'id' => $widget_id, 'class' => 'inkfire-selector', 'data-inkfire-selector' => true, 'style' => $root_style ) ); ?>>
			<div class="inkfire-selector__layout">
				<div class="inkfire-selector__nav-rail">
					<nav class="inkfire-selector__nav" aria-label="<?php esc_attr_e( 'Selector navigation', 'foundation-elementor-plus' ); ?>">
						<?php foreach ( $cards as $index => $card ) : ?>
							<?php
							$card_title = ! empty( $card['title'] ) ? $card['title'] : sprintf( 'Card %d', $index + 1 );
							$nav_label  = ! empty( $card['nav_label'] ) ? $card['nav_label'] : $card_title;
							?>
							<a href="#<?php echo esc_attr( $widget_id . '-card-' . ( $index + 1 ) ); ?>" class="<?php echo 0 === $index ? 'is-active' : ''; ?>">
								<?php echo esc_html( $nav_label ); ?>
							</a>
						<?php endforeach; ?>
					</nav>
				</div>

				<div class="inkfire-selector__deck">
					<div class="inkfire-selector__stage">
						<?php foreach ( $cards as $index => $card ) : ?>
							<?php
							$card_title  = ! empty( $card['title'] ) ? $card['title'] : sprintf( 'Card %d', $index + 1 );
							$description = ! empty( $card['description'] ) ? $card['description'] : '';
							$card_style  = sprintf(
								'--inkfire-selector-card-index:%1$d;z-index:%2$d;',
								$index,
								$index + 1
							);
							?>
							<article id="<?php echo esc_attr( $widget_id . '-card-' . ( $index + 1 ) ); ?>" class="inkfire-selector__card" data-card-index="<?php echo esc_attr( (string) $index ); ?>" style="<?php echo esc_attr( $card_style ); ?>">
								<div class="inkfire-selector__media">
									<?php $this->render_media( $card, 0 === $index ); ?>
								</div>
								<div class="inkfire-selector__content">
									<h3><?php echo esc_html( $card_title ); ?></h3>
									<div class="inkfire-selector__description">
										<?php echo wp_kses_post( wpautop( $description ) ); ?>
									</div>
								</div>
							</article>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</section>
		<?php
	}

	private function register_content_controls() {
		$this->start_controls_section(
			'section_content',
			array(
				'label' => esc_html__( 'Cards', 'foundation-elementor-plus' ),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'nav_label',
			array(
				'label'       => esc_html__( 'Nav Label', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Card', 'foundation-elementor-plus' ),
				'label_block' => true,
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$repeater->add_control(
			'title',
			array(
				'label'       => esc_html__( 'Title', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Card Title', 'foundation-elementor-plus' ),
				'label_block' => true,
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$repeater->add_control(
			'description',
			array(
				'label'   => esc_html__( 'Description', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 6,
				'default' => esc_html__( 'Add a short description for this card.', 'foundation-elementor-plus' ),
				'dynamic' => array(
					'active' => true,
				),
			)
		);

		$repeater->add_control(
			'media_type',
			array(
				'label'   => esc_html__( 'Media Type', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'image',
				'options' => array(
					'image'     => esc_html__( 'Image / GIF', 'foundation-elementor-plus' ),
					'video'     => esc_html__( 'Video', 'foundation-elementor-plus' ),
					'animation' => esc_html__( 'SVG / Animated Asset', 'foundation-elementor-plus' ),
				),
			)
		);

		$repeater->add_control(
			'image',
			array(
				'label'   => esc_html__( 'Image / GIF', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'condition' => array(
					'media_type' => 'image',
				),
				'dynamic' => array(
					'active' => true,
				),
			)
		);

		$repeater->add_control(
			'video_url',
			array(
				'label'       => esc_html__( 'Video URL', 'foundation-elementor-plus' ),
				'description' => esc_html__( 'Use a direct MP4 or WebM URL.', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://example.com/video.mp4',
				'condition'   => array(
					'media_type' => 'video',
				),
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$repeater->add_control(
			'video_poster',
			array(
				'label'     => esc_html__( 'Video Poster', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::MEDIA,
				'condition' => array(
					'media_type' => 'video',
				),
				'dynamic'   => array(
					'active' => true,
				),
			)
		);

		$repeater->add_control(
			'animation_url',
			array(
				'label'       => esc_html__( 'SVG / Animation URL', 'foundation-elementor-plus' ),
				'description' => esc_html__( 'Use a direct SVG, GIF, or animated asset URL.', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://example.com/animation.svg',
				'condition'   => array(
					'media_type' => 'animation',
				),
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
			'cards',
			array(
				'label'       => esc_html__( 'Cards', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ nav_label }}}',
				'default'     => $this->get_default_cards(),
			)
		);

		$this->end_controls_section();
	}

	private function register_layout_controls() {
		$this->start_controls_section(
			'section_stack_motion',
			array(
				'label' => esc_html__( 'Stack Motion', 'foundation-elementor-plus' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'sticky_top',
			array(
				'label'       => esc_html__( 'Stack Start Position', 'foundation-elementor-plus' ),
				'description' => esc_html__( 'How far from the top the active card should lock into place.', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'rem', 'vh', 'vw', '%' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 120,
				),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 240,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .inkfire-selector' => '--inkfire-selector-sticky-top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'nav_top',
			array(
				'label'       => esc_html__( 'Anchor Menu Position', 'foundation-elementor-plus' ),
				'description' => esc_html__( 'How far from the top the left anchor menu should stick.', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'rem', 'vh', 'vw', '%' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 120,
				),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 240,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .inkfire-selector' => '--inkfire-selector-nav-top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'peek_height',
			array(
				'label'       => esc_html__( 'Visible Peek of Next Card', 'foundation-elementor-plus' ),
				'description' => esc_html__( 'How much of the next card should be visible before it stacks into place.', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'rem', 'vh', 'vw', '%' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 132,
				),
				'range'      => array(
					'px' => array(
						'min' => 20,
						'max' => 260,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .inkfire-selector' => '--inkfire-selector-peek: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'stack_gap',
			array(
				'label'       => esc_html__( 'Gap Between Stacked Cards', 'foundation-elementor-plus' ),
				'description' => esc_html__( 'Adds breathing room between each stacked card edge.', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'rem', 'em', 'vw', '%' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 20,
				),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 80,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .inkfire-selector' => '--inkfire-selector-stack-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'layer_step',
			array(
				'label'       => esc_html__( 'Vertical Offset Per Card', 'foundation-elementor-plus' ),
				'description' => esc_html__( 'Creates the stepped card-stack look as cards layer upward.', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'rem', 'em' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 18,
				),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 80,
					),
				),
			)
		);

		$this->add_responsive_control(
			'exit_space',
			array(
				'label'       => esc_html__( 'Space After Final Card', 'foundation-elementor-plus' ),
				'description' => esc_html__( 'Extra scroll room after the final card has stacked.', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'rem', 'vh', 'vw', '%' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 260,
				),
				'range'      => array(
					'px' => array(
						'min' => 80,
						'max' => 640,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .inkfire-selector' => '--inkfire-selector-exit-space: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_card_layout',
			array(
				'label' => esc_html__( 'Card Layout', 'foundation-elementor-plus' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'nav_width',
			array(
				'label'       => esc_html__( 'Anchor Menu Width', 'foundation-elementor-plus' ),
				'description' => esc_html__( 'Controls how much space the left menu column uses.', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'rem', 'vw', '%' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 210,
				),
				'range'      => array(
					'px' => array(
						'min' => 120,
						'max' => 320,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .inkfire-selector' => '--inkfire-selector-nav-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'layout_gap',
			array(
				'label'       => esc_html__( 'Gap Between Menu and Cards', 'foundation-elementor-plus' ),
				'description' => esc_html__( 'Horizontal space between the anchor menu and the card stack.', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'rem', 'vw', '%' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 48,
				),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 120,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .inkfire-selector' => '--inkfire-selector-layout-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'card_height',
			array(
				'label'       => esc_html__( 'Card Height', 'foundation-elementor-plus' ),
				'description' => esc_html__( 'Sets a fixed card height. You can set different values for desktop, tablet, and mobile.', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'rem', 'vh', 'vw', '%' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 620,
				),
				'range'      => array(
					'px' => array(
						'min' => 380,
						'max' => 920,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .inkfire-selector' => '--inkfire-selector-card-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'media_width',
			array(
				'label'       => esc_html__( 'Image / Content Split', 'foundation-elementor-plus' ),
				'description' => esc_html__( 'Controls how much width the image side takes compared to the text side.', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%' ),
				'default'    => array(
					'unit' => '%',
					'size' => 52,
				),
				'range'      => array(
					'%' => array(
						'min' => 30,
						'max' => 70,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .inkfire-selector' => '--inkfire-selector-media-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'card_radius',
			array(
				'label'       => esc_html__( 'Card Corner Radius', 'foundation-elementor-plus' ),
				'description' => esc_html__( 'Rounds the outside corners of each card.', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'rem', '%' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 100,
				),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 160,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .inkfire-selector' => '--inkfire-selector-card-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'content_padding',
			array(
				'label'       => esc_html__( 'Card Content Padding', 'foundation-elementor-plus' ),
				'description' => esc_html__( 'Inner spacing around the title and description area.', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ),
				'default'    => array(
					'top'      => 70,
					'right'    => 70,
					'bottom'   => 70,
					'left'     => 70,
					'unit'     => 'px',
					'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} .inkfire-selector__content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_mobile_layout',
			array(
				'label' => esc_html__( 'Mobile Layout', 'foundation-elementor-plus' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'mobile_image_height',
			array(
				'label'       => esc_html__( 'Stacked Image Height', 'foundation-elementor-plus' ),
				'description' => esc_html__( 'Height of the image when the layout collapses into a single column.', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'rem', 'vh', 'vw', '%' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 260,
				),
				'range'      => array(
					'px' => array(
						'min' => 160,
						'max' => 520,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .inkfire-selector' => '--inkfire-selector-mobile-image-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function register_nav_style_controls() {
		$this->start_controls_section(
			'section_nav_style',
			array(
				'label' => esc_html__( 'Navigation', 'foundation-elementor-plus' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'nav_text_color',
			array(
				'label'     => esc_html__( 'Text Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => array(
					'{{WRAPPER}} .inkfire-selector__nav a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'nav_hover_color',
			array(
				'label'     => esc_html__( 'Hover Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => array(
					'{{WRAPPER}} .inkfire-selector__nav a:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'nav_active_color',
			array(
				'label'     => esc_html__( 'Active Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FBCCBF',
				'selectors' => array(
					'{{WRAPPER}} .inkfire-selector__nav a.is-active' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'nav_marker_color',
			array(
				'label'     => esc_html__( 'Marker Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FBCCBF',
				'selectors' => array(
					'{{WRAPPER}} .inkfire-selector__nav a::before' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'nav_gap',
			array(
				'label'      => esc_html__( 'Nav Item Gap', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'rem', 'em', 'vw' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 18,
				),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 60,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .inkfire-selector__nav' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'nav_typography',
				'selector' => '{{WRAPPER}} .inkfire-selector__nav a',
			)
		);

		$this->end_controls_section();
	}

	private function register_card_style_controls() {
		$this->start_controls_section(
			'section_card_style',
			array(
				'label' => esc_html__( 'Cards', 'foundation-elementor-plus' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'card_bg_start',
			array(
				'label'     => esc_html__( 'Background Start', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#1A1C29',
				'selectors' => array(
					'{{WRAPPER}} .inkfire-selector' => '--inkfire-selector-bg-start: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'card_bg_end',
			array(
				'label'     => esc_html__( 'Background End', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#151622',
				'selectors' => array(
					'{{WRAPPER}} .inkfire-selector' => '--inkfire-selector-bg-end: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'card_accent_one',
			array(
				'label'     => esc_html__( 'Accent Glow One', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(251, 204, 191, 0.08)',
				'selectors' => array(
					'{{WRAPPER}} .inkfire-selector' => '--inkfire-selector-accent-one: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'card_accent_two',
			array(
				'label'     => esc_html__( 'Accent Glow Two', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(7, 160, 121, 0.12)',
				'selectors' => array(
					'{{WRAPPER}} .inkfire-selector' => '--inkfire-selector-accent-two: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'card_border',
				'selector' => '{{WRAPPER}} .inkfire-selector__card',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'card_shadow',
				'selector' => '{{WRAPPER}} .inkfire-selector__card',
			)
		);

		$this->add_responsive_control(
			'card_backdrop_blur',
			array(
				'label'      => esc_html__( 'Backdrop Blur', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 40,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 0,
				),
				'selectors'  => array(
					'{{WRAPPER}} .inkfire-selector__card' => '-webkit-backdrop-filter: blur({{SIZE}}{{UNIT}}); backdrop-filter: blur({{SIZE}}{{UNIT}});',
				),
			)
		);

		$this->end_controls_section();
	}

	private function register_content_style_controls() {
		$this->start_controls_section(
			'section_content_style',
			array(
				'label' => esc_html__( 'Content', 'foundation-elementor-plus' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => esc_html__( 'Title Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => array(
					'{{WRAPPER}} .inkfire-selector__content h3' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .inkfire-selector__content h3',
			)
		);

		$this->add_responsive_control(
			'title_spacing',
			array(
				'label'      => esc_html__( 'Title Spacing', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'rem', 'em', 'vw' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 20,
				),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 80,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .inkfire-selector__content h3' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'description_color',
			array(
				'label'     => esc_html__( 'Description Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#A4A9BD',
				'selectors' => array(
					'{{WRAPPER}} .inkfire-selector__description, {{WRAPPER}} .inkfire-selector__description p' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'description_typography',
				'selector' => '{{WRAPPER}} .inkfire-selector__description, {{WRAPPER}} .inkfire-selector__description p',
			)
		);

		$this->add_responsive_control(
			'text_align',
			array(
				'label'     => esc_html__( 'Text Align', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left' => array(
						'title' => esc_html__( 'Left', 'foundation-elementor-plus' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'foundation-elementor-plus' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right' => array(
						'title' => esc_html__( 'Right', 'foundation-elementor-plus' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => 'left',
				'selectors' => array(
					'{{WRAPPER}} .inkfire-selector__content' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'description_width',
			array(
				'label'      => esc_html__( 'Description Width', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'ch', '%', 'vw', 'rem' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 620,
				),
				'range'      => array(
					'px' => array(
						'min' => 240,
						'max' => 920,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .inkfire-selector__description, {{WRAPPER}} .inkfire-selector__description > *' => 'max-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function register_image_style_controls() {
		$this->start_controls_section(
			'section_image_style',
			array(
				'label' => esc_html__( 'Image', 'foundation-elementor-plus' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'image_overlay_color',
			array(
				'label'     => esc_html__( 'Overlay Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(21, 22, 34, 0.42)',
				'selectors' => array(
					'{{WRAPPER}} .inkfire-selector' => '--inkfire-selector-media-overlay: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'image_hover_scale',
			array(
				'label'     => esc_html__( 'Hover Scale', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 1,
				'max'       => 1.2,
				'step'      => 0.01,
				'default'   => 1.05,
				'selectors' => array(
					'{{WRAPPER}} .inkfire-selector' => '--inkfire-selector-image-hover-scale: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function get_default_cards() {
		return array(
			array(
				'nav_label'   => 'Radical Accessibility',
				'title'       => 'Radical Accessibility',
				'description' => "We believe the web should be for everyone. Our designs don't just meet WCAG compliance; they set new standards for inclusive, barrier-free digital experiences that work for every user.",
				'image'       => array(
					'url' => 'https://images.unsplash.com/photo-1573164713988-8665fc963095?q=80&w=1600',
				),
			),
			array(
				'nav_label'   => 'Bold Branding',
				'title'       => 'Bold Branding',
				'description' => "We don't do quiet. We build striking, memorable brand identities that break the rules, challenge industry norms, and demand attention in crowded digital spaces.",
				'image'       => array(
					'url' => 'https://images.unsplash.com/photo-1561070791-2526d30994b5?q=80&w=1600',
				),
			),
			array(
				'nav_label'   => 'Bespoke Engineering',
				'title'       => 'Bespoke Engineering',
				'description' => 'No off-the-shelf templates. We engineer lightweight, high-performance platforms from the ground up to perfectly match your technical requirements and scale with your ambition.',
				'image'       => array(
					'url' => 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?q=80&w=1600',
				),
			),
			array(
				'nav_label'   => 'Relentless Support',
				'title'       => 'Relentless Support',
				'description' => 'Launching is just the beginning. We provide accountable, long-term partnerships with transparent reporting, proactive maintenance, and continuous technical evolution.',
				'image'       => array(
					'url' => 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?q=80&w=1600',
				),
			),
		);
	}

	private function get_image_url( array $card ) {
		if ( empty( $card['image'] ) || ! is_array( $card['image'] ) ) {
			return '';
		}

		if ( ! empty( $card['image']['id'] ) ) {
			$image_url = wp_get_attachment_image_url( (int) $card['image']['id'], 'full' );
			if ( $image_url ) {
				return $image_url;
			}
		}

		return ! empty( $card['image']['url'] ) ? (string) $card['image']['url'] : '';
	}

	private function get_image_alt( array $card ) {
		if ( ! empty( $card['image']['id'] ) ) {
			$alt = get_post_meta( (int) $card['image']['id'], '_wp_attachment_image_alt', true );
			if ( is_string( $alt ) && '' !== $alt ) {
				return $alt;
			}
		}

		return ! empty( $card['title'] ) ? (string) $card['title'] : '';
	}

	private function get_card_media_type( array $card ) {
		$media_type = ! empty( $card['media_type'] ) ? (string) $card['media_type'] : 'image';

		return in_array( $media_type, array( 'image', 'video', 'animation' ), true ) ? $media_type : 'image';
	}

	private function get_url_control_value( array $card, string $key ) {
		if ( empty( $card[ $key ] ) ) {
			return '';
		}

		if ( is_array( $card[ $key ] ) && ! empty( $card[ $key ]['url'] ) ) {
			return (string) $card[ $key ]['url'];
		}

		if ( is_string( $card[ $key ] ) ) {
			return (string) $card[ $key ];
		}

		return '';
	}

	private function render_media( array $card, bool $is_first ) {
		$media_type = $this->get_card_media_type( $card );
		$loading    = $is_first ? 'eager' : 'lazy';

		if ( 'video' === $media_type ) {
			$video_url  = $this->get_url_control_value( $card, 'video_url' );
			$poster_url = $this->get_image_url(
				array(
					'image' => ! empty( $card['video_poster'] ) && is_array( $card['video_poster'] ) ? $card['video_poster'] : array(),
				)
			);

			if ( $video_url ) {
				?>
				<video class="inkfire-selector__media-asset" playsinline autoplay muted loop preload="metadata" <?php echo $poster_url ? 'poster="' . esc_url( $poster_url ) . '"' : ''; ?>>
					<source src="<?php echo esc_url( $video_url ); ?>">
				</video>
				<?php
				return;
			}
		}

		if ( 'animation' === $media_type ) {
			$animation_url = $this->get_url_control_value( $card, 'animation_url' );

			if ( $animation_url ) {
				?>
				<img class="inkfire-selector__media-asset" src="<?php echo esc_url( $animation_url ); ?>" alt="<?php echo esc_attr( $this->get_image_alt( $card ) ); ?>" loading="<?php echo esc_attr( $loading ); ?>">
				<?php
				return;
			}
		}

		$image_url = $this->get_image_url( $card );

		if ( $image_url ) {
			?>
			<img class="inkfire-selector__media-asset" src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $this->get_image_alt( $card ) ); ?>" loading="<?php echo esc_attr( $loading ); ?>">
			<?php
		}
	}
}
